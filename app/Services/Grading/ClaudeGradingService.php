<?php

namespace App\Services\Grading;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Claude API(Messages API)を使った本採点の実装。
 *
 * 「問題文と回答のみからAIが自己判断で採点する」(模範解答は与えない)という
 * 要件定義どおりの方針で、選択された全問題を1回のAPIリクエストにまとめて送る。
 * 採点結果はtool use(function calling)で厳密なJSON構造として受け取ることで、
 * 自由文からのパース失敗を避けている。
 */
class ClaudeGradingService implements GradingService
{
    private const MODEL = 'claude-sonnet-5';

    private const API_VERSION = '2023-06-01';

    public function grade(array $items, string $level): array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => self::API_VERSION,
        ])
            ->timeout(60)
            ->post('https://api.anthropic.com/v1/messages', [
                'model' => self::MODEL,
                'max_tokens' => 4096,
                'system' => $this->systemPrompt($level),
                'messages' => [
                    ['role' => 'user', 'content' => $this->userPrompt($items)],
                ],
                'tools' => [$this->toolDefinition()],
                // 自由文で返されるとパースが不安定になるので、必ずこのツールを呼ばせる
                'tool_choice' => ['type' => 'tool', 'name' => 'submit_grades'],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Claude APIへのリクエストに失敗しました: '.$response->body());
        }

        $toolUse = collect($response->json('content'))->firstWhere('type', 'tool_use');

        if (! $toolUse) {
            throw new RuntimeException('Claude APIから採点結果が返ってきませんでした。');
        }

        $grades = collect($this->extractGrades($toolUse['input'] ?? []))->keyBy('index');

        return collect($items)
            ->values()
            ->map(function ($item, int $index) use ($grades) {
                $grade = $grades->get($index);

                if (! $grade) {
                    throw new RuntimeException("{$index}番目の問題の採点結果が見つかりませんでした。");
                }

                return [
                    // AIが範囲外の点数を返してもDB制約(0〜100)に収まるよう念のためクランプする
                    'score' => max(0, min(100, (int) $grade['score'])),
                    'feedback' => (string) $grade['feedback'],
                ];
            })
            ->all();
    }

    /**
     * tool_useのinput.gradesを配列として取り出す。
     *
     * 本来は input = {"grades": [ {...}, {...} ]} という構造で返ってくるはずだが、
     * 実際には input.grades がJSON文字列になっていたり、その文字列の中身がさらに
     * {"grades": [...]} と一段ネストしていたりすることがあるため、両方のパターンに対応する。
     */
    private function extractGrades(array $input): array
    {
        $grades = $input['grades'] ?? [];

        if (is_string($grades)) {
            $decoded = json_decode($grades, true) ?? [];
            $grades = $decoded['grades'] ?? $decoded;
        }

        return is_array($grades) ? $grades : [];
    }

    /**
     * 採点レベルに応じて採点方針をプロンプトに明示する(要件定義: レベルごとに方針を固定してブレを抑える)。
     */
    private function systemPrompt(string $level): string
    {
        $policy = match ($level) {
            'easy' => '優しめに採点してください。多少の言葉足らずや表現の粗さは減点せず、趣旨が伝わっていれば高めの点数をつけてください。',
            'hard' => '厳しめに採点してください。曖昧な表現・論理の飛躍・説明不足を見逃さず、完成度が高い解答でなければ高得点をつけないでください。',
            default => '標準的な基準で採点してください。過度に甘くも厳しくもしないでください。',
        };

        return <<<PROMPT
        あなたは学習アプリの採点者です。ユーザーが自由記述で答えた解答を、それぞれ0〜100点の整数で採点し、
        日本語で簡潔な評価コメント(良い点・改善点)を付けてください。
        模範解答は与えられないので、問題文の内容から妥当な採点基準を自分で判断してください。

        採点の厳しさ: {$policy}

        必ずsubmit_gradesツールを使い、渡された問題の数だけ採点結果を返してください。
        PROMPT;
    }

    /**
     * 選択された全問題を「【問題0】...【回答0】...」の形でまとめて1つのメッセージにする。
     */
    private function userPrompt(array $items): string
    {
        return collect($items)
            ->values()
            ->map(fn (array $item, int $index) => sprintf(
                "【問題%d】\n%s\n\n【回答%d】\n%s",
                $index,
                $item['question']->body,
                $index,
                $item['body']
            ))
            ->implode("\n\n---\n\n");
    }

    /**
     * 採点結果を厳密な構造で受け取るためのtool定義。
     */
    private function toolDefinition(): array
    {
        return [
            'name' => 'submit_grades',
            'description' => '各問題の採点結果をまとめて返す',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'grades' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'index' => ['type' => 'integer', 'description' => '採点対象の問題番号(0始まり)'],
                                'score' => ['type' => 'integer', 'description' => '0〜100点の整数'],
                                'feedback' => ['type' => 'string', 'description' => '日本語での評価コメント'],
                            ],
                            'required' => ['index', 'score', 'feedback'],
                        ],
                    ],
                ],
                'required' => ['grades'],
            ],
        ];
    }
}
