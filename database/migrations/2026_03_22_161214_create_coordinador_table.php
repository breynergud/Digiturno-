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
        Schema::create('coordinador', function (Blueprint $table) {
            $table->id('coor_id');
            $table->string('coor_vigencia', 45)->nullable();
            $table->string('coor_correo', 45)->unique()->nullable();
            $table->string('coor_password', 45)->nullable();
            $table->enum('coor_estado', ['disponible', 'ocupado'])->default('disponible');
            $table->integer('PERSONA_pers_doc');
            
            $table->foreign('PERSONA_pers_doc')->references('pers_doc')->on('persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coordinador');
    }
};
