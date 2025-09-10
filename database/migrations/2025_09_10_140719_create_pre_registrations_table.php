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
        Schema::create('pre_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->text('notes')->nullable(); // Opcjonalne notatki od administratora
            $table->timestamps();
            
            $table->index(['token', 'expires_at', 'used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_registrations');
    }
};
