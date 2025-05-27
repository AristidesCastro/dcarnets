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
        Schema::create('cardsassets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')
                  ->constrained('institutions') // Asume que la tabla de instituciones es 'institutions'
                  ->cascadeOnDelete(); // Si se borra la institución, se borran sus assets

            $table->string('path_archivo'); // Ruta relativa al archivo guardado en el storage

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardsassets');
    }
};
