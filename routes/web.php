<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\FeedScheduleController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\AuthWebController;


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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', [AuthWebController::class, 'loginForm'])->name('login');
Route::get('/register', [AuthWebController::class, 'registerForm']);

Route::post('/register', [AuthWebController::class, 'register']);
Route::post('/login', [AuthWebController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout']);

    Route::get('/', [MainController::class, 'index']);
    Route::get('/jadwal', [MainController::class, 'jadwal']);
    Route::get('/riwayat/sensor', [MainController::class, 'riwayatSensor']);
    Route::get('/riwayat/pakan', [MainController::class, 'riwayatPakan']);

    Route::post('/beri-pakan', [FeedController::class, 'beriPakan']);

    Route::post('/jadwal/store', [FeedScheduleController::class, 'store']);
    Route::put('/jadwal/update/{id}', [FeedScheduleController::class, 'update']);
    Route::delete('/jadwal/delete/{id}', [FeedScheduleController::class, 'destroy']);
});

