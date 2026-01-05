<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->tinyInteger('content_type')->default(0)->after('slug');
        });

        // Backfill existing rows using portable, set-based updates.
        DB::table('contents')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('posts')
                    ->whereColumn('posts.content_id', 'contents.id');
            })
            ->update(['content_type' => 1]);

        DB::table('contents')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('projects')
                    ->whereColumn('projects.content_id', 'contents.id');
            })
            ->update(['content_type' => 2]);

        DB::table('contents')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('thoughts')
                    ->whereColumn('thoughts.content_id', 'contents.id');
            })
            ->update(['content_type' => 3]);

        Schema::table('contents', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->unique(['content_type', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->dropUnique(['content_type', 'slug']);
            $table->unique('slug');
            $table->dropColumn('content_type');
        });
    }
};
