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

            <label for="category_id">カテゴリ</label>
            <x-category-select :categories="$categories" :selected="old('category_id', $question->category_id)" />

            <label for="body">問題文</label>
            <textarea id="body" name="body" rows="8" required autofocus>{{ old('body', $question->body) }}</textarea>

            <button type="submit">更新する</button>
        </form>
    </div>
</x-layout>
