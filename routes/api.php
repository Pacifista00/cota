<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\FeedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/sensor-data/insert', [SensorController::class, 'store']);
Route::get('/sensor-data/latest', [SensorController::class, 'latest']);
Route::get('/sensor-data/history', [SensorController::class, 'history']);

Route::post('/feed/command', [FeedController::class, 'store']);
Route::get('/feed/status', [FeedController::class, 'status']);
Route::get('/feed/history', [FeedController::class, 'history']);
