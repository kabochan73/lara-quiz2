<x-layout title="問題作成">
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            @if ($category->parent)
                {{ $category->parent->name }} / {{ $category->name }}
            @else
                {{ $category->name }}
            @endif
        </p>

        <h1>問題作成</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        {{-- カテゴリはURLで決まっているので選び直させない。作成後にカテゴリを変えたい場合は編集画面で --}}
        <form method="POST" action="{{ route('questions.store', $category) }}">
            @csrf

            <label for="body">問題文</label>
            <textarea id="body" name="body" rows="8" required autofocus>{{ old('body') }}</textarea>

            <button type="submit">作成する</button>
        </form>
    </div>
</x-layout>
