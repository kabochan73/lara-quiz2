<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// アプリの起点はログイン画面(doc/requirements.md 5章)。ルートは常にログインへ流す。
Route::get('/', fn () => redirect()->route('login'));

// 未ログインのときだけ通れるルート。ログイン済みなら home へ流す(guestミドルウェア)。
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// ログイン必須のルート。会員登録は行わないので、ここに入れるのは常に唯一の管理者ユーザー。
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // 問題管理・回答・履歴は未実装。実装するまでの仮の着地点。
    Route::view('/home', 'home')->name('home');

    // カテゴリ管理(親子2階層)。showは使わないので除外。
    Route::resource('categories', CategoryController::class)->except(['show', 'create']);

    // 問題管理。詳細プレビュー(show)も含めてフル装備。
    Route::resource('questions', QuestionController::class);

    // 回答フロー。1〜10問まとめて回答→まとめて採点。
    Route::get('/answers/create', [AnswerController::class, 'create'])->name('answers.create');
    Route::post('/answers', [AnswerController::class, 'store'])->name('answers.store');
});
