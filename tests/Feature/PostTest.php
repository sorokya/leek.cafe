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

test('guest can view unlisted post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'unlisted-post',
        'visibility' => Visibility::UNLISTED->value,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    get('/posts/' . $content->slug)->assertOk();
});

test('guest cannot view private post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'private-post',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    get('/posts/' . $content->slug)->assertNotFound();
});

test('guest post index only shows public posts', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $public = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'public-post',
        'title' => 'Public Post',
        'visibility' => Visibility::PUBLIC->value,
    ]);
    Post::factory()->create(['content_id' => $public->id]);

    $unlisted = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'unlisted-post-index',
        'title' => 'Unlisted Post',
        'visibility' => Visibility::UNLISTED->value,
    ]);
    Post::factory()->create(['content_id' => $unlisted->id]);

    $private = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'private-post-index',
        'title' => 'Private Post',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $private->id]);

    get('/posts')
        ->assertOk()
        ->assertSee('/posts/' . $public->slug)
        ->assertDontSee('/posts/' . $unlisted->slug)
        ->assertDontSee('/posts/' . $private->slug);
});

test("authenticated user cannot view another user's private post", function (): void {
    $owner = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $otherUser = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $content = Content::factory()->create([
        'user_id' => $owner->id,
        'slug' => 'private-post-foreign',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($otherUser)
        ->get('/posts/' . $content->slug)
        ->assertNotFound();
});

test('authenticated user can view own private post', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => 'private-post-own',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->get('/posts/' . $content->slug)
        ->assertOk();
});

test("authenticated post index shows user's own non-public posts but not other users' non-public posts", function (): void {
    $viewer = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $otherUser = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $viewerPrivate = Content::factory()->create([
        'user_id' => $viewer->id,
        'slug' => 'viewer-private',
        'title' => 'Viewer Private',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $viewerPrivate->id]);

    $otherPublic = Content::factory()->create([
        'user_id' => $otherUser->id,
        'slug' => 'other-public',
        'title' => 'Other Public',
        'visibility' => Visibility::PUBLIC->value,
    ]);
    Post::factory()->create(['content_id' => $otherPublic->id]);

    $otherUnlisted = Content::factory()->create([
        'user_id' => $otherUser->id,
        'slug' => 'other-unlisted',
        'title' => 'Other Unlisted',
        'visibility' => Visibility::UNLISTED->value,
    ]);
    Post::factory()->create(['content_id' => $otherUnlisted->id]);

    $otherPrivate = Content::factory()->create([
        'user_id' => $otherUser->id,
        'slug' => 'other-private',
        'title' => 'Other Private',
        'visibility' => Visibility::PRIVATE->value,
    ]);
    Post::factory()->create(['content_id' => $otherPrivate->id]);

    actingAs($viewer)
        ->get('/posts')
        ->assertOk()
        ->assertSee('/posts/' . $viewerPrivate->slug)
        ->assertSee('/posts/' . $otherPublic->slug)
        ->assertDontSee('/posts/' . $otherUnlisted->slug)
        ->assertDontSee('/posts/' . $otherPrivate->slug);
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
