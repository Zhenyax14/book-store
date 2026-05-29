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
        Schema::create('bookmarks', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('page_id')
                ->constrained('book_pages')
                ->cascadeOnDelete();
            $table->string('label')
                ->nullable();
            $table->string('color', 7)
                ->default('#FFD700');
            $table->timestamps();

            $table->index(['user_id', 'book_id']);
            $table->unique(['user_id', 'page_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
};
