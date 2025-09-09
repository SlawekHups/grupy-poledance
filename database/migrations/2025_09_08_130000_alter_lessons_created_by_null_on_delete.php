<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: usuń istniejący FK i ustaw kolumnę na NULLABLE, dodaj FK z ON DELETE SET NULL
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign('lessons_created_by_foreign');
        });

        // Zmień kolumnę na nullable (użyj SQL aby nie wymagać doctrine/dbal)
        DB::statement('ALTER TABLE lessons MODIFY created_by BIGINT UNSIGNED NULL');

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Przywróć NOT NULL (może się nie udać jeśli są NULL-e)
        DB::statement('ALTER TABLE lessons MODIFY created_by BIGINT UNSIGNED NOT NULL');

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users'); // bez akcji on delete
        });
    }
};


