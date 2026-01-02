<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Post;
use App\Models\User;
use App\Visibility;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutMiddleware;

pest()->use(RefreshDatabase::class);

beforeEach(function (): void {
    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
});

test('user can complete full post creation flow', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

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
            'visibility' => Visibility::PUBLIC->value,
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
});

test('user can edit existing post', function (): void {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'existing-post',
        'title' => 'Existing Post',
        'user_id' => $user->id,
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
            'visibility' => Visibility::PUBLIC->value,
        ])
        ->assertRedirect();

    // Verify changes
    assertDatabaseHas('contents', [
        'slug' => 'existing-post',
        'title' => 'Updated Post Title',
    ]);
});

test('user can view delete confirmation and delete post', function (): void {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'post-to-delete',
        'title' => 'Post to Delete',
        'user_id' => $user->id,
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
});
