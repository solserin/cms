<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProgramacionPagosTerrenos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programacion_pagos_terrenos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_version')->nullable();
            $table->unsignedBigInteger('ventas_terrenos_id')->nullable();
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
            $table->integer('mensualidades')->nullable();
            $table->double('enganche_inicial')->nullable();
            $table->dateTime('fecha_registro')->nullable()->description('fecha de ingreso en la bd');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programacion_pagos_terrenos');
    }
}