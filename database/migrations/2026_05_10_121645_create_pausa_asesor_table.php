<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pausa_asesor', function (Blueprint $table) {
            $table->bigIncrements('pau_id');
            $table->dateTime('pau_inicio');
            $table->dateTime('pau_fin')->nullable();
            $table->unsignedBigInteger('SESION_ses_id');
            $table->string('pau_motivo')->nullable();

            $table->foreign('SESION_ses_id')
                  ->references('ses_id')
                  ->on('sesion_asesor')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pausa_asesor');
    }
};
