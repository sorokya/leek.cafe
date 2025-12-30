<?php

declare(strict_types=1);

use App\ImageRole;
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
        Schema::create('images', function (Blueprint $table): void {
            $table->id();
            $table->string('hash')->unique();
            $table->string('extension', 4);
            $table->timestamps();
        });

        Schema::create('content_images', function (Blueprint $table): void {
            $table->foreignId('content_id')->constrained()->cascadeOnDelete();
            $table->foreignId('image_id')->constrained('images')->cascadeOnDelete();
            $table->tinyInteger('role')->default(ImageRole::INLINE->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_images');
        Schema::dropIfExists('images');
    }
};
