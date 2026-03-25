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
        Schema::create('atencion', function (Blueprint $table) {
            $table->id('atnc_id');
            $table->dateTime('atnc_hora_inicio');
            $table->dateTime('atnc_hora_fin')->nullable();
            $table->enum('atnc_tipo', ['General', 'Prioritaria', 'Victimas']);
            
            $table->unsignedBigInteger('ASESOR_ase_id')->nullable();
            $table->unsignedBigInteger('COORDINADOR_coor_id')->nullable();
            $table->unsignedBigInteger('TURNO_tur_id')->nullable();

            $table->foreign('ASESOR_ase_id')
                  ->references('ase_id')->on('asesor')
                  ->onDelete('cascade');
                  
            $table->foreign('TURNO_tur_id')
                  ->references('tur_id')->on('turno')
                  ->onDelete('cascade');

            $table->foreign('COORDINADOR_coor_id')
                  ->references('coor_id')->on('coordinador')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atencion');
    }
};
