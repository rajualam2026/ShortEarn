<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramMiniAppController;
Route::get('/', function () {
    return 'Laravel is working';
});
Route::get('/', [TelegramMiniAppController::class, 'index'])->name('home');
Route::get('/mini-app', [TelegramMiniAppController::class, 'index'])->name('miniapp');

Route::post('/telegram/login', [TelegramMiniAppController::class, 'login'])->name('telegram.login');
Route::get('/dashboard', [TelegramMiniAppController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [TelegramMiniAppController::class, 'logout'])->name('logout');
