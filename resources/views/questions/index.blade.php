<x-layout title="問題一覧" wide>
    <div class="card">
        <h1>問題一覧</h1>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        {{-- タイトルのキーワード検索とカテゴリ絞り込み。GETなのでURLで状態を共有できる --}}
        <form method="GET" action="{{ route('questions.index') }}">
            <label for="q">キーワード</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="タイトルで検索">

            <label for="category_id">カテゴリ</label>
            <x-category-select :categories="$categories" :selected="request('category_id')" />

            <button type="submit">絞り込む</button>
        </form>
    </div>

    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h2 style="margin:0;">{{ $questions->count() }}件</h2>
            <a class="btn-small" href="{{ route('questions.create') }}" style="background:#2f5233;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;">
                + 新しい問題を作成
            </a>
        </div>

        @forelse ($questions as $question)
            <div class="row">
                <div>
                    <a href="{{ route('questions.show', $question) }}">{{ $question->title }}</a>
                    @if ($question->category)
                        <span style="color:#888; font-size:12px;">
                            [{{ $question->category->parent?->name ?? $question->category->name }}
                            @if ($question->category->parent) / {{ $question->category->name }} @endif]
                        </span>
                    @endif
                </div>
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
        @empty
            <p>まだ問題がありません。</p>
        @endforelse
    </div>
</x-layout>
