<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Score extends Model
{
    protected $fillable = ['answer_id', 'score', 'feedback', 'grading_level'];

    protected function casts(): array
    {
        return [
            // 0〜100の整数として扱う
            'score' => 'integer',
        ];
    }

    /**
     * 採点対象の解答
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
