<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * 親カテゴリの一覧(ログイン後の着地点)。子カテゴリや問題はここには出さず、
     * それぞれのカテゴリ詳細ページ(show)で見る。
     */
    public function index(): View
    {
        $categories = Category::whereNull('parent_id')
            ->withCount('children')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * カテゴリ作成フォーム。
     * ?parent_id=X 付きでアクセスされた場合は、そのカテゴリの「子カテゴリ作成」になる
     * (カテゴリ詳細ページの「+ 子カテゴリを追加」から遷移してくる)。
     */
    public function create(Request $request): View
    {
        $parent = $request->filled('parent_id')
            ? Category::whereNull('parent_id')->findOrFail($request->integer('parent_id'))
            : null;

        return view('categories.create', compact('parent'));
    }

    /**
     * カテゴリを新規作成する。parent_idを指定すれば子カテゴリになる。
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $category = Category::create($data);

        return redirect()->route('categories.show', $category)->with('status', 'カテゴリを追加しました。');
    }

    /**
     * カテゴリ詳細ページ。
     * 親カテゴリなら「子カテゴリ一覧+追加ボタン」、子カテゴリなら「問題一覧+追加ボタン」を出す。
     * どちらも、このカテゴリ直下の問題への「+ 問題を追加」「履歴を見る」の導線を持つ。
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'questions' => fn ($query) => $query->latest()]);

        $children = $category->parent_id === null
            ? $category->children()->withCount('questions')->orderBy('name')->get()
            : collect();

        return view('categories.show', compact('category', 'children'));
    }

    /**
     * 編集フォームを表示する。親カテゴリの選択肢は自分自身を除いたルートカテゴリのみ。
     */
    public function edit(Category $category): View
    {
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate($this->rules($category));

        $category->update($data);

        return redirect()->route('categories.show', $category)->with('status', 'カテゴリを更新しました。');
    }

    /**
     * カテゴリを削除する。cascadeOnDeleteにより、配下の子カテゴリ・問題・回答・採点結果も
     * すべて連動して削除される。削除後は一覧(親を消した場合)か、親の詳細ページ(子を消した場合)へ戻る。
     */
    public function destroy(Category $category): RedirectResponse
    {
        $parent = $category->parent;

        $category->delete();

        return $parent
            ? redirect()->route('categories.show', $parent)->with('status', 'カテゴリを削除しました。')
            : redirect()->route('categories.index')->with('status', 'カテゴリを削除しました。');
    }

    /**
     * 「親カテゴリ→子カテゴリの2階層まで」という要件をバリデーションでも担保する。
     * - 親に選べるのは、それ自体が子カテゴリではない(parent_idがnullの)カテゴリだけ
     * - 自分自身を親にはできない
     * - すでに子カテゴリを持つカテゴリを、他のカテゴリの子にはできない(3階層化の防止)
     *
     * Rule::exists()の絞り込みに頼らず、ここで直接DBを見て判定する
     * (対象カテゴリを取得して親子関係をその場でチェックするほうが確実なため)。
     */
    private function rules(?Category $category = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                function (string $attribute, mixed $value, Closure $fail) use ($category) {
                    if (! $value) {
                        return;
                    }

                    $parent = Category::find($value);

                    if (! $parent) {
                        $fail('指定された親カテゴリが見つかりません。');

                        return;
                    }

                    if (! is_null($parent->parent_id)) {
                        $fail('子カテゴリを親カテゴリには選べません(2階層までのため)。');

                        return;
                    }

                    if ($category && (int) $value === $category->id) {
                        $fail('自分自身を親カテゴリにはできません。');
                    }

                    if ($category && $category->children()->exists()) {
                        $fail('子カテゴリを持つカテゴリは、他のカテゴリの子にはできません。');
                    }
                },
            ],
        ];
    }
}
