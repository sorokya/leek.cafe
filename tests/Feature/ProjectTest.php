<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guest can view projects index page', function () {
    get('/projects')->assertOk();
});

test('guest can view published project', function () {
    $content = Content::factory()->create([
        'slug' => 'test-project',
        'published' => true,
    ]);
    Project::factory()->create(['content_id' => $content->id]);

    get('/projects/test-project')->assertOk();
});

test('authenticated user can create project', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get('/projects/new')
        ->assertOk();
});

test('guest cannot create project', function () {
    get('/projects/new')->assertRedirect('/login');
});

test('authenticated user can store project', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'body' => 'This is a test project body',
            'url' => 'https://example.com',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    expect(Project::count())->toBe(1);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can edit own project', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-project']);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->get('/projects/test-project/edit')
        ->assertOk();
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can update project', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-project', 'title' => 'Original Title']);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->put('/projects/test-project', [
            'title' => 'Updated Title',
            'slug' => 'test-project',
            'body' => 'Updated body',
            'url' => 'https://example.com',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    expect($content->fresh()->title)->toBe('Updated Title');
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('authenticated user can delete project', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create(['slug' => 'test-project']);
    Project::factory()->create(['content_id' => $content->id]);

    actingAs($user)
        ->delete('/projects/test-project')
        ->assertRedirect('/projects');

    expect(Project::count())->toBe(0);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
