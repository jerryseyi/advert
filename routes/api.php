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

   Route::post('/{user}/uploads', [\App\Http\Controllers\UploadController::class, 'store'])->name('upload.store');
   Route::patch('/{user}/uploads/{upload}', [\App\Http\Controllers\UploadController::class, 'update'])->name('upload.update');
   Route::post('/uploads/{upload}/exclude', []);

   Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

Route::middleware(['auth:api', 'is.admin'])->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');

});

