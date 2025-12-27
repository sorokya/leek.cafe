<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
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
        ]);

        DB::table('content_types')->insert([
            ['type' => 'post'],
            ['type' => 'media'],
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

        Content::factory()
            ->count(5)
            ->for($user)
            ->create();
    }
}
