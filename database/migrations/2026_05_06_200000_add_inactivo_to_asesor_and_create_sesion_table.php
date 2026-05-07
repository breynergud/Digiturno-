<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modificar el ENUM de ase_estado para incluir 'inactivo'
        // MySQL requiere re-declarar el ENUM completo
        DB::statement("ALTER TABLE ASESOR MODIFY COLUMN ase_estado ENUM('disponible','en_espera','ocupado','inactivo') NOT NULL DEFAULT 'inactivo'");

        // 2. Poner todos los asesores existentes en 'inactivo' (jornada cerrada por defecto)
        DB::statement("UPDATE ASESOR SET ase_estado = 'inactivo' WHERE ase_estado IN ('disponible', 'en_espera')");

        // 3. Crear tabla sesion_asesor para registrar jornadas de trabajo
        Schema::create('sesion_asesor', function (Blueprint $table) {
            $table->bigIncrements('ses_id');
            $table->dateTime('ses_inicio');
            $table->dateTime('ses_fin')->nullable();
            $table->unsignedBigInteger('ASESOR_ase_id');

            $table->foreign('ASESOR_ase_id')
                  ->references('ase_id')
                  ->on('ASESOR')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesion_asesor');

        // Revertir ENUM a valores originales y default a 'disponible'
        DB::statement("ALTER TABLE ASESOR MODIFY COLUMN ase_estado ENUM('disponible','en_espera','ocupado') NOT NULL DEFAULT 'disponible'");
        DB::statement("UPDATE ASESOR SET ase_estado = 'disponible' WHERE ase_estado = 'inactivo'");
    }
};
