<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustesPoliticas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajustes_politicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('tasa_fija_anual', 8, 2);
            $table->integer('dias_antes_vencimiento');
            $table->integer('maximo_dias_retraso');
            $table->unsignedDecimal('porcentaje_pena_convencional_minima', 8, 2);
            $table->integer('minima_partes_cubiertas');
            $table->integer('maximo_pagos_vencidos');
            $table->integer('maximo_dias_cancelar_contrato');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajustes_politicas');
    }
}