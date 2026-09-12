<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * カテゴリ内の全問に一括で回答した「1回分」の記録。
 * Udemyのクイズ結果のように、履歴はこの単位でまとめて表示する
 * (問題ごとではなく、1回の回答セッションごとに一覧・詳細を見る)。
 */
class Attempt extends Model
{
    protected $fillable = ['category_id', 'user_id', 'grading_level'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この挑戦で答えた、カテゴリ内の各問題への回答
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
