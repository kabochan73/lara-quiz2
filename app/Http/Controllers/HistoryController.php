<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * 回答履歴の一覧と得点推移をまとめて表示する。問題・期間で絞り込める。
     */
    public function index(Request $request): View
    {
        $answers = Answer::query()
            ->where('user_id', $request->user()->id)
            ->with(['question', 'score'])
            ->when($request->filled('question_id'), fn ($q) => $q->where('question_id', $request->integer('question_id')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            // グラフを時系列(古い→新しい)で描きたいので昇順で取得する。
            // 一覧表示側は新しい順にしたいので、blade側で並べ替える。
            ->oldest()
            ->get();

        $questions = Question::where('user_id', $request->user()->id)->orderBy('title')->get();

        return view('history.index', compact('answers', 'questions'));
    }
}
