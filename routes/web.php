<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MyVacancyApplicationController;
use App\Http\Controllers\MyVacancyController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VacancyApplicationController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\VerificationEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', fn() => to_route('vacancies.index'));

Route::resource('vacancies', VacancyController::class)
    ->only(['index', 'show']);

// redirect to custom auth controller
Route::get('login', fn() => to_route('auth.create'))
    ->name('login');

Route::resource('auth', AuthController::class)
    ->only(['create', 'store']);

Route::resource('reg', RegisterController::class)
    ->middleware('guest')
    ->only(['create', 'store']);

// redirect to custom auth controller
Route::delete('logout', fn() => to_route('auth.destroy'))
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::delete('auth', [AuthController::class, 'destroy'])
        ->name('auth.destroy');

    Route::middleware('verified')->group(function () {
        Route::resource('vacancies.application', VacancyApplicationController::class)
            ->only(['create', 'store']);

        Route::resource('my-vacancy-applications', MyVacancyApplicationController::class)
            ->only(['index', 'destroy']);

        Route::get(
            '/my-vacancy-applications/{my_vacancy_application}/download',
            [MyVacancyApplicationController::class, 'download']
        )->name('my-vacancy-applications.download');

        Route::resource('my-vacancy', MyVacancyController::class);

        // Download CV from application
        Route::get('my-vacancy/{my_vacancy}/download/{vacancy_application}', [MyVacancyController::class, 'download'])
            ->scopeBindings()
            ->name('my-vacancy.download');

        // Restore soft deleted vacancy
        Route::get('my-vacancy/{my_vacancy}/restore', [MyVacancyController::class, 'restore'])
            ->withTrashed()
            ->name('my-vacancy.restore');

        // Permanent delete vacancy with related application and CV files
        Route::delete('my-vacancy/{my_vacancy}/force_destroy', [MyVacancyController::class, 'forceDestroy'])
            ->withTrashed()
            ->name('my-vacancy.force_destroy');
    });
});

/*
|--------------------------------------------------------------------------
| The Email Verification routes
|--------------------------------------------------------------------------
|
| Here short description.
|
*/
Route::prefix('email')
    ->name('verification.')
    ->middleware(['auth'])
    ->controller(VerificationEmailController::class)
    ->group(function () {
        Route::get('verify', 'notice')
            ->name('notice');

        // The Email Verification Handler
        Route::get('verify/{id}/{hash}', 'verification')
            ->middleware('throttle:6,1')
            ->name('verify');

        // Resending The Verification Email
        Route::post('verification-notification', 'resending')
            ->middleware('throttle:6,1')
            ->name('send');
    });
