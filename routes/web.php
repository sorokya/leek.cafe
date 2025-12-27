<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WelcomeController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::controller(App\Http\Controllers\AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('auth.show-login');
    Route::post('/login', 'login')->name('auth.store-login');
    Route::post('/logout', 'logout')->name('auth.logout');
    Route::get('/set-password', 'showSetPassword')->name('auth.show-set-password');
    Route::post('/set-password', 'setPassword')->name('auth.store-set-password');
});

Route::controller(ProfileController::class)->middleware('auth')->group(function () {
    Route::get('/settings', 'showSettings')->name('profile.show-settings');
    Route::post('/settings', 'updateSettings')->name('profile.update-settings');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts/{slug}', 'show')->name('posts.show');
    Route::get('/posts/{slug}/edit', 'edit')->middleware('auth')->name('posts.edit');
    Route::post('/posts/{slug}/upload-images', 'uploadImages')->middleware('auth')->name('posts.upload-images');
    Route::put('/posts/{slug}', 'update')->middleware('auth')->name('posts.update');
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');

Route::get('/img/{path}', ImageController::class)
    ->middleware('cache.headers:public;max_age=31536000;etag')
    ->name('image.serve')->where('path', '.*');
