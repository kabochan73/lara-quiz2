<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// アプリの起点はログイン画面(doc/requirements.md 5章)。ルートは常にログインへ流す。
Route::get('/', fn () => redirect()->route('login'));

// 未ログインのときだけ通れるルート。ログイン済みなら categories へ流す(guestミドルウェア)。
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// ログイン必須のルート。会員登録は行わないので、ここに入れるのは常に唯一の管理者ユーザー。
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // カテゴリ管理(親子2階層)。ログイン後の着地点でもあり、アプリの起点になる。
    // showがカテゴリ詳細ページ(子カテゴリ/問題の一覧+追加ボタン)。
    Route::resource('categories', CategoryController::class);

    // 問題は必ずカテゴリに紐づくため、作成はカテゴリ配下のURLにする(カテゴリ選択の必要がない)。
    Route::get('/categories/{category}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/categories/{category}/questions', [QuestionController::class, 'store'])->name('questions.store');

    // 詳細プレビュー・編集・削除は問題IDだけで一意に決まるのでフラットなURLのまま。
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // 回答フロー。カテゴリ内の問題1〜10問をまとめて回答→まとめて採点(カテゴリをまたいだ回答はしない)。
    Route::get('/categories/{category}/answers/create', [AnswerController::class, 'create'])->name('answers.create');
    Route::post('/categories/{category}/answers', [AnswerController::class, 'store'])->name('answers.store');

    // カテゴリ単位の回答履歴(直近10件)・履歴詳細
    Route::get('/categories/{category}/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/categories/{category}/history/{attempt}', [HistoryController::class, 'show'])->name('history.show');
});
