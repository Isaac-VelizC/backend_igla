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
        Schema::create('evaluacion_docente', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->timestamps();
        });

        Schema::create('eval_preguntas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_evaluacion')->nullable();
            $table->foreign('id_evaluacion')->references('id')->on('evaluacion_docente')->onDelete('cascade');
            $table->integer('numero');
            $table->string('texto')->unique();
            $table->timestamps();
        });
        
        Schema::create('habilitado_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('materia_id')->nullable();
            $table->foreign('materia_id')->references('id')->on('curso_habilitados')->onDelete('cascade');
            $table->unsignedBigInteger('eval_docente_id')->nullable();
            $table->foreign('eval_docente_id')->references('id')->on('evaluacion_docente')->onDelete('cascade');
            $table->datetime('fecha')->default(now());
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('eval_respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregunta_id')->nullable();
            $table->foreign('pregunta_id')->references('id')->on('eval_preguntas')->onDelete('cascade');
            $table->unsignedBigInteger('habilitado_id')->nullable();
            $table->foreign('habilitado_id')->references('id')->on('habilitado_evaluacion')->onDelete('cascade');
            $table->string('texto');
            $table->datetime('fecha')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion_docente');
    }
};
