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
        Schema::create('criterios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('porcentaje');
            $table->integer('total');
            $table->unsignedBigInteger('curso_id')->nullable();
            $table->foreign('curso_id')->references('id')->on('curso_habilitados')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('categorias_criterio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('porcentaje');
            $table->integer('total');
            $table->unsignedBigInteger('criterio_id');
            $table->foreign('criterio_id')->references('id')->on('criterios')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterios');
        Schema::dropIfExists('categorias_criterio');
    }
};
