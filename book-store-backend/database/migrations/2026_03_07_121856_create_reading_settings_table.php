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
        Schema::create('reading_settings', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('font_size')->default(16);
            $table->string('font_family', 50)->default('Georgia');
            $table->decimal('line_height', 3, 1)->default(1.6);

            $table->string('theme', 20);
            $table->unsignedTinyInteger('page_width')->default(70);

            $table->string('pagination_mode', 10)->default('page');
            $table->unsignedTinyInteger('words_per_page')->default(300);

            $table->timestamps();

            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_settings');
    }
};
