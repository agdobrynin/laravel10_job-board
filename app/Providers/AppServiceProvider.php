<?php

namespace App\Providers;

use App\Services\VacancyApplicationCvStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            VacancyApplicationCvStorage::class,
            fn() => new VacancyApplicationCvStorage(Storage::disk('cv'))
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
