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
        Schema::create('cardselements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('elementstype_id');
            $table->foreign('elementstype_id')->references('id')->on('elementstypes');
            $table->string('informacion', 255);
            $table->integer('posicion_X');
            $table->integer('posicion_Y');
            $table->integer('tamano_W');
            $table->integer('tamano_H');
            $table->boolean('visible');
            $table->unsignedBigInteger('cardsdesign_id');
            $table->foreign('cardsdesign_id')->references('id')->on('cardsdesigns');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardselements');
    }
};
