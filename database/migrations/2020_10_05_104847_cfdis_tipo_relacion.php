<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CfdisTipoRelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdis_tipo_relacion', function (Blueprint $table) {
            $table->unsignedBigInteger('sat_tipo_relacion_id')->unsigned()->nullable();
            $table->foreign('sat_tipo_relacion_id')->references('id')->on('sat_tipo_relacion');
            $table->unsignedBigInteger('cfdis_id')->unsigned();
            $table->foreign('cfdis_id')->references('id')->on('cfdis');
            $table->unsignedDecimal('importe_pagado', 10, 2)->nullable();
            $table->unsignedDecimal('importe_saldo_anterior', 10, 2)->nullable();
            $table->unsignedDecimal('importe_saldo_insoluto', 10, 2)->nullable();
            $table->integer('numero_parcialidad')->nullable();
            $table->unsignedBigInteger('sat_metodos_pago_id')->unsigned();
            $table->foreign('sat_metodos_pago_id')->references('id')->on('sat_metodos_pago');
            $table->unsignedDecimal('monto_nota_credito', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cfdis_tipo_relacion');
    }
}