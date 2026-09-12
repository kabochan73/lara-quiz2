<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    /**
     * セクション作成フォーム。カテゴリはURLで固定される。
     */
    public function create(Category $category): View
    {
        return view('sections.create', compact('category'));
    }

    public function store(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        $section = $category->sections()->create($data);

        return redirect()->route('sections.show', $section)->with('status', 'セクションを追加しました。');
    }

    /**
     * セクション詳細ページ。中の問題一覧、「+ 問題を追加」「全問に回答する」「履歴を見る」を出す。
     */
    public function show(Section $section): View
    {
        $section->load(['category', 'questions' => fn ($query) => $query->latest()]);

        return view('sections.show', compact('section'));
    }

    /**
     * 編集フォーム。カテゴリの付け替えができるよう、カテゴリ一覧も渡す。
     */
    public function edit(Section $section): View
    {
        $categories = Category::orderBy('name')->get();

        return view('sections.edit', compact('section', 'categories'));
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $section->update($data);

        return redirect()->route('sections.show', $section)->with('status', 'セクションを更新しました。');
    }

    /**
     * セクションを削除する。cascadeOnDeleteにより、配下の問題・回答・採点結果も連動削除される。
     */
    public function destroy(Section $section): RedirectResponse
    {
        $category = $section->category;

        $section->delete();

        return redirect()->route('categories.show', $category)->with('status', 'セクションを削除しました。');
    }
}
