<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeccionesPlanOriginal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secciones_plan_original', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ventas_planes_id')->unsigned()->nullable();
            $table->foreign('ventas_planes_id')->references('id')->on('ventas_planes');
            $table->string('seccion');
            $table->string('seccion_ingles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secciones_plan_original');
    }
}