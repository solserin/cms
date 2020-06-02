<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VentasTerrenos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_terrenos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('clientes_id');
            $table->foreign('clientes_id')->references('id')->on('clientes');

            /**datos de la ubicacion */
            $table->unsignedBigInteger('propiedades_id')->nullable();
            $table->foreign('propiedades_id')->references('id')->on('propiedades'); //id de la propieda a la que pertenece
            $table->string('ubicacion', 15)->nullable(); //se crea una estructura para poder hacer la relacion de las propiedades
            /**fin de datos de la ubicacion */
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_venta');

            /**fin de datos en caso de cancelacion */
            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('usuarios'); //quien hizo la venta
            $table->mediumText('nota')->nullable();
            $table->tinyInteger('consider_mantenimiento_b')->default(1);
            $table->mediumText('nota_mantenimiento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas_terrenos');
    }
}