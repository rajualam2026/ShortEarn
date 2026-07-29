<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramMiniAppController;

Route::get('/debug', function () {
    return [
        'php' => PHP_VERSION,
        'app_env' => config('app.env'),
        'app_key' => config('app.key') ? 'OK' : 'MISSING',
        'bot_token' => config('shortearn.bot_token') ? 'OK' : 'MISSING',
    ];
});

Route::get('/', [TelegramMiniAppController::class, 'index'])->name('home');
Route::get('/mini-app', [TelegramMiniAppController::class, 'index'])->name('miniapp');

Route::post('/telegram/login', [TelegramMiniAppController::class, 'login'])->name('telegram.login');
Route::get('/dashboard', [TelegramMiniAppController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [TelegramMiniAppController::class, 'logout'])->name('logout');
