<x-layout :title="$category->name" wide>
    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h1 style="margin:0;">{{ $category->name }}</h1>
            <span>
                <a class="btn-small" href="{{ route('categories.edit', $category) }}">編集</a>
                <form class="inline-form" method="POST" action="{{ route('categories.destroy', $category) }}"
                    onsubmit="return confirm('「{{ $category->name }}」を削除しますか?(中のセクション・問題・履歴もすべて削除されます)');">
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
            <h2 style="margin:0;">セクション</h2>
            <a class="btn-small" href="{{ route('sections.create', $category) }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                + セクションを追加
            </a>
        </div>

        @forelse ($sections as $section)
            <div class="row">
                <a href="{{ route('sections.show', $section) }}">{{ $section->name }}</a>
                <span style="color:#888; font-size:12px;">{{ $section->questions_count }}/10問</span>
            </div>
        @empty
            <p>まだセクションがありません。「+ セクションを追加」から作成してください。</p>
        @endforelse
    </div>
</x-layout>
