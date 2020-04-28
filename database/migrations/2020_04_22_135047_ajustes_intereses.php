<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AjustesIntereses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ajustes_intereses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('tasa_fija_anual')->default(12);
            $table->integer('dias_antes_vencimiento')->default(5);
            $table->integer('maximo_dias_retraso')->default(120);
            $table->double('porcentaje_pena_convencional_minima')->default(60);
            $table->integer('minima_partes_cubiertas')->default(3);
            $table->integer('maximo_pagos_vencidos')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajustes_intereses');
    }
}