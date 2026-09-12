<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * 自分が作成した問題の一覧。タイトルのキーワード検索とカテゴリ絞り込みに対応する。
     */
    public function index(Request $request): View
    {
        $questions = Question::query()
            ->where('user_id', $request->user()->id)
            ->with('category')
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->latest()
            ->get();

        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('questions.index', compact('questions', 'categories'));
    }

    /**
     * 問題作成フォーム。カテゴリは親カテゴリの下に子カテゴリをぶら下げた形で選択肢を出す。
     */
    public function create(): View
    {
        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('questions.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $question = $request->user()->questions()->create($data);

        return redirect()->route('questions.show', $question)->with('status', '問題を作成しました。');
    }

    /**
     * 問題の詳細プレビュー。
     */
    public function show(Question $question): View
    {
        $this->authorizeOwner($question);

        return view('questions.show', compact('question'));
    }

    public function edit(Question $question): View
    {
        $this->authorizeOwner($question);

        $categories = Category::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $question->update($data);

        return redirect()->route('questions.show', $question)->with('status', '問題を更新しました。');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $question->delete();

        return redirect()->route('questions.index')->with('status', '問題を削除しました。');
    }

    /**
     * 今は管理者1人しかいないが、要件定義どおり「作成者本人のみ編集・削除できる」を
     * 将来のマルチユーザー化に備えて明示的にチェックしておく。
     */
    private function authorizeOwner(Question $question): void
    {
        abort_unless($question->user_id === auth()->id(), 403);
    }
}
