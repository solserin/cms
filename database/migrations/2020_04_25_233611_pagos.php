<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('monto_pago');
            $table->float('pago_con_cantidad');
            $table->float('cambio_pago');
            $table->dateTime('fecha_registro')->nullable();
            $table->date('fecha_pago');
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cobrador_id')->nullable();
            $table->foreign('cobrador_id')->references('id')->on('usuarios');
            $table->date('fecha_cancelacion')->nullable();
            $table->foreign('motivos_cancelacion_id')->references('id')->on('motivos_cancelacion'); //en caso de cancelar la venta
            $table->unsignedBigInteger('motivos_cancelacion_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->mediumText('nota_cancelacion')->nullable();
            $table->string('ultimos_cuatro')->nullable();
            $table->string('banco')->nullable();
            $table->string('num_cheque')->nullable();
            $table->string('referencia')->nullable();
            $table->bigInteger('parent_pago_id')->nullable();
            $table->unsignedBigInteger('sat_formas_pago_id')->nullable();
            $table->foreign('sat_formas_pago_id')->references('id')->on('sat_formas_pago');
            $table->unsignedTinyInteger('movimientos_pagos_id')->nullable();
            $table->foreign('movimientos_pagos_id')->references('id')->on('movimientos_pagos');
            $table->unsignedBigInteger('sat_monedas_id')->nullable();
            $table->foreign('sat_monedas_id')->references('id')->on('sat_monedas');
            $table->mediumText('nota')->nullable();
            $table->float('tipo_cambio');
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
        Schema::dropIfExists('pagos');
    }
}