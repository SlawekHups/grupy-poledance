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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nazwa pliku
            $table->string('original_name'); // Oryginalna nazwa pliku
            $table->string('path'); // Ścieżka do pliku
            $table->string('mime_type'); // Typ MIME
            $table->bigInteger('size'); // Rozmiar w bajtach
            $table->string('category')->default('general'); // Kategoria pliku
            $table->text('description')->nullable(); // Opis pliku
            $table->foreignId('uploaded_by')->constrained('users'); // Kto przesłał
            $table->boolean('is_public')->default(false); // Czy publiczny
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
