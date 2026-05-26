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
        Schema::table('reading_sessions', static function (Blueprint $table): void {
            $table->index(['book_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_sessions', static function (Blueprint $table): void {
            $table->dropIndex(['book_id', 'started_at']);
        });
    }
};
