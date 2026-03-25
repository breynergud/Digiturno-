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
        Schema::create('persona', function (Blueprint $table) {
            $table->integer('pers_doc')->primary();
            $table->string('pers_tipodoc', 45)->nullable();
            $table->string('pers_nombres', 100)->nullable();
            $table->string('pers_apellidos', 100)->nullable();
            $table->bigInteger('pers_telefono')->nullable();
            $table->timestamp('pers_fecha_nac')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona');
    }
};
