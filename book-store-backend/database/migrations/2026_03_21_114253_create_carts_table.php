<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('carts', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('cart_items', static function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('type', 20);
            $table->unsignedInteger('reference_id');
            $table->string('title');
            $table->unsignedInteger('price');
            $table->char('currency', 3);
            $table->timestamps();

            $table->unique(['cart_id', 'type', 'reference_id']);
            $table->index(['cart_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
