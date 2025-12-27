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
        ]);

        DB::table('media_types')->insert([
            ['type' => 'film'],
            ['type' => 'series'],
            ['type' => 'music'],
            ['type' => 'book'],
            ['type' => 'anime'],
            ['type' => 'manga'],
            ['type' => 'game'],
        ]);

        DB::table('media_statuses')->insert([
            ['status' => 'planned'],
            ['status' => 'in-progress'],
            ['status' => 'completed'],
            ['status' => 'on-hold'],
            ['status' => 'dropped'],
        ]);

        Content::factory()
            ->count(5)
            ->for($user)
            ->create();
    }
}
