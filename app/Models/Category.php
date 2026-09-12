<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name'];

    /**
     * 親カテゴリ。nullなら自分がトップレベルの親カテゴリ。
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * 自分にぶら下がる子カテゴリ一覧(親カテゴリ側から見たとき)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * このカテゴリに分類されている問題一覧
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
