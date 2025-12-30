<?php

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
        Schema::create('media_types', function (Blueprint $table): void {
            $table->id();
            $table->string('type')->unique();
            $table->string('slug')->unique();
        });

        Schema::create('media_statuses', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
        });

        Schema::create('media', function (Blueprint $table): void {
            $table->foreignId('content_id')->constrained('contents')->cascadeOnDelete();
            $table->foreignId('media_type_id')->constrained('media_types')->restrictOnDelete();
            $table->foreignId('media_status_id')->constrained('media_statuses')->restrictOnDelete();
            $table->decimal('rating', 3, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_statuses');
        Schema::dropIfExists('media_types');
    }
};
