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
        // Clean up orphaned rows before adding foreign keys
        DB::table('asesor')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();
        
        DB::table('coordinador')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();
        
        DB::table('usuario')->whereNotIn('PERSONA_pers_doc', function($query) {
            $query->select('pers_doc')->from('persona');
        })->delete();

        // Change the primary key type
        Schema::table('persona', function (Blueprint $table) {
            $table->bigInteger('pers_doc')->change();
        });

        // Change foreign keys type and re-add constraints with unique names
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
            // Wrap in try-catch or skip if already created from previous crash? 
            // Better to use a unique name, but wait, if previous run created a constraint for usuario, changing it again might fail.
            // Let's drop the previous one we might have created:
            // $table->dropForeign(['PERSONA_pers_doc']); // This would fail if it doesn't exist.
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
