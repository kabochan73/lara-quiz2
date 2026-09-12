<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * カテゴリ一覧(ログイン後の着地点)。カテゴリは分類だけの役割で、
     * 中のセクション・問題はそれぞれの詳細ページで見る。
     */
    public function index(): View
    {
        $categories = Category::withCount('sections')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * カテゴリ作成フォーム。
     */
    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $category = Category::create($data);

        return redirect()->route('categories.show', $category)->with('status', 'カテゴリを追加しました。');
    }

    /**
     * カテゴリ詳細ページ。中のセクション一覧と「+ セクションを追加」ボタンを出す。
     */
    public function show(Category $category): View
    {
        $sections = $category->sections()->withCount('questions')->orderBy('name')->get();

        return view('categories.show', compact('category', 'sections'));
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $category->update($data);

        return redirect()->route('categories.show', $category)->with('status', 'カテゴリを更新しました。');
    }

    /**
     * カテゴリを削除する。cascadeOnDeleteにより、配下のセクション・問題・回答・採点結果も
     * すべて連動して削除される。
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')->with('status', 'カテゴリを削除しました。');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
