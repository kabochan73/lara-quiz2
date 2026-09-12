<x-layout title="履歴" wide>
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
        </p>
        <h1>回答履歴(直近{{ $answers->count() }}件)</h1>
    </div>

    <div class="card">
        @forelse ($answers as $answer)
            <a href="{{ route('history.show', [$category, $answer]) }}" style="text-decoration:none; color:inherit;">
                <div class="row">
                    <div>
                        {{ $answer->question->title }}
                        <span style="color:#888; font-size:12px;">
                            {{ $answer->created_at->format('Y/m/d H:i') }}
                            ・{{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$answer->score->grading_level] }}
                        </span>
                    </div>
                    <strong>{{ $answer->score->score }}点</strong>
                </div>
            </a>
        @empty
            <p>まだ回答履歴がありません。</p>
        @endforelse
    </div>
</x-layout>
