<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * セクションの回答履歴を、Udemyのクイズ結果のように「1回の全問回答(Attempt)」単位で一覧表示する。
     */
    public function index(Request $request, Section $section): View
    {
        $attempts = $section->attempts()
            ->where('user_id', $request->user()->id)
            ->with('answers.score')
            ->latest()
            ->get();

        return view('history.index', compact('section', 'attempts'));
    }

    /**
     * 1回分の挑戦の詳細。採点直後の結果画面(answers.result)と表示内容が同じなので、
     * そのビューをそのまま再利用する。
     */
    public function show(Request $request, Section $section, Attempt $attempt): View
    {
        abort_unless(
            $attempt->section_id === $section->id && $attempt->user_id === $request->user()->id,
            404
        );

        // attemptはすでに手元にあるので、改めてクエリを投げずに各Answerへセットしておく
        // (answers.resultビューがanswer->attempt->grading_levelを参照するため)
        $answers = $attempt->answers()->with(['question', 'score'])->get()
            ->each(fn ($answer) => $answer->setRelation('attempt', $attempt));

        return view('answers.result', compact('section', 'answers'));
    }
}
