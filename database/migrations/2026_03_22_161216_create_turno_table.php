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
        Schema::create('TURNO', function (Blueprint $table) {
            $table->id('tur_id');
            $table->dateTime('tur_hora_fecha');
            $table->string('tur_numero', 45);
            $table->enum('tur_tipo', ['General', 'Prioritario', 'Victimas']);
            $table->unsignedBigInteger('USUARIO_user_id')->nullable();

            $table->foreign('USUARIO_user_id')
                  ->references('user_id')->on('USUARIO')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TURNO');
    }
};
