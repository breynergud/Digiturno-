<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ASESOR', function (Blueprint $table) {
            $table->enum('ase_estado', ['disponible', 'en_espera', 'ocupado'])
                  ->default('disponible')
                  ->after('ase_correo');
            // ID del turno que está atendiendo actualmente (nullable)
            $table->unsignedBigInteger('ase_turno_actual_id')->nullable()->after('ase_estado');
            $table->string('ase_turno_actual_tipo', 20)->nullable()->after('ase_turno_actual_id');
        });
    }

    public function down(): void
    {
        Schema::table('ASESOR', function (Blueprint $table) {
            $table->dropColumn(['ase_estado', 'ase_turno_actual_id', 'ase_turno_actual_tipo']);
        });
    }
};
