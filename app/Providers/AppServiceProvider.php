<?php

namespace App\Providers;

use App\Models\Kegiatan;
use App\Observers\KegiatanObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Kegiatan::observe(KegiatanObserver::class);
    }
}
