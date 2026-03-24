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
        Schema::create('ATENCION', function (Blueprint $table) {
            $table->id('atnc_id');
            $table->dateTime('atnc_hora_inicio');
            $table->dateTime('atnc_hora_fin')->nullable();
            $table->enum('atnc_tipo', ['General', 'Prioritaria', 'Victimas']);
            
            $table->unsignedBigInteger('ASESOR_ase_id')->nullable();
            $table->unsignedBigInteger('TURNO_tur_id')->nullable();

            $table->foreign('ASESOR_ase_id')
                  ->references('ase_id')->on('ASESOR')
                  ->onDelete('cascade');
                  
            $table->foreign('TURNO_tur_id')
                  ->references('tur_id')->on('TURNO')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ATENCION');
    }
};
