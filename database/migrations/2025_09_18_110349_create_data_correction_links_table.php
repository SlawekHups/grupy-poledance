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
        Schema::create('data_correction_links', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->datetime('expires_at');
            $table->boolean('used')->default(false);
            $table->datetime('used_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('allowed_fields')->nullable(); // Pola które użytkownik może edytować
            $table->timestamps();
            
            $table->index(['token', 'used']);
            $table->index(['user_id', 'used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_correction_links');
    }
};
