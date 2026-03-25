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
        Schema::create('asesor', function (Blueprint $table) {
            $table->id('ase_id');
            $table->string('ase_nrocontrato', 45)->nullable();
            $table->string('ase_tipo_asesor', 2)->nullable();
            $table->integer('PERSONA_pers_doc');
            $table->string('ase_vigencia', 45)->nullable();
            $table->string('ase_password', 45)->nullable();
            $table->string('ase_correo', 45)->nullable();
            
            $table->foreign('PERSONA_pers_doc')->references('pers_doc')->on('persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesor');
    }
};
