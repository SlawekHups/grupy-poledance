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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('month'); // np. "05-2025"
            $table->decimal('amount', 8, 2)->nullable(); // kwota w zł
            $table->boolean('paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_link')->nullable(); // link do Przelewy24
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
