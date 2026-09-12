<?php

namespace App\Providers;

use App\Services\Grading\FakeGradingService;
use App\Services\Grading\GradingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // AI採点(Claude API)ができるまでの仮実装をつないでおく。
        // 実装したら、ここをClaudeGradingServiceなどに差し替えるだけでよい。
        $this->app->bind(GradingService::class, FakeGradingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
