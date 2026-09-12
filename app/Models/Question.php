<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Question extends Model
{
    protected $fillable = ['user_id', 'section_id', 'body'];

    /**
     * 問題の作成者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 分類先のセクション(必須)
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * この問題に対する解答一覧。再挑戦のたびに増えていく
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * タイトルを持たないので、一覧やページタイトルでは本文の先頭を短く切り出して使う。
     * 改行は表示上見づらいのでスペースに置き換えてから切り詰める。
     */
    public function excerpt(int $length = 40): string
    {
        return Str::limit(str_replace(["\r\n", "\r", "\n"], ' ', $this->body), $length);
    }
}
