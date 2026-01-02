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
use function Pest\Laravel\get;
use function Pest\Laravel\withoutMiddleware;

pest()->use(RefreshDatabase::class);

beforeEach(function (): void {
    withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
});

test('guest can view posts index page', function (): void {
    get('/posts')->assertOk();
});

test('guest can view published post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'test-post',
        'visibility' => Visibility::PUBLIC->value,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    get('/posts/' . $content->slug)->assertOk();
});

test('authenticated user can create post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    actingAs($user)
        ->get('/posts/new')
        ->assertOk();
});

test('guest cannot create post', function (): void {
    get('/posts/new')->assertRedirect('/login');
});

test('authenticated user can store post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    actingAs($user)
        ->post('/posts', [
            'title' => 'Test Post',
            'slug' => 'test-post',
            'body' => 'This is a test post body',
            'visibility' => Visibility::PUBLIC->value,
        ])
        ->assertRedirect();

    expect(Post::count())->toBe(1);
});

test('authenticated user can edit own post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-post', 'user_id' => $user->id]);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->get('/posts/test-post/edit')
        ->assertOk();
});

test('authenticated user can update post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-post', 'title' => 'Original Title', 'user_id' => $user->id]);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->put('/posts/test-post', [
            'title' => 'Updated Title',
            'slug' => 'test-post',
            'body' => 'Updated body',
            'visibility' => Visibility::PUBLIC->value,
        ])
        ->assertRedirect();

    $content = $content->fresh();
    assert($content instanceof Content);

    expect($content->title)->toBe('Updated Title');
});

test('authenticated user can delete post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-post', 'user_id' => $user->id]);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->delete('/posts/' . $content->slug)
        ->assertRedirect('/posts');

    expect(Post::count())->toBe(0);
});
