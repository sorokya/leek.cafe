<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Api\MediaStatusController;
use App\Http\Controllers\Api\MediaTypeController;
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

Route::middleware('auth')->group(function () {
    Route::post('/settings/media-statuses', [MediaStatusController::class, 'store'])->name('media-statuses.store');
    Route::put('/settings/media-statuses/{mediaStatus}', [MediaStatusController::class, 'update'])->name('media-statuses.update');
    Route::delete('/settings/media-statuses/{mediaStatus}', [MediaStatusController::class, 'destroy'])->name('media-statuses.destroy');

    Route::post('/settings/media-types', [MediaTypeController::class, 'store'])->name('media-types.store');
    Route::put('/settings/media-types/{mediaType}', [MediaTypeController::class, 'update'])->name('media-types.update');
    Route::delete('/settings/media-types/{mediaType}', [MediaTypeController::class, 'destroy'])->name('media-types.destroy');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index')->name('posts.index');
    Route::get('/posts/new', 'create')->middleware('auth')->name('posts.create');
    Route::post('/posts', 'store')->middleware('auth')->name('posts.store');
    Route::get('/posts/{slug}', 'show')->name('posts.show');
    Route::get('/posts/{slug}/edit', 'edit')->middleware('auth')->name('posts.edit');
    Route::get('/posts/{slug}/delete-confirm', 'deleteConfirm')->middleware('auth')->name('posts.delete-confirm');
    Route::delete('/posts/{slug}', 'destroy')->middleware('auth')->name('posts.destroy');
    Route::put('/posts/{slug}', 'update')->middleware('auth')->name('posts.update');
    Route::post('/posts/{slug}/upload-images', 'uploadImages')->middleware('auth')->name('posts.upload-images');
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');

Route::get('/img/{path}', ImageController::class)
    ->middleware('cache.headers:public;max_age=31536000;etag')
    ->name('image.serve')->where('path', '.*');
