<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'body'];

    /**
     * 問題の作成者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 分類先のカテゴリ(未分類の場合はnull)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * この問題に対する解答一覧。再挑戦のたびに増えていく
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
