<x-layout title="採点結果" wide>
    <div class="card">
        <h1>採点結果</h1>
        <p style="color:#888; font-size:13px; margin:0;">
            採点レベル:
            {{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$answers->first()->score->grading_level] }}
            /
            {{ $answers->count() }}問
        </p>
    </div>

    @foreach ($answers as $answer)
        <div class="card">
            <h2>{{ $answer->question->title }}</h2>

            <p style="color:#555; white-space: pre-wrap;">{{ $answer->body }}</p>

            <div class="row" style="border-bottom:none; padding-top:0;">
                <strong style="font-size:24px;">{{ $answer->score->score }}点</strong>
            </div>
            <p style="white-space: pre-wrap;">{{ $answer->score->feedback }}</p>
        </div>
    @endforeach

    <div class="card">
        <a class="btn-small" href="{{ route('categories.show', $category) }}">カテゴリに戻る</a>
    </div>
</x-layout>
