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
            $table->unsignedBigInteger('antiguedad_ventas_id');
            $table->foreign('antiguedad_ventas_id')->references('id')->on('antiguedad_ventas'); //VENTA SEGUN SU ANTIGUEDAD
            $table->string('numero_solicitud')->nullable();
            $table->string('numero_convenio')->nullable();
            $table->string('numero_titulo')->nullable();
            /**datos de la ubicacion */
            $table->unsignedBigInteger('propiedades_id')->nullable();
            $table->foreign('propiedades_id')->references('id')->on('propiedades'); //id de la propieda a la que pertenece
            $table->string('ubicacion', 15); //se crea una estructura para poder hacer la relacion de las propiedades
            /**fin de datos de la ubicacion */
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_modificacion')->nullable();
            $table->unsignedBigInteger('modifico_id')->nullable();
            $table->foreign('modifico_id')->references('id')->on('usuarios');
            $table->date('fecha_venta');
            $table->double('subtotal')->nullable();
            $table->double('descuento')->nullable();
            $table->double('iva')->nullable();
            $table->double('total')->nullable();
            //informacion del titular sustituto
            $table->string('titular_sustituto');
            $table->string('parentesco_titular_sustituto')->nullable();
            $table->string('telefono_titular_sustituto')->nullable();
            /**datos en caso de cancelacion */
            $table->string('numero_solicitud_cancelacion')->nullable();
            $table->string('numero_convenio_cancelacion')->nullable();
            $table->string('numero_titulo_cancelacion')->nullable();
            $table->string('ubicacion_cancelacion')->nullable(); //ubicacion en caso de que cancele 
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios'); //en caso de cancelar la venta
            $table->unsignedBigInteger('motivos_cancelacion_id')->nullable();
            $table->foreign('motivos_cancelacion_id')->references('id')->on('motivos_cancelacion'); //en caso de cancelar la venta
            /**fin de datos en caso de cancelacion */
            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('usuarios'); //quien hizo la venta
            $table->unsignedBigInteger('empresa_operaciones_id')->nullable();
            $table->foreign('empresa_operaciones_id')->references('id')->on('empresa_operaciones');
            $table->longText('nota')->nullable();
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
        Schema::dropIfExists('ventas_terrenos');
    }
}