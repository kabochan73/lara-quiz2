<x-layout title="セクション編集">
    <div class="card">
        <h1>セクション編集</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('sections.update', $section) }}">
            @csrf
            @method('PUT')

            <label for="name">セクション名</label>
            <input type="text" id="name" name="name" value="{{ old('name', $section->name) }}" required autofocus>

            <label for="category_id">カテゴリ</label>
            <select id="category_id" name="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $section->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">更新する</button>
        </form>
    </div>
</x-layout>
