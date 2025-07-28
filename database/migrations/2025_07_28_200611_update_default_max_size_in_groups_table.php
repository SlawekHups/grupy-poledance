<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Zmiana domyślnej wartości dla nowych rekordów
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('max_size')->default(7)->change();
        });

        // Aktualizacja istniejących rekordów
        DB::table('groups')->where('max_size', 10)->update(['max_size' => 7]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('max_size')->default(10)->change();
        });
    }
};
