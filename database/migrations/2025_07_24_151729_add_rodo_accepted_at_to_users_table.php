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
        if (!Schema::hasColumn('users', 'rodo_accepted_at')) {
            Schema::table('users', function (Blueprint $table) {
                // Bezpieczne określenie kolumny, po której wstawić nową
                $afterColumn = 'email_verified_at';
                if (Schema::hasColumn('users', 'terms_accepted_at')) {
                    $afterColumn = 'terms_accepted_at';
                } elseif (Schema::hasColumn('users', 'accepted_terms_at')) {
                    $afterColumn = 'accepted_terms_at';
                }

                $table->timestamp('rodo_accepted_at')->nullable()->after($afterColumn);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rodo_accepted_at');
        });
    }
};
