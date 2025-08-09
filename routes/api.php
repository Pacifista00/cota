<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FeedScheduleController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/sensor-data/latest', [SensorController::class, 'latest']);
    Route::post('/sensor-data/insert', [SensorController::class, 'store']);
    Route::get('/sensor-data/history', [SensorController::class, 'history']);

    // Route::post('/feed/command', [FeedController::class, 'store']);
    // Route::get('/feed/status', [FeedController::class, 'status']);

    Route::get('/feed/give', [FeedController::class, 'beriPakan']);
    Route::post('/feed/give/{id}', [FeedController::class, 'beriPakanTerjadwal']);
    Route::get('/feed/ready', [FeedController::class, 'siap']);
    Route::get('/feed/history', [FeedController::class, 'history']);

    Route::post('/feed-schedule/insert', [FeedScheduleController::class, 'store']);
    Route::put('/feed-schedule/update/{id}', [FeedScheduleController::class, 'update']);
    Route::delete('/feed-schedule/delete/{id}', [FeedScheduleController::class, 'destroy']);

    Route::post('/pond/store', [PondController::class, 'store']);
    Route::put('/pond/update/{id}', [PondController::class, 'update']);
    Route::delete('/pond/delete/{id}', [PondController::class, 'destroy']);
});
