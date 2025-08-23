<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_email')->comment('Email użytkownika którego hasło zostało zresetowane');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade')->comment('ID administratora który wykonał reset');
            $table->string('admin_email')->comment('Email administratora');
            $table->text('reason')->nullable()->comment('Powód resetowania hasła');
            $table->enum('reset_type', ['single', 'bulk'])->default('single')->comment('Typ resetowania: pojedynczy lub masowy');
            $table->timestamp('token_expires_at')->comment('Data wygaśnięcia tokenu (72 godziny)');
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending')->comment('Status resetowania');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['admin_id', 'created_at']);
            $table->index(['token_expires_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_logs');
    }
};
