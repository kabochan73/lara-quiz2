<x-layout title="カテゴリ作成">
    <div class="card">
        <h1>
            @if ($parent)
                「{{ $parent->name }}」に子カテゴリを作成
            @else
                新規カテゴリ作成
            @endif
        </h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            {{-- 子カテゴリ作成のときは親を固定にする(選択式にはしない) --}}
            @if ($parent)
                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
            @endif

            <label for="name">カテゴリ名</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>

            <button type="submit">作成する</button>
        </form>
    </div>
</x-layout>
