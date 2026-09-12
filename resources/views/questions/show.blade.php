<x-layout :title="$question->excerpt()">
    <div class="card">
        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            @if ($question->category->parent)
                <a href="{{ route('categories.show', $question->category->parent) }}">{{ $question->category->parent->name }}</a>
                / <a href="{{ route('categories.show', $question->category) }}">{{ $question->category->name }}</a>
            @else
                <a href="{{ route('categories.show', $question->category) }}">{{ $question->category->name }}</a>
            @endif
        </p>

        <p style="white-space: pre-wrap;">{{ $question->body }}</p>

        <div class="row" style="border-bottom:none; margin-top:24px;">
            <a class="btn-small" href="{{ route('categories.show', $question->category) }}">カテゴリへ戻る</a>
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
