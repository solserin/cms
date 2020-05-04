<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PagosTerrenos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_terrenos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pagos_programados_terrenos_id')->nullable();
            $table->foreign('pagos_programados_terrenos_id')->references('id')->on('pagos_programados_terrenos');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('descuento', 10, 2);
            $table->decimal('total', 10, 2);
            $table->dateTime('fecha_registro')->nullable();
            $table->date('fecha_pago');
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cobrador_id')->nullable();
            $table->foreign('cobrador_id')->references('id')->on('usuarios');
            $table->string('ultimos_cuatro')->nullable();
            $table->string('banco')->nullable();
            $table->string('num_cheque')->nullable();
            $table->string('referencia_operacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('motivos_cancelacion_id')->nullable();
            $table->foreign('motivos_cancelacion_id')->references('id')->on('motivos_cancelacion'); //en caso de cancelar la venta
            $table->unsignedBigInteger('sat_formas_pago_id')->nullable();
            $table->foreign('sat_formas_pago_id')->references('id')->on('sat_formas_pago');
            $table->unsignedBigInteger('tipo_pagos_id')->nullable();
            $table->foreign('tipo_pagos_id')->references('id')->on('tipo_pagos');
            $table->longText('nota')->nullable();
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_terrenos');
    }
}