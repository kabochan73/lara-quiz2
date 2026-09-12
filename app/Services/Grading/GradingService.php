<?php

namespace App\Services\Grading;

interface GradingService
{
    /**
     * 選択された問題+回答をまとめて採点する(要件定義: 1回のAPIリクエストで1〜10問をまとめて採点)。
     *
     * @param  array<int, array{question: \App\Models\Question, body: string}>  $items  採点したい問題と回答の組
     * @param  string  $level  'easy' | 'normal' | 'hard'
     * @return array<int, array{score: int, feedback: string}>  $itemsと同じ順番・同じ件数で返す
     */
    public function grade(array $items, string $level): array;
}
