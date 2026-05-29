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
        Schema::table('bookmarks', static function (Blueprint $table): void {
            $table->foreignId('chapter_id')
                ->constrained('book_chapters')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookmarks', static function (Blueprint $table): void {
            $table->dropForeign('bookmarks_chapter_id_foreign');
            $table->dropColumn('chapter_id');
        });
    }
};
