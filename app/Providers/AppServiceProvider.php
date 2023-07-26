<?php

namespace App\Providers;

use App\Contracts\VacancyApplicationCvStorageInterface;
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
            VacancyApplicationCvStorageInterface::class,
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
