<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmpresaOperaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 1- 001- VENTA DE TERRENOS DE USO INMEDIATO
         * 2- 002- VENTA DE TERRENOS DE USO A FUTURO
         * 3- 003- SERVICIOS FUNERARIOS DE USO INMEDIATO
         * 4- 004- SERVICIOS FUNERARIOS DE USO A FUTURO
         * 5- 005- VENTA DE ARTICULOS FUNERARIOS
         * 6- 006- VENTA DE CONSUMIBLES
         * 7- 007- COBRO DE CUOTAS DE MANTENIMIENTO ANUAL
         */
        Schema::create('empresa_operaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('referencia_pago', 5)->nullable();
            $table->string('descripcion', 75)->nullable();
            $table->string('nombre_corto', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empresa_operaciones');
    }
}