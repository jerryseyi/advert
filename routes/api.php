<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::get('/uploads', [\App\Http\Controllers\UploadController::class, 'index'])->name('upload.index');

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/{user}/stats', [\App\Http\Controllers\StatusController::class, 'stats']);
    Route::post('/{user}/uploads', [\App\Http\Controllers\UploadController::class, 'store'])->name('upload.store');
    Route::post('/{user}/uploads/verify', [\App\Http\Controllers\UploadVerificationController::class, 'store'])->name('upload.requirements');
    Route::post('/{user}/uploads/{upload}', [\App\Http\Controllers\UploadController::class, 'update'])->name('upload.update');
    Route::post('/device/{device}/uploads/{upload}/exclude', [\App\Http\Controllers\ExcludeUploadController::class, 'store'])->name('exclude-upload.store');
    Route::post('/device/{device}/disconnect', [\App\Http\Controllers\DeviceStateController::class, 'disconnect'])->name('device.disconnect');

    Route::delete('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::middleware(['auth:api', 'is.admin'])->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('users', [\App\Http\Controllers\UsersController::class, 'index']);
    Route::patch('/users/{user}', [\App\Http\Controllers\UsersController::class, 'update']);
    Route::delete('/users/{user}', [\App\Http\Controllers\UsersController::class, 'destroy']);

    Route::get('/devices', [\App\Http\Controllers\DeviceController::class, 'index']);
    Route::post('/devices', [\App\Http\Controllers\DeviceController::class, 'store']);
    Route::get('/devices/{device}', [\App\Http\Controllers\DeviceController::class, 'show']);
    Route::patch('/devices/{device}', [\App\Http\Controllers\DeviceController::class, 'update']);
    Route::delete('/devices/{device}', [\App\Http\Controllers\DeviceController::class, 'destroy']);
    Route::patch('/devices/{device}/user', [\App\Http\Controllers\UserDeviceController::class, 'update']);

    Route::post('/devices/{device}/uploads', [\App\Http\Controllers\DeviceUploadsController::class, 'store'])->withoutMiddleware(['is.admin']);
    Route::get('/devices/{device}/uploads', [\App\Http\Controllers\DeviceUploadsController::class, 'index'])->withoutMiddleware(['is.admin']);
    Route::get('/devices/{device}/uploads/{upload}', [\App\Http\Controllers\DeviceUploadsController::class, 'update']);
    Route::get('/devices/{device}/stats', [\App\Http\Controllers\StatusController::class, 'index']);

    Route::post('/uploads/{upload}/enable', [\App\Http\Controllers\UploadStatusController::class, 'store'])->name('upload.enable');
});

