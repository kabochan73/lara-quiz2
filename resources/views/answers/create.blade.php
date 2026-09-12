<x-layout title="回答する" wide>
    <div class="card">
        <h1>回答する({{ $questions->count() }}問)</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('answers.store') }}">
            @csrf

            {{-- 採点レベルはこのバッチ全体に1つだけ適用される --}}
            <label>採点レベル</label>
            <div style="margin-bottom: 24px;">
                @foreach (['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'] as $value => $label)
                    <label style="display:inline-flex; align-items:center; gap:4px; margin-right:16px; font-weight:normal; font-size:14px;">
                        <input
                            type="radio"
                            name="grading_level"
                            value="{{ $value }}"
                            @checked(old('grading_level', 'normal') === $value)
                            required
                        >
                        {{ $label }}
                    </label>
                @endforeach
            </div>

            @foreach ($questions as $i => $question)
                <div class="card" style="background:#fafbf9; margin-bottom:16px;">
                    <h2>{{ $loop->iteration }}. {{ $question->title }}</h2>
                    <p style="white-space: pre-wrap; color:#444;">{{ $question->body }}</p>

                    {{-- どの問題への回答かをサーバー側で復元できるよう、問題IDも一緒に送る --}}
                    <input type="hidden" name="answers[{{ $i }}][question_id]" value="{{ $question->id }}">

                    <label for="body-{{ $question->id }}">あなたの解答</label>
                    <textarea id="body-{{ $question->id }}" name="answers[{{ $i }}][body]" rows="6" required>{{ old("answers.$i.body") }}</textarea>
                </div>
            @endforeach

            <button type="submit">まとめて採点する</button>
        </form>
    </div>
</x-layout>
