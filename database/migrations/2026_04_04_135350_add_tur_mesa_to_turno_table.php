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
            $table->integer('tur_mesa')->default(0)->after('tur_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turno', function (Blueprint $table) {
            $table->dropColumn('tur_mesa');
        });
    }
};
