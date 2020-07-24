<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VentaDetalle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_detalle', function (Blueprint $table) {
            $table->integer('cantidad');
            $table->dateTime('fecha_caducidad')->nullable();
            $table->unsignedBigInteger('movimientos_inventario_id')->unsigned()->nullable();
            $table->foreign('movimientos_inventario_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('lotes_id')->unsigned()->nullable();
            $table->foreign('lotes_id')->references('id')->on('movimientos_inventario');
            $table->unsignedBigInteger('articulos_id')->unsigned()->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedDecimal('subtotal', 10, 2);
            $table->unsignedDecimal('descuento', 10, 2);
            $table->unsignedDecimal('impuestos', 10, 2);
            $table->unsignedDecimal('total', 10, 2);
            $table->tinyInteger('descuento_plan_b')->default(0);
            $table->bigInteger('parent_paquete_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venta_detalle');
    }
}