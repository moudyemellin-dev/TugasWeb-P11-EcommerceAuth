<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->cascadeOnDelete();

    $table->string('order_number')->unique();
    $table->decimal('total_amount', 12, 2)->default(0);
    $table->enum('status', ['pending', 'paid', 'processed', 'completed', 'cancelled'])
        ->default('pending');
    $table->timestamp('order_date')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
