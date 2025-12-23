<?php

use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(App\Http\Controllers\AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('auth.show-login');
    Route::post('/login', 'login')->name('auth.store-login');
    Route::get('/set-password', 'showSetPassword')->name('auth.show-set-password');
    Route::post('/set-password', 'setPassword')->name('auth.store-set-password');
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');
