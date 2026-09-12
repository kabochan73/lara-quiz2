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
     * 問題作成フォーム。カテゴリはURL(/categories/{category}/questions/create)で決まっているので、
     * フォーム側で選び直す必要はない(隠しフィールドで固定)。
     */
    public function create(Category $category): View
    {
        return view('questions.create', compact('category'));
    }

    public function store(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        // category_idはURLで指定されたカテゴリに固定する(ユーザー入力のcategory_idは信用しない)
        $question = $category->questions()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

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
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $question->update($data);

        return redirect()->route('questions.show', $question)->with('status', '問題を更新しました。');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $category = $question->category;

        $question->delete();

        return redirect()->route('categories.show', $category)->with('status', '問題を削除しました。');
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
