<x-layout title="履歴詳細" wide>
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
            /
            <a href="{{ route('history.index', $category) }}">履歴</a>
        </p>

        <p style="color:#888; font-size:12px;">
            {{ $answer->created_at->format('Y/m/d H:i') }}
            ・採点レベル: {{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$answer->score->grading_level] }}
        </p>
    </div>

    <div class="card">
        <h2>問題文</h2>
        <p style="white-space: pre-wrap;">{{ $answer->question->body }}</p>
    </div>

    <div class="card">
        <h2>あなたの回答</h2>
        <p style="white-space: pre-wrap; color:#444;">{{ $answer->body }}</p>
    </div>

    <div class="card">
        <div class="row" style="border-bottom:none; padding-top:0;">
            <h2 style="margin:0;">採点結果</h2>
            <strong style="font-size:24px;">{{ $answer->score->score }}点</strong>
        </div>
        <p style="white-space: pre-wrap;">{{ $answer->score->feedback }}</p>
    </div>

    <div class="card">
        <a class="btn-small" href="{{ route('history.index', $category) }}">履歴一覧へ戻る</a>
    </div>
</x-layout>
