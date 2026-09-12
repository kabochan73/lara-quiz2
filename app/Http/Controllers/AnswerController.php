<?php

namespace App\Http\Controllers;

use App\Models\Answer;
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
     * 選択した問題(1〜10問)に対する一括回答フォームを表示する。
     * カテゴリ詳細ページの「回答する」ボタンから ?ids[]=1&ids[]=2... という形で遷移してくる。
     * 要件定義どおり、回答は同じカテゴリ内の問題だけで完結させる(カテゴリをまたいだ回答はしない)。
     */
    public function create(Request $request, Category $category): View|RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:10'],
            'ids.*' => ['integer'],
        ]);

        // 自分の問題、かつこのカテゴリに属する問題だけに絞り込む
        $questions = $category->questions()
            ->whereIn('id', $data['ids'])
            ->where('user_id', $request->user()->id)
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('categories.show', $category)->with('status', '回答する問題が選択されていません。');
        }

        return view('answers.create', compact('category', 'questions'));
    }

    /**
     * 選んだ全問題の回答をまとめて保存し、その場で採点して結果を表示する。
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

        $answers = collect();

        foreach ($data['answers'] as $i => $a) {
            $question = $questions[$a['question_id']];

            $answer = $question->answers()->create([
                'user_id' => $user->id,
                'body' => $a['body'],
            ]);

            $answer->score()->create([
                'score' => $results[$i]['score'],
                'feedback' => $results[$i]['feedback'],
                'grading_level' => $data['grading_level'],
            ]);

            $this->pruneOldAnswers($question, $user);

            $answers->push($answer->load('score', 'question'));
        }

        return view('answers.result', compact('category', 'answers'));
    }

    /**
     * 同じ問題への解答は直近10件だけ残し、それより古いものは削除する(要件定義: 履歴の自動整理)。
     * PostgreSQLはDELETE文にLIMITを使えないので、先に消す対象のIDを絞り込んでから削除する。
     */
    private function pruneOldAnswers(Question $question, User $user): void
    {
        $staleIds = $question->answers()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->pluck('id')
            ->slice(10);

        if ($staleIds->isNotEmpty()) {
            Answer::whereIn('id', $staleIds)->delete();
        }
    }
}
