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
        Schema::table('turno', function (Blueprint $table) {
            $table->unsignedBigInteger('ASESOR_ase_id')->nullable()->after('USUARIO_user_id');
            $table->foreign('ASESOR_ase_id')->references('ase_id')->on('asesor')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno', function (Blueprint $table) {
            $table->dropForeign(['ASESOR_ase_id']);
            $table->dropColumn('ASESOR_ase_id');
        });
    }
};
