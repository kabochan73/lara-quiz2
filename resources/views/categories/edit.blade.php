<x-layout title="カテゴリ編集">
    <div class="card">
        <h1>カテゴリ編集</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            <label for="name">カテゴリ名</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>

            <label for="parent_id">親カテゴリ</label>
            <select id="parent_id" name="parent_id">
                <option value="">-- なし(親カテゴリにする) --</option>
                @foreach ($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', $category->parent_id) == $parent->id)>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">更新する</button>
        </form>
    </div>
</x-layout>
