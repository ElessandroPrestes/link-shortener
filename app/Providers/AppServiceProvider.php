<?php

namespace App\Providers;

use App\Models\ShortLink;
use App\Observers\ShortLinkObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        ShortLink::observe(ShortLinkObserver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
