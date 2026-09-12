<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name'];

    /**
     * このカテゴリに属するセクション一覧。問題はセクションの下にぶら下がる。
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
