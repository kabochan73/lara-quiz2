<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    /**
     * 1セクションに置ける問題数の上限。
     * 「セクション内の全問題に一括で回答する」という回答フローの前提(1回のAPIリクエストで
     * 採点できるのは最大10問)を、そもそも問題を作る時点で守らせるための制限。
     */
    private const MAX_QUESTIONS_PER_SECTION = 10;

    /**
     * 問題作成フォーム。セクションはURL(/sections/{section}/questions/create)で決まっているので、
     * フォーム側で選び直す必要はない(隠しフィールドで固定)。
     */
    public function create(Section $section): View
    {
        $isFull = $section->questions()->count() >= self::MAX_QUESTIONS_PER_SECTION;

        return view('questions.create', compact('section', 'isFull'));
    }

    public function store(Request $request, Section $section): RedirectResponse
    {
        if ($section->questions()->count() >= self::MAX_QUESTIONS_PER_SECTION) {
            return back()->withInput()->withErrors([
                'body' => '1つのセクションに作れる問題は'.self::MAX_QUESTIONS_PER_SECTION.'問までです。',
            ]);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        // section_idはURLで指定されたセクションに固定する(ユーザー入力のsection_idは信用しない)
        $section->questions()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('sections.show', $section)->with('status', '問題を作成しました。');
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

        $categories = Category::with('sections')->orderBy('name')->get();

        return view('questions.edit', compact('question', 'categories'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        // 別のセクションに移そうとしている場合、移動先がすでに上限いっぱいでないか確認する
        if ((int) $data['section_id'] !== $question->section_id) {
            $newSectionCount = Section::findOrFail($data['section_id'])->questions()->count();

            if ($newSectionCount >= self::MAX_QUESTIONS_PER_SECTION) {
                return back()->withInput()->withErrors([
                    'section_id' => '移動先のセクションはすでに'.self::MAX_QUESTIONS_PER_SECTION.'問に達しています。',
                ]);
            }
        }

        $question->update($data);

        return redirect()->route('questions.show', $question)->with('status', '問題を更新しました。');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $this->authorizeOwner($question);

        $section = $question->section;

        $question->delete();

        return redirect()->route('sections.show', $section)->with('status', '問題を削除しました。');
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
