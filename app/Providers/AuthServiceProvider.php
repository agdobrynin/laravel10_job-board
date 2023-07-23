<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Policies\VacancyApplicationPolicy;
use App\Policies\VacancyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Vacancy::class => VacancyPolicy::class,
        VacancyApplication::class => VacancyApplicationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
