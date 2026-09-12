<x-layout title="カテゴリ管理" wide>
    <div class="card">
        <h1>カテゴリ管理</h1>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        {{-- 親カテゴリを選ばなければ「親カテゴリ」、選べば「その子カテゴリ」として作成される --}}
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <label for="name">カテゴリ名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>

            <label for="parent_id">親カテゴリ</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- なし(親カテゴリとして作成) --</option>
                @foreach ($categories as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ $parent->name }}</option>
                @endforeach
            </select>

            <button type="submit">追加</button>
        </form>
    </div>

    <div class="card">
        <h2>カテゴリ一覧</h2>

        @forelse ($categories as $parent)
            <div class="row">
                <strong>{{ $parent->name }}</strong>
                <span>
                    <a class="btn-small" href="{{ route('categories.edit', $parent) }}">編集</a>
                    <form class="inline-form" method="POST" action="{{ route('categories.destroy', $parent) }}"
                        onsubmit="return confirm('「{{ $parent->name }}」を削除しますか?(子カテゴリも一緒に削除されます)');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-small btn-danger">削除</button>
                    </form>
                </span>
            </div>

            @if ($parent->children->isNotEmpty())
                <ul class="list-plain">
                    @foreach ($parent->children as $child)
                        <li class="row">
                            {{ $child->name }}
                            <span>
                                <a class="btn-small" href="{{ route('categories.edit', $child) }}">編集</a>
                                <form class="inline-form" method="POST" action="{{ route('categories.destroy', $child) }}"
                                    onsubmit="return confirm('「{{ $child->name }}」を削除しますか?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-small btn-danger">削除</button>
                                </form>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        @empty
            <p>まだカテゴリがありません。</p>
        @endforelse
    </div>
</x-layout>
