<x-layout title="採点結果" wide>
    <div class="card">
        <h1>採点結果</h1>
        <p style="color:#888; font-size:13px; margin:0;">
            採点レベル:
            {{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$answers->first()->attempt->grading_level] }}
            /
            {{ $answers->count() }}問
        </p>
    </div>

    @foreach ($answers as $answer)
        <div class="card">
            <p style="white-space: pre-wrap;">{{ $answer->question->body }}</p>

            <p style="color:#555; white-space: pre-wrap; border-top:1px solid #eee; padding-top:12px;">{{ $answer->body }}</p>

            <div class="row" style="border-bottom:none; padding-top:0;">
                <strong style="font-size:24px;">{{ $answer->score->score }}点</strong>
            </div>
            <p style="white-space: pre-wrap;">{{ $answer->score->feedback }}</p>
        </div>
    @endforeach

    <div class="card">
        <a class="btn-small" href="{{ route('sections.show', $section) }}">セクションに戻る</a>
        <a class="btn-small" href="{{ route('history.index', $section) }}">履歴一覧を見る</a>
    </div>
</x-layout>
