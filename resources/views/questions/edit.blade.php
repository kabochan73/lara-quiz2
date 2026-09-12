<x-layout title="問題編集">
    <div class="card">
        <h1>問題編集</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('questions.update', $question) }}">
            @csrf
            @method('PUT')

            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="{{ old('title', $question->title) }}" required autofocus>

            <label for="category_id">カテゴリ</label>
            <x-category-select :categories="$categories" :selected="old('category_id', $question->category_id)" />

            <label for="body">問題文</label>
            <textarea id="body" name="body" rows="8" required>{{ old('body', $question->body) }}</textarea>

            <button type="submit">更新する</button>
        </form>
    </div>
</x-layout>
