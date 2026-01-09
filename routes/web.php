<?php

declare(strict_types=1);

use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\MediaStatusController;
use App\Http\Controllers\Api\MediaTypeController;
use App\Http\Controllers\Api\MetricController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\ThoughtsController;
use App\Http\Controllers\UserDayController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserProfileController::class, 'home'])->name('home');
Route::get('/sitemap.xml', SiteMapController::class)->name('sitemap');
Route::feeds();
Route::get('/health', fn () => response()->json(['status' => 'ok']))->name('health');

Route::controller(App\Http\Controllers\AuthController::class)->group(function (): void {
    Route::get('/login', 'showLogin')->name('auth.show-login');
    Route::post('/login', 'login')->name('auth.store-login');
    Route::post('/logout', 'logout')->name('auth.logout');
    Route::get('/set-password', 'showSetPassword')->name('auth.show-set-password');
    Route::post('/set-password', 'setPassword')->name('auth.store-set-password');
});

Route::controller(ProfileController::class)->middleware('auth')->group(function (): void {
    Route::get('/settings', 'showSettings')->name('profile.show-settings');
    Route::post('/settings', 'updateSettings')->name('profile.update-settings');
});

Route::controller(UserProfileController::class)->group(function (): void {
    Route::get('/user/{user:username}', 'show')->name('user.profile');
    Route::get('/user/{user:username}/{date}', 'showDate')
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('user.profile.date');
    Route::get('/user/{user:username}/{date}/day', 'dayFragment')
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('user.profile.day-fragment');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/settings/media-statuses', [MediaStatusController::class, 'store'])->name('media-statuses.store');
    Route::put('/settings/media-statuses/{mediaStatus}', [MediaStatusController::class, 'update'])->name('media-statuses.update');
    Route::delete('/settings/media-statuses/{mediaStatus}', [MediaStatusController::class, 'destroy'])->name('media-statuses.destroy');

    Route::post('/settings/media-types', [MediaTypeController::class, 'store'])->name('media-types.store');
    Route::put('/settings/media-types/{mediaType}', [MediaTypeController::class, 'update'])->name('media-types.update');
    Route::delete('/settings/media-types/{mediaType}', [MediaTypeController::class, 'destroy'])->name('media-types.destroy');

    Route::post('/settings/metrics', [MetricController::class, 'store'])->name('metrics.store');
    Route::put('/settings/metrics/{metric}', [MetricController::class, 'update'])->name('metrics.update');
    Route::delete('/settings/metrics/{metric}', [MetricController::class, 'destroy'])->name('metrics.destroy');

    Route::post('/settings/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::put('/settings/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
    Route::delete('/settings/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

    Route::post('/user/{user:username}/{date}/metrics', [UserDayController::class, 'storeMetrics'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('user.day.metrics.store');
    Route::post('/user/{user:username}/{date}/habits', [UserDayController::class, 'storeHabits'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('user.day.habits.store');

    Route::post('/user/{user:username}/{date}/day', [UserDayController::class, 'store'])
        ->where('date', '\\d{4}-\\d{2}-\\d{2}')
        ->name('user.day.store');

    Route::post('/upload-images', [ImageController::class, 'upload'])->name('upload-images');
});

Route::controller(PostController::class)->group(function (): void {
    Route::get('/posts', 'index')->name('posts.index');
    Route::get('/posts/new', 'create')->middleware('auth')->name('posts.create');
    Route::post('/posts', 'store')->middleware('auth')->name('posts.store');
    Route::get('/posts/{slug}', 'show')->name('posts.show');
    Route::get('/posts/{slug}/edit', 'edit')->middleware('auth')->name('posts.edit');
    Route::get('/posts/{slug}/delete-confirm', 'deleteConfirm')->middleware('auth')->name('posts.delete-confirm');
    Route::delete('/posts/{slug}', 'destroy')->middleware('auth')->name('posts.destroy');
    Route::put('/posts/{slug}', 'update')->middleware('auth')->name('posts.update');
});

Route::controller(ProjectController::class)->group(function (): void {
    Route::get('/projects', 'index')->name('projects.index');
    Route::get('/projects/new', 'create')->middleware('auth')->name('projects.create');
    Route::post('/projects', 'store')->middleware('auth')->name('projects.store');
    Route::get('/projects/{slug}', 'show')->name('projects.show');
    Route::get('/projects/{slug}/edit', 'edit')->middleware('auth')->name('projects.edit');
    Route::get('/projects/{slug}/delete-confirm', 'deleteConfirm')->middleware('auth')->name('projects.delete-confirm');
    Route::delete('/projects/{slug}', 'destroy')->middleware('auth')->name('projects.destroy');
    Route::put('/projects/{slug}', 'update')->middleware('auth')->name('projects.update');
});

Route::controller(ThoughtsController::class)->group(function (): void {
    Route::get('/thoughts', 'index')->name('thoughts.index');
    Route::get('/thoughts/{slug}/fragments/edit', 'editFragment')->middleware('auth')->name('thoughts.fragments.edit');
    Route::get('/thoughts/{slug}/fragments/view', 'viewFragment')->name('thoughts.fragments.view');
    Route::get('/thoughts/{slug}', 'show')->name('thoughts.show');
    Route::post('/thoughts', 'store')->middleware('auth')->name('thoughts.store');
    Route::put('/thoughts/{slug}', 'update')->middleware('auth')->name('thoughts.update');
    Route::delete('/thoughts/{slug}', 'destroy')->middleware('auth')->name('thoughts.destroy');
});

Route::controller(MediaController::class)->group(function (): void {
    Route::get('/media', 'index')->name('media.index');
});

Route::post('/theme/toggle', ThemeController::class)->name('theme.toggle');

Route::controller(ImageController::class)->group(function (): void {
    Route::get('/img/{hash}/thumbnail', 'serveThumbnail')
        ->name('image.serve-thumbnail')->where('hash', '.*');
    Route::get('/img/{hash}', 'serve')
        ->name('image.serve')->where('hash', '.*');
});
