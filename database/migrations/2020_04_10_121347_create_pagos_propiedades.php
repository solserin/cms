<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosPropiedades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_propiedades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pagos_programados_propiedades_id')->nullable();
            $table->foreign('pagos_programados_propiedades_id')->references('id')->on('pagos_programados_propiedades');


            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('descuento', 10, 2);
            $table->decimal('total', 10, 2);

            $table->dateTime('fecha_registro');
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
            $table->longText('motivo_cancelacion')->nullable();
            $table->unsignedBigInteger('sat_formas_pago_id')->nullable();
            $table->foreign('sat_formas_pago_id')->references('id')->on('sat_formas_pago');
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
        Schema::dropIfExists('pagos_propiedades');
    }
}