<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Services\Grading\GradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AnswerController extends Controller
{
    public function __construct(private readonly GradingService $grader)
    {
    }

    /**
     * カテゴリ内の全問題に対する一括回答フォームを表示する。
     * 1カテゴリは最大10問までしか作れない(QuestionController参照)ので、
     * 問題を選ばせず「このカテゴリの全問に回答する」という単純な方式にしている。
     */
    public function create(Request $request, Category $category): View|RedirectResponse
    {
        $questions = $category->questions()->where('user_id', $request->user()->id)->get();

        if ($questions->isEmpty()) {
            return redirect()->route('categories.show', $category)->with('status', 'このカテゴリにはまだ問題がありません。');
        }

        return view('answers.create', compact('category', 'questions'));
    }

    /**
     * カテゴリ内の全問題の回答をまとめて保存し、その場で採点して結果を表示する。
     * 採点自体は1回のGradingService呼び出しにまとめて渡す(要件定義: 1リクエストで一括採点)。
     */
    public function store(Request $request, Category $category): View|RedirectResponse
    {
        $data = $request->validate([
            'grading_level' => ['required', 'in:easy,normal,hard'],
            'answers' => ['required', 'array', 'min:1', 'max:10'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.body' => ['required', 'string'],
        ]);

        $user = $request->user();

        // 送られてきた問題IDが、本当に自分の・このカテゴリの問題かを確認する
        // (他人の問題や他カテゴリの問題が混ざっていても弾く)
        $questionIds = collect($data['answers'])->pluck('question_id')->unique();
        $questions = $category->questions()
            ->whereIn('id', $questionIds)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('id');

        abort_unless($questions->count() === $questionIds->count(), 403);

        // AIに渡す材料をまとめてから、1回のリクエストでまとめて採点してもらう
        $items = collect($data['answers'])
            ->map(fn (array $a) => [
                'question' => $questions[$a['question_id']],
                'body' => $a['body'],
            ])
            ->all();

        // Claude APIの通信エラーやキー未設定など、外部サービス起因の失敗は普通に起こりうるので、
        // 500エラーにせず入力内容を保持したままフォームへ差し戻す。
        try {
            $results = $this->grader->grade($items, $data['grading_level']);
        } catch (Throwable $e) {
            Log::error('AI採点に失敗しました', ['exception' => $e]);

            return back()->withInput()->withErrors(['grading' => 'AI採点でエラーが発生しました。しばらくしてから再度お試しください。']);
        }

        // この「1回分の全問回答」をまとめるAttemptを先に作り、各Answerをそこにぶら下げる
        // (履歴画面でUdemyのクイズ結果のように挑戦単位で一覧・詳細表示するため)
        $attempt = $category->attempts()->create([
            'user_id' => $user->id,
            'grading_level' => $data['grading_level'],
        ]);

        $answers = collect();

        foreach ($data['answers'] as $i => $a) {
            $question = $questions[$a['question_id']];

            $answer = $question->answers()->create([
                'user_id' => $user->id,
                'attempt_id' => $attempt->id,
                'body' => $a['body'],
            ]);

            $answer->score()->create([
                'score' => $results[$i]['score'],
                'feedback' => $results[$i]['feedback'],
            ]);

            $this->pruneOldAnswers($question, $user);

            // attemptは作成済みのものをそのまま使い回す(answers.resultビューが参照するため)
            $answer->setRelation('attempt', $attempt);
            $answers->push($answer->load('score', 'question'));
        }

        return view('answers.result', compact('category', 'answers'));
    }

    /**
     * 同じ問題への解答は直近10件だけ残し、それより古いものは削除する(要件定義: 履歴の自動整理)。
     * PostgreSQLはDELETE文にLIMITを使えないので、先に消す対象のIDを絞り込んでから削除する。
     * 削除の結果、回答が1件も残らなくなった挑戦(Attempt)は挑戦自体も削除して履歴から消す
     * (カテゴリの全問を毎回一括回答する仕様上、古い挑戦の回答は全問題でほぼ同時にこの上限へ達する)。
     */
    private function pruneOldAnswers(Question $question, User $user): void
    {
        $staleIds = $question->answers()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('id')
            ->slice(10);

        if ($staleIds->isEmpty()) {
            return;
        }

        $staleAttemptIds = Answer::whereIn('id', $staleIds)->pluck('attempt_id')->unique();

        Answer::whereIn('id', $staleIds)->delete();

        Attempt::whereIn('id', $staleAttemptIds)->whereDoesntHave('answers')->delete();
    }
}
