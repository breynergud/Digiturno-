<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix enum en tabla 'turno' para incluir 'Empresario'
        DB::statement("ALTER TABLE `turno` MODIFY `tur_tipo` ENUM('General', 'Prioritario', 'Victimas', 'Empresario') NOT NULL");

        // Fix enum en tabla 'atencion' para incluir 'Empresario' y 'Prioritario'
        DB::statement("ALTER TABLE `atencion` MODIFY `atnc_tipo` ENUM('General', 'Prioritaria', 'Prioritario', 'Victimas', 'Empresario') NOT NULL");

        // Hacer telefono nullable en tabla 'turnos'
        Schema::table('turnos', function (Blueprint $table) {
            $table->string('telefono')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `turno` MODIFY `tur_tipo` ENUM('General', 'Prioritario', 'Victimas') NOT NULL");
        DB::statement("ALTER TABLE `atencion` MODIFY `atnc_tipo` ENUM('General', 'Prioritaria', 'Victimas') NOT NULL");
        Schema::table('turnos', function (Blueprint $table) {
            $table->string('telefono')->nullable(false)->change();
        });
    }
};
