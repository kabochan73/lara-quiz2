<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * カテゴリの下にぶら下がる、問題の入れ物。
 * 「カテゴリに問題とセクションが両方ぶら下がっている」状態が分かりにくかったため、
 * 問題は必ずセクションの下に置く形に整理した(カテゴリは分類、セクションが実体)。
 */
class Section extends Model
{
    protected $fillable = ['category_id', 'name'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * このセクションに属する問題一覧(最大10問)
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * このセクションの全問に一括で回答した挑戦(Attempt)の履歴
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
