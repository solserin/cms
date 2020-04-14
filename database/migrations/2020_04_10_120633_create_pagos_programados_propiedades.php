<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosProgramadosPropiedades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_programados_propiedades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_pago');
            $table->unsignedBigInteger('ventas_propiedades_id')->nullable();
            $table->foreign('ventas_propiedades_id')->references('id')->on('ventas_propiedades');
            $table->unsignedBigInteger('tipo_pagos_id')->nullable();
            $table->foreign('tipo_pagos_id')->references('id')->on('tipo_pagos');
            $table->date('fecha_programada');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('descuento', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('referencia_pago');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_programados_propiedades');
    }
}