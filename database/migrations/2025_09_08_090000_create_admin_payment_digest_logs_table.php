<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_payment_digest_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('weekday');
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('groups_count')->default(0);
            $table->unsignedInteger('users_count')->default(0);
            $table->unsignedInteger('unpaid_count')->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['date'], 'unique_digest_per_day');
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_payment_digest_logs');
    }
};


