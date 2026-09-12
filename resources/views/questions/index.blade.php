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

    {{--
        問題を選んで回答するためのフォーム。中身の各行にも削除用のPOSTフォームがあり、
        HTMLはform同士の入れ子を許さないので、この<form>はここでは要素を持たず、
        チェックボックス・送信ボタン側からform属性で紐づける形にしている。
    --}}
    <form id="answer-select-form" method="GET" action="{{ route('answers.create') }}"></form>

    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h2 style="margin:0;">{{ $questions->count() }}件</h2>
            <span>
                <button type="submit" form="answer-select-form" class="btn-small" style="background:#2f5233;">
                    選択した問題に回答する(1〜10問)
                </button>
                <a class="btn-small" href="{{ route('questions.create') }}" style="background:#555;color:#fff;text-decoration:none;">
                    + 新しい問題を作成
                </a>
            </span>
        </div>

        @forelse ($questions as $question)
            <div class="row">
                <div>
                    <input type="checkbox" name="ids[]" value="{{ $question->id }}" form="answer-select-form">
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
