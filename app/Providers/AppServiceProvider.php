<?php

namespace App\Providers;

use App\Interfaces\Repositories\AccessLogRepositoryInterface;
use App\Interfaces\Repositories\ShortLinkRepositoryInterface;
use App\Interfaces\Services\CacheServiceInterface;
use App\Interfaces\Services\RedirectionServiceInterface;
use App\Repositories\AccessLogRepository;
use App\Repositories\ShortLinkRepository;
use App\Services\CacheService;
use App\Services\RedirectionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ShortLinkRepositoryInterface::class,ShortLinkRepository::class);
        $this->app->bind(RedirectionServiceInterface::class, RedirectionService::class);
        $this->app->bind(CacheServiceInterface::class, CacheService::class);
        $this->app->bind(AccessLogRepositoryInterface::class, AccessLogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
