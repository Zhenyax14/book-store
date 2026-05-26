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
        Schema::create('book_pages', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chapter_id')
                ->constrained('book_chapters')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->unsignedSmallInteger('global_number');
            $table->longText('content');
            $table->string('content_format', 10);
            $table->unsignedSmallInteger('word_count')->default(0);
            $table->timestamps();

            $table->unique(['chapter_id', 'number']);
            $table->index('global_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_pages');
    }
};
