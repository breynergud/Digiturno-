<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop existing foreign keys first to allow changing column types
        Schema::table('asesor', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
        });
        Schema::table('coordinador', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
        });
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
        });

        // 2. Clean up orphaned rows before re-adding foreign keys
        DB::table('asesor')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();
        
        DB::table('coordinador')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();
        
        DB::table('usuario')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();

        // 3. Change the primary key type in persona table
        Schema::table('persona', function (Blueprint $table) {
            $table->bigInteger('pers_doc')->change();
        });

        // 4. Change foreign keys type and re-add constraints
        Schema::table('asesor', function (Blueprint $table) {
            $table->bigInteger('PERSONA_pers_doc')->change();
            $table->foreign('PERSONA_pers_doc', 'fk_asesor_persona_doc_v2')->references('pers_doc')->on('persona');
        });
        Schema::table('coordinador', function (Blueprint $table) {
            $table->bigInteger('PERSONA_pers_doc')->change();
            $table->foreign('PERSONA_pers_doc', 'fk_coordinador_persona_doc_v2')->references('pers_doc')->on('persona');
        });
        Schema::table('usuario', function (Blueprint $table) {
            $table->bigInteger('PERSONA_pers_doc')->change();
            $table->foreign('PERSONA_pers_doc', 'fk_usuario_persona_doc_v2')->references('pers_doc')->on('persona');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys if rolling back, then revert type
        Schema::table('asesor', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
            $table->integer('PERSONA_pers_doc')->change();
        });
        Schema::table('coordinador', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
            $table->integer('PERSONA_pers_doc')->change();
        });
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropForeign(['PERSONA_pers_doc']);
            $table->integer('PERSONA_pers_doc')->change();
        });

        Schema::table('persona', function (Blueprint $table) {
            $table->integer('pers_doc')->change();
        });
    }
};
