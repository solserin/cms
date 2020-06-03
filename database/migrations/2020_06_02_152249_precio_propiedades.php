<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PrecioPropiedades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios_propiedades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('pago_inicial');
            $table->float('subtotal');
            $table->float('impuestos');
            $table->float('costo_neto');
            $table->tinyInteger('descuento_pronto_pago_b');
            $table->float('costo_neto_pronto_pago');
            $table->unsignedBigInteger('tipo_propiedades_id')->unsigned()->nullable();
            $table->foreign('tipo_propiedades_id')->references('id')->on('tipo_propiedades');
            $table->dateTime('fecha_actualizacion');
            $table->unsignedBigInteger('actualizo_id')->unsigned()->nullable();
            $table->foreign('actualizo_id')->references('id')->on('usuarios');
            $table->tinyInteger('financiamiento'); //indica con un valor entero el numeero de pagos qe creara esta operacion 1 para de contado y mayor a 1 para meses
            $table->tinyInteger('contado_b');
            $table->string('descripcion')->nullable();
            $table->string('descripcion_ingles')->nullable();
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
        Schema::dropIfExists('precios_propiedades');
    }
}