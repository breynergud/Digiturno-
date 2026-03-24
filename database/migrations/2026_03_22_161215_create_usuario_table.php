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
        Schema::create('USUARIO', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_tipo', 45)->nullable();
            $table->integer('PERSONA_pers_doc');
            
            $table->foreign('PERSONA_pers_doc')->references('pers_doc')->on('PERSONA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('USUARIO');
    }
};
