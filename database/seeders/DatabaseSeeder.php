<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\ContentType;
use App\Models\Content;
use App\Models\Project;
use App\Models\User;
use App\Visibility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'username' => 'richard',
            'name' => 'Richard Leek',
            'primary' => true,
        ]);

        DB::table('media_types')->insert([
            ['type' => 'Film', 'slug' => 'film'],
            ['type' => 'Series', 'slug' => 'series'],
            ['type' => 'Music', 'slug' => 'music'],
            ['type' => 'Book', 'slug' => 'book'],
            ['type' => 'Anime', 'slug' => 'anime'],
            ['type' => 'Manga', 'slug' => 'manga'],
            ['type' => 'Game', 'slug' => 'game'],
        ]);

        DB::table('media_statuses')->insert([
            ['status' => 'Planned', 'slug' => 'planned', 'icon' => 'heroicon-s-clock', 'color' => '#6B7280'],
            ['status' => 'In Progress', 'slug' => 'in-progress', 'icon' => 'heroicon-s-play', 'color' => '#3B82F6'],
            ['status' => 'Completed', 'slug' => 'completed', 'icon' => 'heroicon-s-check', 'color' => '#10B981'],
            ['status' => 'On Hold', 'slug' => 'on-hold', 'icon' => 'heroicon-s-pause', 'color' => '#F59E0B'],
            ['status' => 'Dropped', 'slug' => 'dropped', 'icon' => 'heroicon-s-trash', 'color' => '#EF4444'],
        ]);

        DB::table('metrics')->insert([
            ['user_id' => $user->id, 'name' => 'Wordle Score', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-chart-bar', 'color' => '#8B5CF6', 'min' => 1, 'max' => 6],
            ['user_id' => $user->id, 'name' => 'Sleep Hours', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-moon', 'color' => '#3B82F6', 'min' => null, 'max' => null],
            ['user_id' => $user->id, 'name' => 'Fasting Hours', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-sun', 'color' => '#F59E0B', 'min' => null, 'max' => null],
            ['user_id' => $user->id, 'name' => 'Mood', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-face-smile', 'color' => '#10B981', 'min' => 1, 'max' => 10],
        ]);

        DB::table('habits')->insert([
            ['user_id' => $user->id, 'name' => 'Exercise', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-heart', 'color' => '#EF4444'],
            ['user_id' => $user->id, 'name' => 'Meditation', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-brain', 'color' => '#8B5CF6'],
            ['user_id' => $user->id, 'name' => 'Reading', 'visibility' => Visibility::PUBLIC->value, 'icon' => 'heroicon-s-book-open', 'color' => '#3B82F6'],
        ]);

        Content::factory()
            ->count(3)
            ->for($user)
            ->create([
                'content_type' => ContentType::POST->value,
            ])
            ->each(function (Content $content): void {
                $content->post()->create();
            });

        Content::factory()
            ->count(5)
            ->for($user)
            ->create([
                'content_type' => ContentType::THOUGHT->value,
            ])
            ->each(function (Content $content): void {
                $content->thought()->create();
            });

        Content::factory()
            ->count(2)
            ->for($user)
            ->create([
                'content_type' => ContentType::PROJECT->value,
            ])
            ->each(function (Content $content): void {
                Project::factory()->create([
                    'content_id' => $content->id,
                ]);
            });
    }
}
