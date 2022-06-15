<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardUsController::class, 'index'])
        ->name('home');

    Route::get('/dashboard_us', [\App\Http\Controllers\DashboardUsController::class, 'index'])
        ->name('dashboard_us');

    Route::get('/dashboard_au', [\App\Http\Controllers\DashboardAuController::class, 'index'])
        ->name('dashboard_au');
});

require __DIR__.'/auth.php';
