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
            $table->unsignedInteger('duration_seconds')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_sessions', static function (Blueprint $table): void {
            $table->unsignedSmallInteger('duration_seconds')->default(0)->change();
        });
    }
};
