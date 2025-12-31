<?php

declare(strict_types=1);

use App\Models\Content;
use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

test('user can complete full project creation flow', function () {
    $user = User::factory()->create();

    // Navigate to project creation page
    actingAs($user)
        ->get('/projects/new')
        ->assertOk()
        ->assertSee('New Project');

    // Submit project creation form
    actingAs($user)
        ->post('/projects', [
            'title' => 'My Awesome Project',
            'slug' => 'my-awesome-project',
            'body' => '## Overview\n\nThis project is amazing!',
            'url' => 'https://github.com/example/project',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    // Verify project was created
    assertDatabaseHas('contents', [
        'title' => 'My Awesome Project',
        'slug' => 'my-awesome-project',
    ]);

    assertDatabaseHas('projects', [
        'url' => 'https://github.com/example/project',
    ]);

    // View the created project
    get('/projects/my-awesome-project')
        ->assertOk()
        ->assertSee('My Awesome Project');
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can edit existing project', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'existing-project',
        'title' => 'Existing Project',
    ]);
    Project::factory()->create([
        'content_id' => $content->id,
        'url' => 'https://example.com/old',
    ]);

    // Navigate to edit page
    actingAs($user)
        ->get('/projects/existing-project/edit')
        ->assertOk();

    // Update the project
    actingAs($user)
        ->put('/projects/existing-project', [
            'title' => 'Updated Project Title',
            'slug' => 'existing-project',
            'body' => 'Updated project description',
            'url' => 'https://example.com/new',
            'visibility' => 'public',
        ])
        ->assertRedirect();

    // Verify changes
    assertDatabaseHas('contents', [
        'slug' => 'existing-project',
        'title' => 'Updated Project Title',
    ]);

    assertDatabaseHas('projects', [
        'url' => 'https://example.com/new',
    ]);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can view delete confirmation and delete project', function () {
    $user = User::factory()->create();
    $content = Content::factory()->create([
        'slug' => 'project-to-delete',
        'title' => 'Project to Delete',
    ]);
    Project::factory()->create(['content_id' => $content->id]);

    // View delete confirmation
    actingAs($user)
        ->get('/projects/project-to-delete/delete-confirm')
        ->assertOk()
        ->assertSee('Project to Delete');

    // Delete the project
    actingAs($user)
        ->delete('/projects/project-to-delete')
        ->assertRedirect('/projects');

    // Verify project is deleted
    expect(Project::count())->toBe(0);
})->uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
