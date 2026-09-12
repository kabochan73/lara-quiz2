<x-layout :title="$section->name" wide>
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            <a href="{{ route('categories.show', $section->category) }}">{{ $section->category->name }}</a>
        </p>

        <div class="row" style="border-bottom:none;">
            <h1 style="margin:0;">{{ $section->name }}</h1>
            <span>
                <a class="btn-small" href="{{ route('history.index', $section) }}">履歴を見る</a>
                <a class="btn-small" href="{{ route('sections.edit', $section) }}">編集</a>
                <form class="inline-form" method="POST" action="{{ route('sections.destroy', $section) }}"
                    onsubmit="return confirm('「{{ $section->name }}」を削除しますか?(中の問題・履歴もすべて削除されます)');">
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

    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h2 style="margin:0;">問題({{ $section->questions->count() }}/10)</h2>
            <a class="btn-small" href="{{ route('questions.create', $section) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                + 問題を追加
            </a>
        </div>

        {{-- 1セクション最大10問なので、問題を選ばせずこのセクションの全問にまとめて回答する --}}
        @if ($section->questions->isNotEmpty())
            <div class="row" style="border-bottom:none; padding-top:0;">
                <a class="btn-small" href="{{ route('answers.create', $section) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                    全問({{ $section->questions->count() }}問)に回答する
                </a>
            </div>
        @endif

        @forelse ($section->questions as $question)
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
