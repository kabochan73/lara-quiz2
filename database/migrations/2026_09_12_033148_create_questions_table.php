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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // 作成者(今は管理者1人だけだが、将来のマルチユーザー化に備えて残す)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // カテゴリは必須(未分類は廃止)。カテゴリを削除したら、その問題も連動して削除する
            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
