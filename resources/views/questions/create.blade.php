<x-layout title="問題作成">
    <div class="card">
        <h1>問題作成</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('questions.store') }}">
            @csrf

            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus>

            <label for="category_id">カテゴリ</label>
            <x-category-select :categories="$categories" :selected="old('category_id')" />

            <label for="body">問題文</label>
            <textarea id="body" name="body" rows="8" required>{{ old('body') }}</textarea>

            <button type="submit">作成する</button>
        </form>
    </div>
</x-layout>
