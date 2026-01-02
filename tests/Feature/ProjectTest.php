<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Project;
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

test('guest can view projects index page', function (): void {
    get('/projects')->assertOk();
});

test('guest can view published project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create([
        'slug' => 'test-project',
        'visibility' => Visibility::PUBLIC->value,
        'user_id' => $user->id,
    ]);
    Project::factory()->create(['content_id' => $content->id]);

    get('/projects/test-project')->assertOk();
});

test('authenticated user can create project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    actingAs($user)
        ->get('/projects/new')
        ->assertOk();
});

test('guest cannot create project', function (): void {
    get('/projects/new')->assertRedirect('/login');
});

test('authenticated user can store project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    actingAs($user)
        ->post('/projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'body' => 'This is a test project body',
            'url' => 'https://example.com',
            'visibility' => Visibility::PUBLIC->value,
        ])
        ->assertRedirect();

    expect(Project::count())->toBe(1);
});

test('authenticated user can edit own project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-project', 'user_id' => $user->id]);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->get('/projects/test-project/edit')
        ->assertOk();
});

test('authenticated user can update project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-project', 'title' => 'Original Title', 'user_id' => $user->id]);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->put('/projects/test-project', [
            'title' => 'Updated Title',
            'slug' => 'test-project',
            'body' => 'Updated body',
            'url' => 'https://example.com',
            'visibility' => Visibility::PUBLIC->value,
        ])
        ->assertRedirect();

    $content = $content->fresh();
    assert($content instanceof Content);

    expect($content->title)->toBe('Updated Title');
});

test('authenticated user can delete project', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);
    $content = Content::factory()->create(['slug' => 'test-project', 'user_id' => $user->id]);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->delete('/projects/test-project')
        ->assertRedirect('/projects');

    expect(Project::count())->toBe(0);
});
