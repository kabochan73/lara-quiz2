<x-layout :title="$question->excerpt()">
    <div class="card">
        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            <a href="{{ route('categories.show', $question->section->category) }}">{{ $question->section->category->name }}</a>
            / <a href="{{ route('sections.show', $question->section) }}">{{ $question->section->name }}</a>
        </p>

        <p style="white-space: pre-wrap;">{{ $question->body }}</p>

        <div class="row" style="border-bottom:none; margin-top:24px;">
            <a class="btn-small" href="{{ route('sections.show', $question->section) }}">セクションへ戻る</a>
            <span>
                <a class="btn-small" href="{{ route('questions.edit', $question) }}">編集</a>
                <form class="inline-form" method="POST" action="{{ route('questions.destroy', $question) }}"
                    onsubmit="return confirm('この問題を削除しますか?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-small btn-danger">削除</button>
                </form>
            </span>
        </div>
    </div>
</x-layout>
