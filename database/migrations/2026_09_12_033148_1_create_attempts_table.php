<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();

            // カテゴリ内の全問に一括で回答した「1回分」を表す。
            // カテゴリを削除したら、その挑戦記録もまとめて消す。
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // この挑戦全体に適用された採点レベル(優しい/普通/厳しい)。
            // 個々の解答(answers/scores)ではなく、挑戦単位で1つだけ持つ。
            $table->enum('grading_level', ['easy', 'normal', 'hard']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
