<x-layout title="セクション作成">
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">{{ $category->name }}</p>
        <h1>セクション作成</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('sections.store', $category) }}">
            @csrf

            <label for="name">セクション名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>

            <button type="submit">作成する</button>
        </form>
    </div>
</x-layout>
