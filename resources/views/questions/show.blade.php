<x-layout :title="$question->title">
    <div class="card">
        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        @if ($question->category)
            <p style="color:#888; font-size:12px; margin:0 0 8px;">
                {{ $question->category->parent?->name ?? $question->category->name }}
                @if ($question->category->parent) / {{ $question->category->name }} @endif
            </p>
        @endif

        <h1>{{ $question->title }}</h1>

        <p style="white-space: pre-wrap;">{{ $question->body }}</p>

        <div class="row" style="border-bottom:none; margin-top:24px;">
            <a class="btn-small" href="{{ route('questions.index') }}">一覧へ戻る</a>
            <span>
                <a class="btn-small" href="{{ route('questions.edit', $question) }}">編集</a>
                <form class="inline-form" method="POST" action="{{ route('questions.destroy', $question) }}"
                    onsubmit="return confirm('「{{ $question->title }}」を削除しますか?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-small btn-danger">削除</button>
                </form>
            </span>
        </div>
    </div>
</x-layout>
