<x-layout :title="$category->name" wide>
    <div class="card">
        @if ($category->parent)
            <p style="color:#888; font-size:12px; margin:0 0 8px;">
                <a href="{{ route('categories.show', $category->parent) }}">{{ $category->parent->name }}</a>
            </p>
        @endif

        <div class="row" style="border-bottom:none;">
            <h1 style="margin:0;">{{ $category->name }}</h1>
            <span>
                <a class="btn-small" href="{{ route('history.index', $category) }}">履歴を見る</a>
                <a class="btn-small" href="{{ route('categories.edit', $category) }}">編集</a>
                <form class="inline-form" method="POST" action="{{ route('categories.destroy', $category) }}"
                    onsubmit="return confirm('「{{ $category->name }}」を削除しますか?(中の問題・履歴もすべて削除されます)');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-small btn-danger">削除</button>
                </form>
            </span>
        </div>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif
    </div>

    {{-- 親カテゴリのときだけ、子カテゴリの一覧と追加ボタンを出す(2階層までのため) --}}
    @if ($category->parent_id === null)
        <div class="card">
            <div class="row" style="border-bottom:none;">
                <h2 style="margin:0;">子カテゴリ</h2>
                <a class="btn-small" href="{{ route('categories.create', ['parent_id' => $category->id]) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                    + 子カテゴリを追加
                </a>
            </div>

            @forelse ($children as $child)
                <div class="row">
                    <a href="{{ route('categories.show', $child) }}">{{ $child->name }}</a>
                    <span style="color:#888; font-size:12px;">{{ $child->questions_count }}問</span>
                </div>
            @empty
                <p>まだ子カテゴリがありません。</p>
            @endforelse
        </div>
    @endif

    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h2 style="margin:0;">このカテゴリの問題({{ $category->questions->count() }}/10)</h2>
            <a class="btn-small" href="{{ route('questions.create', $category) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                + 問題を追加
            </a>
        </div>

        {{-- 1カテゴリ最大10問なので、問題を選ばせずこのカテゴリの全問にまとめて回答する --}}
        @if ($category->questions->isNotEmpty())
            <div class="row" style="border-bottom:none; padding-top:0;">
                <a class="btn-small" href="{{ route('answers.create', $category) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                    全問({{ $category->questions->count() }}問)に回答する
                </a>
            </div>
        @endif

        @forelse ($category->questions as $question)
            <div class="row">
                <a href="{{ route('questions.show', $question) }}">{{ $question->excerpt() }}</a>
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
        @empty
            <p>まだ問題がありません。</p>
        @endforelse
    </div>
</x-layout>
