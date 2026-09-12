<x-layout title="履歴・得点推移" wide>
    <div class="card">
        <h1>履歴・得点推移</h1>

        {{-- 問題・期間で絞り込み。GETなのでURLで状態を共有できる --}}
        <form method="GET" action="{{ route('history.index') }}">
            <label for="question_id">問題</label>
            <select id="question_id" name="question_id">
                <option value="">-- すべて --</option>
                @foreach ($questions as $q)
                    <option value="{{ $q->id }}" @selected(request('question_id') == $q->id)>{{ $q->title }}</option>
                @endforeach
            </select>

            <label for="from">期間(開始)</label>
            <input type="date" id="from" name="from" value="{{ request('from') }}">

            <label for="to">期間(終了)</label>
            <input type="date" id="to" name="to" value="{{ request('to') }}">

            <button type="submit">絞り込む</button>
        </form>
    </div>

    <div class="card">
        <h2>得点推移</h2>
        <x-score-chart :points="$answers->map(fn ($a) => ['label' => $a->created_at->format('n/j H:i'), 'score' => $a->score->score])" />
    </div>

    <div class="card">
        <h2>{{ $answers->count() }}件</h2>

        {{-- グラフは時系列(古い→新しい)、一覧は新しい順のほうが見やすいのでここで並べ替える --}}
        @forelse ($answers->sortByDesc('created_at') as $answer)
            <div class="row">
                <div>
                    <a href="{{ route('questions.show', $answer->question) }}">{{ $answer->question->title }}</a>
                    <span style="color:#888; font-size:12px;">
                        {{ $answer->created_at->format('Y/m/d H:i') }}
                        ・{{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$answer->score->grading_level] }}
                    </span>
                </div>
                <strong>{{ $answer->score->score }}点</strong>
            </div>
        @empty
            <p>まだ回答履歴がありません。</p>
        @endforelse
    </div>
</x-layout>
