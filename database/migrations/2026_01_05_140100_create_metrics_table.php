<?php

declare(strict_types=1);

use App\Visibility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('visibility')->default(Visibility::PRIVATE->value);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->decimal('min', 8, 2)->nullable();
            $table->decimal('max', 8, 2)->nullable();
            $table->string('options')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'name']);
            $table->index(['user_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
