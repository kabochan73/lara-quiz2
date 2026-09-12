<?php

namespace App\Providers;

use App\Services\Grading\ClaudeGradingService;
use App\Services\Grading\GradingService;
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
        //
    }
}
