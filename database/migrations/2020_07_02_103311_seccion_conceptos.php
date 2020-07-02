<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeccionConceptos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seccion_conceptos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('concepto');
            $table->string('concepto_ingles');
            $table->unsignedBigInteger('secciones_planes_id')->unsigned()->nullable();
            $table->foreign('secciones_planes_id')->references('id')->on('secciones_planes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seccion_conceptos');
    }
}