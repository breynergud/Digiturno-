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
        Schema::table('atencion', function (Blueprint $table) {
            $table->enum('atnc_estado', ['atendido', 'ausente'])->default('atendido')->after('atnc_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atencion', function (Blueprint $table) {
            $table->dropColumn('atnc_estado');
        });
    }
};
