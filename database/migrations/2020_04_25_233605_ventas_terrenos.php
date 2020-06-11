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
            /**datos de la ubicacion */
            $table->unsignedTinyInteger('tipo_propiedades_id')->unsigned()->nullable();
            $table->foreign('tipo_propiedades_id')->references('id')->on('tipo_propiedades'); //id del tipo de  propiedad a la que pertenece


            $table->unsignedBigInteger('propiedades_id')->unsigned()->nullable();
            $table->foreign('propiedades_id')->references('id')->on('propiedades'); //id de la propieda a la que pertenece
            $table->string('ubicacion', 15)->nullable(); //se crea una estructura para poder hacer la relacion de las propiedades
            /**fin de datos de la ubicacion */
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->dateTime('fecha_venta');

            /**fin de datos en caso de cancelacion */
            $table->unsignedBigInteger('registro_id')->unsigned()->nullable();
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('vendedor_id')->unsigned()->nullable();
            $table->foreign('vendedor_id')->references('id')->on('usuarios'); //quien hizo la venta
            $table->mediumText('nota')->nullable();
            $table->tinyInteger('considerar_mantenimiento_b')->default(1);
            $table->mediumText('nota_mantenimiento')->nullable();
            $table->tinyInteger('tipo_financiamiento')->nullable(); //1 contado(uso inmeadiatoo) 2-credito uso a futuro
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