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
        Schema::create('tags', static function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
        });

        Schema::create('book_tag', static function (Blueprint $table): void {
            $table->foreignId('book_id')
                ->constrained('books')
                ->cascadeOnDelete();
            $table->foreignId('tag_id')
                ->constrained('tags')
                ->cascadeOnDelete();

            $table->primary(['book_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_tag');
        Schema::dropIfExists('tags');
    }
};
