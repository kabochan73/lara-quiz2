<?php

namespace App\Providers;

use App\Services\Grading\ClaudeGradingService;
use App\Services\Grading\GradingService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // AI採点はClaude API(ClaudeGradingService)で行う。
        // テストやローカルでAPIを呼びたくないときはFakeGradingServiceに差し替え可能。
        $this->app->bind(GradingService::class, ClaudeGradingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 「home」という名前のルートを廃止したので、guestミドルウェアが
        // ログイン済みの人を/loginから追い返す先も明示的にカテゴリ画面にしておく。
        // (指定しないと '/' に飛ばされ、'/' は常に/loginへ戻すルートなので無限ループになる)
        RedirectIfAuthenticated::redirectUsing(fn () => route('categories.index'));
    }
}
