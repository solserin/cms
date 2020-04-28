<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PagosProgramadosTerrenos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_programados_terrenos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_pago');
            $table->unsignedBigInteger('ventas_terrenos_id')->nullable();
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
            $table->unsignedBigInteger('conceptos_pagos_id')->nullable();
            $table->foreign('conceptos_pagos_id')->references('id')->on('conceptos_pagos');
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
        Schema::dropIfExists('pagos_programados_terrenos');
    }
}