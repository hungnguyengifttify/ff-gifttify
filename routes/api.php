<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/register_tracking_number', [\App\Http\Controllers\TrackingController::class, 'registerTrackingNumber'])
    ->name('register_tracking_number');

Route::get('/get_track_info', [\App\Http\Controllers\TrackingController::class, 'getTrackInfo'])
    ->name('get_track_info');