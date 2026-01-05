<?php

declare(strict_types=1);

use App\ContentType;
use App\Models\Content;
use App\Models\Post;
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

test('slugs can be reused across content types', function (): void {
    $user = User::factory()->create([
        'password' => '$argon2id$v=19$m=65536,t=4,p=1$ZkI5QjFPam84dGFKMlFEYQ$9NhqUNyjzlsaER+9lIDf2ERefBxJ6qY6JN6i34gSIB0',
    ]);

    $slug = 'shared-slug';

    $postContent = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => $slug,
        'content_type' => ContentType::POST,
        'visibility' => Visibility::PUBLIC->value,
    ]);
    Post::factory()->create(['content_id' => $postContent->id]);

    $projectContent = Content::factory()->create([
        'user_id' => $user->id,
        'slug' => $slug,
        'content_type' => ContentType::PROJECT,
        'visibility' => Visibility::PUBLIC->value,
    ]);
    Project::factory()->create([
        'content_id' => $projectContent->id,
        'url' => 'https://example.com',
    ]);

    get('/posts/' . $slug)->assertOk();
    get('/projects/' . $slug)->assertOk();

    actingAs($user)
        ->get('/posts/' . $slug . '/edit')
        ->assertOk();

    actingAs($user)
        ->get('/projects/' . $slug . '/edit')
        ->assertOk();
});
