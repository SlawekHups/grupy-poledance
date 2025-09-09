<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['group_id', 'user_id']);
        });

        // Skopiuj istniejące przypisania z kolumny users.group_id do pivotu
        $pairs = DB::table('users')->whereNotNull('group_id')->select('id as user_id', 'group_id')->get();
        if ($pairs->count() > 0) {
            $now = now();
            $insert = $pairs->map(function ($row) use ($now) {
                return [
                    'group_id' => $row->group_id,
                    'user_id' => $row->user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->toArray();
            DB::table('group_user')->insert($insert);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_user');
    }
};


