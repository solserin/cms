<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeccionesConceptosOriginal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seccion_conceptos_original', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('secciones_plan_original_id')->unsigned()->nullable();
            $table->foreign('secciones_plan_original_id')->references('id')->on('secciones_plan_original');
            $table->string('concepto');
            $table->string('concepto_ingles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seccion_conceptos_original');
    }
}