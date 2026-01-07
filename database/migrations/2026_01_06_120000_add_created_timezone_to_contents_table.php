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
        Schema::table('contents', function (Blueprint $table): void {
            $table->string('created_timezone', 64)->nullable()->after('user_id');
            $table->index('created_timezone');
        });

        $fallbackTimezone = Config::string('app.timezone', 'UTC');

        DB::table('contents')
            ->select(['id', 'user_id'])
            ->whereNull('created_timezone')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($fallbackTimezone): void {
                $userIds = collect($rows)
                    ->pluck('user_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $timezonesByUserId = DB::table('users')
                    ->whereIn('id', $userIds)
                    ->pluck('timezone', 'id');

                foreach ($rows as $row) {
                    $timezone = $timezonesByUserId[$row->user_id] ?? $fallbackTimezone;

                    DB::table('contents')
                        ->where('id', $row->id)
                        ->update(['created_timezone' => $timezone]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->dropIndex(['created_timezone']);
            $table->dropColumn('created_timezone');
        });
    }
};
