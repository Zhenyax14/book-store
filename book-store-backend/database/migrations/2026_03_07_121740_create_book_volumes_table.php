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
        Schema::create('book_volumes', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'number']);
            $table->index('book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_volumes');
    }
};
