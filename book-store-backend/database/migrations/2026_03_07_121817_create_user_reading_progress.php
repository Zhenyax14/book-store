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
        Schema::create('user_reading_progress', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chapter_id')
                ->nullable()
                ->constrained('book_chapters')
                ->nullOnDelete();
            $table->foreignId('page_id')
                ->nullable()
                ->constrained('book_pages')
                ->nullOnDelete();

            $table->unsignedTinyInteger('scroll_position')->default(0);

            $table->decimal('completion_percentage', 5, 2)->default(0);

            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_finished')->default(false);
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
            $table->index(['user_id', 'last_read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reading_progress');
    }
};
