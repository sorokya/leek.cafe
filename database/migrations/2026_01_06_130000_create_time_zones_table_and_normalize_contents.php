<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_zones', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 64)->unique();
        });

        Schema::table('contents', function (Blueprint $table): void {
            $table->foreignId('created_timezone_id')
                ->nullable()
                ->constrained('time_zones')
                ->after('created_timezone');

            $table->index('created_timezone_id');
        });

        $fallbackTimezone = Config::string('app.timezone', 'UTC');

        $contentZones = DB::table('contents')
            ->whereNotNull('created_timezone')
            ->distinct()
            ->orderBy('created_timezone')
            ->pluck('created_timezone')
            ->filter(fn ($v): bool => is_string($v) && $v !== '')
            ->values();

        $userZones = DB::table('users')
            ->whereNotNull('timezone')
            ->distinct()
            ->orderBy('timezone')
            ->pluck('timezone')
            ->filter(fn ($v): bool => is_string($v) && $v !== '')
            ->values();

        $zoneNames = $contentZones
            ->merge($userZones)
            ->push($fallbackTimezone)
            ->unique()
            ->values()
            ->all();

        foreach (array_chunk($zoneNames, 500) as $chunk) {
            DB::table('time_zones')->insertOrIgnore(
                array_map(fn (string $name): array => ['name' => $name], $chunk),
            );
        }

        /** @var array<string, int> $zoneIdsByName */
        $zoneIdsByName = DB::table('time_zones')->pluck('id', 'name')->all();

        DB::table('contents')
            ->select(['id', 'created_timezone'])
            ->whereNull('created_timezone_id')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($zoneIdsByName, $fallbackTimezone): void {
                foreach ($rows as $row) {
                    $name = is_string($row->created_timezone) && $row->created_timezone !== ''
                        ? $row->created_timezone
                        : $fallbackTimezone;

                    $zoneId = $zoneIdsByName[$name] ?? null;

                    if (! is_int($zoneId)) {
                        continue;
                    }

                    DB::table('contents')
                        ->where('id', $row->id)
                        ->update(['created_timezone_id' => $zoneId]);
                }
            });

        Schema::table('contents', function (Blueprint $table): void {
            $table->dropIndex(['created_timezone']);
            $table->dropColumn('created_timezone');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->string('created_timezone', 64)->nullable()->after('user_id');
            $table->index('created_timezone');
        });

        $fallbackTimezone = Config::string('app.timezone', 'UTC');

        $zoneNamesById = DB::table('time_zones')->pluck('name', 'id');

        DB::table('contents')
            ->select(['id', 'created_timezone_id'])
            ->whereNull('created_timezone')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($zoneNamesById, $fallbackTimezone): void {
                foreach ($rows as $row) {
                    $name = $zoneNamesById[$row->created_timezone_id] ?? $fallbackTimezone;

                    DB::table('contents')
                        ->where('id', $row->id)
                        ->update(['created_timezone' => $name]);
                }
            });

        Schema::table('contents', function (Blueprint $table): void {
            $table->dropIndex(['created_timezone_id']);
            $table->dropConstrainedForeignId('created_timezone_id');
        });

        Schema::dropIfExists('time_zones');
    }
};
