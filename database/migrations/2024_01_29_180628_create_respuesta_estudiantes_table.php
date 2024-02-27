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
        Schema::create('respuesta_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estudiante_id')->nullable();
            $table->foreign('estudiante_id')->references('id')->on('estudiantes')->onDelete('cascade');
            $table->unsignedBigInteger('materia_id')->nullable();
            $table->foreign('materia_id')->references('id')->on('curso_habilitados')->onDelete('cascade');
            $table->string('cometario')->nullable();
            $table->datetime('fecha')->default(now());
            $table->timestamps();
        });

        Schema::table('eval_respuestas', function (Blueprint $table) {
            // Agregar la nueva columna 'est_respt_id' antes de la columna 'texto'
            $table->unsignedBigInteger('est_respt_id')->nullable();
            $table->foreign('est_respt_id')->references('id')->on('respuesta_estudiantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::table('eval_respuestas', function (Blueprint $table) {
            $table->dropForeign(['est_respt_id']);
            $table->dropColumn('est_respt_id');
        });
        Schema::dropIfExists('respuesta_estudiantes');
    }
};
