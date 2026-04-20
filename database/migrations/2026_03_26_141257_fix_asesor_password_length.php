<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliar columna a 255 para soportar hashes bcrypt (60 chars)
        Schema::table('asesor', function (Blueprint $table) {
            $table->string('ase_password', 255)->nullable()->change();
        });

        // Re-hashear contraseñas con la contraseña por defecto "sena2024"
        // (las anteriores estaban truncadas y son inválidas)
        DB::table('asesor')->update([
            'ase_password' => Hash::make('sena2024')
        ]);
    }

    public function down(): void
    {
        Schema::table('asesor', function (Blueprint $table) {
            $table->string('ase_password', 45)->nullable()->change();
        });
    }
};
