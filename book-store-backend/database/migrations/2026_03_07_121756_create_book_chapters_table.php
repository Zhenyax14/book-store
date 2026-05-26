<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_chapters', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('volume_id')
                ->nullable()
                ->constrained('book_volumes')
                ->nullOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('title');
            $table->string('slug');
            $table->unsignedSmallInteger('reading_time_minutes')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['book_id', 'number']);
            $table->index(['book_id', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
