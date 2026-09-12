<x-layout title="カテゴリ" wide>
    <div class="card">
        <div class="row" style="border-bottom:none;">
            <h1 style="margin:0;">カテゴリ</h1>
            <a class="btn-small" href="{{ route('categories.create') }}" style="background:#2f5233; color:#fff; text-decoration:none;">
                + 新規カテゴリ作成
            </a>
        </div>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif
    </div>

    <div class="card">
        @forelse ($categories as $category)
            <div class="row">
                <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
                <span style="color:#888; font-size:12px;">{{ $category->sections_count }}個のセクション</span>
            </div>
        @empty
            <p>まだカテゴリがありません。「+ 新規カテゴリ作成」から作成してください。</p>
        @endforelse
    </div>
</x-layout>
