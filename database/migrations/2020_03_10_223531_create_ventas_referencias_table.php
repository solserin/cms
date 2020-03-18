<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasReferenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_referencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referencia_pagos');
            $table->integer('tipos_venta_id')->unsigned(); //depende del tipo de venta para saber que referencia tomar a la hora de manipular pagos
            $table->string('tipo_venta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas_referencias');
    }
}