<?php

namespace App\Services\Grading;

/**
 * Claude APIによる本採点を実装するまでの仮実装。
 * 回答フローの動作確認ができるよう、常に固定のダミー結果を返す。
 * AI採点を実装するときは、AppServiceProviderのバインド先を
 * ClaudeGradingService(仮)に差し替えるだけで済むようにしてある。
 */
class FakeGradingService implements GradingService
{
    public function grade(array $items, string $level): array
    {
        return array_map(
            fn () => [
                'score' => 0,
                'feedback' => '(仮の採点結果です。AI採点はまだ実装されていません)',
            ],
            $items
        );
    }
}
