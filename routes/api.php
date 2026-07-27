<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramMiniAppController;

Route::post('/telegram/login', [TelegramMiniAppController::class, 'login']);
