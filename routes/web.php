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

Route::get('/dashboard_test', [\App\Http\Controllers\DashboardTestController::class, 'index'])
    ->name('dashboard_test');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/report_detail/{store}', [\App\Http\Controllers\DashboardController::class, 'report_detail'])
    ->name('report_detail');

Route::get('/ads_creative', [\App\Http\Controllers\DashboardController::class, 'ads_creative'])
    ->name('ads_creative');

Route::get('/get_image_links', [\App\Http\Controllers\ToolsController::class, 'get_image_links'])
    ->name('get_image_links');

Route::get('/order_management', [\App\Http\Controllers\OrderManagementController::class, 'list'])
    ->name('order_management');

require __DIR__.'/auth.php';
