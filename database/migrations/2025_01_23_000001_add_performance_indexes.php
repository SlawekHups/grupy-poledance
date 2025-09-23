<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Dodaje indeksy wydajnościowe dla optymalizacji zapytań w Filament Resources.
     * Wszystkie indeksy są dodawane bezpiecznie dla produkcji.
     */
    public function up(): void
    {
        // Sprawdź czy kolumny istnieją przed dodaniem indeksów
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasIndex('users', 'users_role_idx')) {
                    $table->index('role', 'users_role_idx');
                }
            });
        }
        
        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasIndex('users', 'users_is_active_idx')) {
                    $table->index('is_active', 'users_is_active_idx');
                }
            });
        }
        
        if (Schema::hasColumn('users', 'terms_accepted_at')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasIndex('users', 'users_terms_accepted_at_idx')) {
                    $table->index('terms_accepted_at', 'users_terms_accepted_at_idx');
                }
            });
        }

        // Indeksy dla tabeli payments
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasIndex('payments', 'payments_paid_idx')) {
                    $table->index('paid', 'payments_paid_idx');
                }
                
                if (!Schema::hasIndex('payments', 'payments_month_idx')) {
                    $table->index('month', 'payments_month_idx');
                }
                
                if (!Schema::hasIndex('payments', 'payments_user_month_idx')) {
                    $table->index(['user_id', 'month'], 'payments_user_month_idx');
                }
            });
        }

        // Indeksy dla tabeli lessons
        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (!Schema::hasIndex('lessons', 'lessons_created_by_idx')) {
                    $table->index('created_by', 'lessons_created_by_idx');
                }
            });
        }

        // Indeksy dla tabeli group_user (pivot table)
        if (Schema::hasTable('group_user')) {
            Schema::table('group_user', function (Blueprint $table) {
                if (!Schema::hasIndex('group_user', 'group_user_user_id_idx')) {
                    $table->index('user_id', 'group_user_user_id_idx');
                }
                
                if (!Schema::hasIndex('group_user', 'group_user_group_id_idx')) {
                    $table->index('group_id', 'group_user_group_id_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Bezpieczne usuwanie indeksów w kolejności odwrotnej.
     */
    public function down(): void
    {
        // Usuwanie indeksów z group_user
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropIndex('group_user_group_id_idx');
            $table->dropIndex('group_user_user_id_idx');
        });

        // Usuwanie indeksów z lessons
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('lessons_created_by_idx');
        });

        // Usuwanie indeksów z payments
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_user_month_idx');
            $table->dropIndex('payments_month_idx');
            $table->dropIndex('payments_paid_idx');
        });

        // Usuwanie indeksów z users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_terms_accepted_at_idx');
            $table->dropIndex('users_is_active_idx');
            $table->dropIndex('users_role_idx');
        });
    }
};
