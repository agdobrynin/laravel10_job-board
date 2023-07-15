<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MyVacancyApplicationController;
use App\Http\Controllers\VacancyApplicationController;
use App\Http\Controllers\VacancyController;
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
Route::get('/', fn () => to_route('vacancies.index'));

Route::resource('vacancies', VacancyController::class)
    ->only(['index', 'show']);

// redirect to custom auth controller
Route::get('login', fn() => to_route('auth.create'))
    ->name('login');

Route::resource('auth', AuthController::class)
    ->only(['create', 'store']);
// redirect to custom auth controller
Route::delete('logout', fn() => to_route('auth.destroy'))
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::delete('auth', [AuthController::class, 'destroy'])
        ->name('auth.destroy');

    Route::resource('vacancies.application', VacancyApplicationController::class)
        ->only(['create', 'store']);

    Route::resource('my-vacancy-applications', MyVacancyApplicationController::class)
        ->only(['index', 'destroy']);
});
