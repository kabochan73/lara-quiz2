<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * カテゴリに属する全問題を横断して、直近10件の回答履歴を一覧表示する。
     * (DB側では問題ごとに直近10件保持しているが、こちらはカテゴリ単位で直近10件に絞る表示側のルール)
     */
    public function index(Request $request, Category $category): View
    {
        $answers = Answer::whereHas('question', fn ($q) => $q->where('category_id', $category->id))
            ->where('user_id', $request->user()->id)
            ->with(['question', 'score'])
            ->latest()
            ->take(10)
            ->get();

        return view('history.index', compact('category', 'answers'));
    }

    /**
     * 履歴1件の詳細(問題文・回答全文・点数・フィードバック全文)。
     */
    public function show(Request $request, Category $category, Answer $answer): View
    {
        abort_unless(
            $answer->user_id === $request->user()->id && $answer->question->category_id === $category->id,
            404
        );

        $answer->load(['question', 'score']);

        return view('history.show', compact('category', 'answer'));
    }
}
