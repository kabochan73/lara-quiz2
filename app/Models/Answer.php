<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Answer extends Model
{
    protected $fillable = ['question_id', 'user_id', 'attempt_id', 'body'];

    /**
     * 解答対象の問題
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * この解答が属する「1回分の全問回答」(Udemyのクイズ結果のような挑戦単位)
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    /**
     * 解答した人
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この解答に対するAIの採点結果(1:1)
     */
    public function score(): HasOne
    {
        return $this->hasOne(Score::class);
    }
}
