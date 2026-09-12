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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();

            // answersと1:1の関係。unique()をconstrained()より前に呼ぶことで
            // 「1つの回答につき採点結果は1件だけ」をDBレベルで保証する
            $table->foreignId('answer_id')->unique()->constrained()->cascadeOnDelete();

            // 0〜100点の整数。0〜255に収まるのでtinyIntegerで十分
            $table->unsignedTinyInteger('score');

            // AIからの日本語フィードバックコメント
            $table->text('feedback');

            // grading_levelは挑戦(attempts)単位の情報なので、ここでは持たずattempts側で管理する

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
