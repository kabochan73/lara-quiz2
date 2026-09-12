<x-layout title="履歴" wide>
    <div class="card">
        <p style="color:#888; font-size:12px; margin:0 0 8px;">
            <a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a>
        </p>
        <h1>履歴</h1>
        <p style="color:#888; font-size:13px; margin:0;">
            「全問に回答する」を1回行うごとに、1件の履歴として記録されます。
        </p>
    </div>

    <div class="card">
        @forelse ($attempts as $attempt)
            @php
                $average = $attempt->answers->isNotEmpty()
                    ? round($attempt->answers->avg(fn ($a) => $a->score->score))
                    : null;
            @endphp
            <a href="{{ route('history.show', [$category, $attempt]) }}" style="text-decoration:none; color:inherit;">
                <div class="row">
                    <div>
                        {{ $attempt->created_at->format('Y/m/d H:i') }}
                        <span style="color:#888; font-size:12px;">
                            {{ $attempt->answers->count() }}問
                            ・{{ ['easy' => '優しい', 'normal' => '普通', 'hard' => '厳しい'][$attempt->grading_level] }}
                        </span>
                    </div>
                    <strong>平均 {{ $average }}点</strong>
                </div>
            </a>
        @empty
            <p>まだ回答履歴がありません。</p>
        @endforelse
    </div>
</x-layout>
