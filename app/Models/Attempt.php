<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * セクション内の全問に一括で回答した「1回分」の記録。
 * Udemyのクイズ結果のように、履歴はこの単位でまとめて表示する
 * (問題ごとではなく、1回の回答セッションごとに一覧・詳細を見る)。
 */
class Attempt extends Model
{
    protected $fillable = ['section_id', 'user_id', 'grading_level'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この挑戦で答えた、セクション内の各問題への回答
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
