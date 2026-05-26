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
        Schema::table('user_reading_progress', static function (Blueprint $table): void {
            $table->unsignedInteger('global_page_number')
                ->default(0)
                ->after('page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reading_progress', static function (Blueprint $table): void {
            $table->dropColumn('global_page_number');
        });
    }
};
