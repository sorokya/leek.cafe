<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $posts = Post::query()
        ->whereNotNull('published_at')
        ->orderByDesc('published_at')
        ->with('user')
        ->take(10)
        ->get();
    return view('welcome', ['posts' => $posts]);
});

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
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');
