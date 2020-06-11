<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConceptosPagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        1- enganche
        2- abono
        3- liquidacion*/
        Schema::create('conceptos_pagos', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('concepto', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conceptos_pago');
    }
}