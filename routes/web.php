<?php

use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');
