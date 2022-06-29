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

Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->name('home');

Route::get('/dashboard_test/{store}', [\App\Http\Controllers\DashboardTestController::class, 'report_detail'])
    ->name('dashboard_test');


Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/report_detail/{store}', [\App\Http\Controllers\DashboardController::class, 'report_detail'])
    ->name('report_detail');


require __DIR__.'/auth.php';
