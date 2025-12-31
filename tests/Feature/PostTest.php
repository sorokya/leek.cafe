<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('guest can view posts index page', function () {
    get('/posts')->assertOk();
});

test('guest can view published post', function () {
    $content = Content::factory()->create([
        'slug' => 'test-post',
        'published' => true,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    get('/posts/test-post')->assertOk();
});

test('authenticated user can create post', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/posts/new')
        ->assertOk();
});

test('guest cannot create post', function () {
    get('/posts/new')->assertRedirect('/login');
});

test('authenticated user can store post', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/posts', [
            'title' => 'Test Post',
            'slug' => 'test-post',
            'body' => 'This is a test post body',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    expect(Post::count())->toBe(1);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can edit own post', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-post']);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->get('/posts/test-post/edit')
        ->assertOk();
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can update post', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-post', 'title' => 'Original Title']);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->put('/posts/test-post', [
            'title' => 'Updated Title',
            'slug' => 'test-post',
            'body' => 'Updated body',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    expect($content->fresh()->title)->toBe('Updated Title');
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can delete post', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-post']);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->delete('/posts/test-post')
        ->assertRedirect('/posts');

    expect(Post::count())->toBe(0);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
