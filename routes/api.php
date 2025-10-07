<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FeedScheduleController;
use App\Http\Controllers\PondController;
use App\Http\Controllers\NotificationController;

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

    Route::get('/sensor-data/history', [SensorController::class, 'history']);

    // Route::post('/feed/command', [FeedController::class, 'store']);
    // Route::get('/feed/status', [FeedController::class, 'status']);

    Route::get('/feed/history', [FeedController::class, 'history']);

    // Feed Schedule Management Routes
    Route::prefix('feed-schedule')->group(function () {
        Route::get('/', [FeedScheduleController::class, 'index']);
        Route::get('/active', [FeedScheduleController::class, 'active']);
        Route::get('/{id}', [FeedScheduleController::class, 'show']);
        Route::post('/create', [FeedScheduleController::class, 'store']);
        Route::put('/{id}', [FeedScheduleController::class, 'update']);
        Route::delete('/{id}', [FeedScheduleController::class, 'destroy']);
        Route::patch('/{id}/activate', [FeedScheduleController::class, 'activate']);
        Route::patch('/{id}/deactivate', [FeedScheduleController::class, 'deactivate']);
        
        // Legacy routes for backward compatibility
        Route::post('/insert', [FeedScheduleController::class, 'store']);
        Route::put('/update/{id}', [FeedScheduleController::class, 'update']);
        Route::delete('/delete/{id}', [FeedScheduleController::class, 'destroy']);
    });

    Route::post('/pond/store', [PondController::class, 'store']);
    Route::put('/pond/update/{id}', [PondController::class, 'update']);
    Route::delete('/pond/delete/{id}', [PondController::class, 'destroy']);

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/statistics', [NotificationController::class, 'statistics']);
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });
});

Route::get('/feed/ready', [FeedController::class, 'siap']);
Route::get('/feed/give', [FeedController::class, 'beriPakan']);
Route::get('/feed/give/{id}', [FeedController::class, 'beriPakanTerjadwal']);
Route::get('/feed/status', [FeedController::class, 'checkFeedStatus']);
Route::get('/sensor-data/latest', [SensorController::class, 'latest']);
Route::post('/sensor-data/insert', [SensorController::class, 'store']);
Route::get('/sensor-data/quality', [SensorController::class, 'dataQuality']);
