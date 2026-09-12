<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionController;
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

    // カテゴリ管理。ログイン後の着地点でもあり、アプリの起点になる。
    // showはカテゴリ詳細ページ(中のセクション一覧+追加ボタン)。カテゴリは問題を直接持たない。
    Route::resource('categories', CategoryController::class);

    // セクションは必ずカテゴリに紐づくため、作成はカテゴリ配下のURLにする。
    Route::get('/categories/{category}/sections/create', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/categories/{category}/sections', [SectionController::class, 'store'])->name('sections.store');

    // 詳細・編集・削除はセクションIDだけで一意に決まるのでフラットなURL。
    Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');
    Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

    // 問題は必ずセクションに紐づくため、作成はセクション配下のURLにする(選択の必要がない)。
    Route::get('/sections/{section}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/sections/{section}/questions', [QuestionController::class, 'store'])->name('questions.store');

    // 詳細プレビュー・編集・削除は問題IDだけで一意に決まるのでフラットなURLのまま。
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // 回答フロー。セクション内の問題(最大10問)をまとめて回答→まとめて採点(セクションをまたいだ回答はしない)。
    Route::get('/sections/{section}/answers/create', [AnswerController::class, 'create'])->name('answers.create');
    Route::post('/sections/{section}/answers', [AnswerController::class, 'store'])->name('answers.store');

    // セクション単位の回答履歴(挑戦一覧)・履歴詳細
    Route::get('/sections/{section}/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/sections/{section}/history/{attempt}', [HistoryController::class, 'show'])->name('history.show');
});
