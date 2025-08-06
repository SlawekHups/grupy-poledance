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
        Schema::create('user_mail_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('direction', ['in', 'out'])->comment('in = odebrana, out = wysłana');
            $table->string('email')->comment('Adres email nadawcy/odbiorcy');
            $table->string('subject')->comment('Temat wiadomości');
            $table->text('content')->comment('Treść wiadomości');
            $table->timestamp('sent_at')->nullable()->comment('Data wysłania/odebrania');
            $table->json('headers')->nullable()->comment('Nagłówki emaila');
            $table->string('message_id')->nullable()->comment('Unikalny ID wiadomości');
            $table->timestamps();
            
            // Indeksy dla wydajności
            $table->index(['user_id', 'direction']);
            $table->index(['email', 'direction']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mail_messages');
    }
};
