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
        Schema::create('payment_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('group_name')->comment('Nazwa grupy');
            $table->date('sent_date')->comment('Data wysłania przypomnienia');
            $table->string('reminder_type')->comment('Typ przypomnienia: bieżący/zaległy');
            $table->integer('unpaid_count')->comment('Liczba nieopłaconych miesięcy');
            $table->decimal('total_amount', 8, 2)->comment('Łączna kwota zaległości');
            $table->timestamps();
            
            // Indeksy dla wydajności
            $table->index(['user_id', 'sent_date']);
            $table->index(['group_name', 'sent_date']);
            $table->unique(['user_id', 'sent_date'], 'unique_user_date_reminder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reminder_logs');
    }
};
