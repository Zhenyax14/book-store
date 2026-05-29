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
        Schema::create('annotations', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('page_id')
                ->constrained('book_pages')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('start_offset');
            $table->unsignedSmallInteger('end_offset');
            $table->text('selected_text');

            $table->string('color', 7)
                ->default('#FFFF00');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'page_id']);
            $table->index('page_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annotations');
    }
};
