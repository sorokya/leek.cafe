<?php

declare(strict_types=1);

use App\Models\Content;
use App\Services\ContentRenderer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table): void {
            $table->text('rendered')->nullable()->after('body');
        });

        // Populate existing contents
        $renderer = resolve(ContentRenderer::class);
        Content::query()->chunkById(100, function (iterable $contents) use ($renderer): void {
            foreach ($contents as $content) {
                $content->rendered = $renderer->render($content->body);
                $content->save();
            }
        });

        // Make the column non-nullable
        Schema::table('contents', function (Blueprint $table): void {
            $table->text('rendered')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Content::query()->update(['rendered' => null]);
        Schema::table('contents', function (Blueprint $table): void {
            $table->dropColumn('rendered');
        });
    }
};
