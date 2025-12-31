<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('user can complete full post creation flow', function () {
    $user = User::factory()->create();

    // Navigate to post creation page
    actingAs($user)
        ->get('/posts/new')
        ->assertOk()
        ->assertSee('New Post');

    // Submit post creation form
    actingAs($user)
        ->post('/posts', [
            'title' => 'My New Blog Post',
            'slug' => 'my-new-blog-post',
            'body' => '# Introduction\n\nThis is my first blog post!',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    // Verify post was created
    assertDatabaseHas('contents', [
        'title' => 'My New Blog Post',
        'slug' => 'my-new-blog-post',
    ]);

    // View the created post
    get('/posts/my-new-blog-post')
        ->assertOk()
        ->assertSee('My New Blog Post');
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can edit existing post', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'existing-post',
        'title' => 'Existing Post',
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    // Navigate to edit page
    actingAs($user)
        ->get('/posts/existing-post/edit')
        ->assertOk();

    // Update the post
    actingAs($user)
        ->put('/posts/existing-post', [
            'title' => 'Updated Post Title',
            'slug' => 'existing-post',
            'body' => 'Updated content',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    // Verify changes
    assertDatabaseHas('contents', [
        'slug' => 'existing-post',
        'title' => 'Updated Post Title',
    ]);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can view delete confirmation and delete post', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'post-to-delete',
        'title' => 'Post to Delete',
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    // View delete confirmation
    actingAs($user)
        ->get('/posts/post-to-delete/delete-confirm')
        ->assertOk()
        ->assertSee('Post to Delete');

    // Delete the post
    actingAs($user)
        ->delete('/posts/post-to-delete')
        ->assertRedirect('/posts');

    // Verify post is deleted
    expect(Post::count())->toBe(0);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
