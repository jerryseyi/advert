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

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/uploads', [\App\Http\Controllers\UploadController::class, 'index'])->name('upload.index');

    Route::post('/{user}/uploads', [\App\Http\Controllers\UploadController::class, 'store'])->name('upload.store');
    Route::post('/{user}/uploads/verify', [\App\Http\Controllers\UploadVerificationController::class, 'store'])->name('upload.requirements');
    Route::patch('/{user}/uploads/{upload}', [\App\Http\Controllers\UploadController::class, 'update'])->name('upload.update');
    Route::post('/device/{device}/uploads/{upload}/exclude', [\App\Http\Controllers\ExcludeUploadController::class, 'store'])->name('exclude-upload.store');
    Route::post('/device/{device}/disconnect', [\App\Http\Controllers\DeviceStateController::class, 'disconnect'])->name('device.disconnect');

    Route::post('/uploads/{upload}/enable', [\App\Http\Controllers\UploadStatusController::class, 'store'])->name('upload.enable');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::middleware(['auth:api', 'is.admin'])->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');

});

