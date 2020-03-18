<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasPropiedadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas_propiedades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero_solicitud')->nullable();
            $table->string('numero_convenio')->nullable();
            $table->string('numero_titulo')->nullable();
            $table->string('ubicacion'); //se crea una estructura para poder hacer la relacion de las propiedades
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_venta');
            $table->unsignedBigInteger('tipo_propiedades_id');
            $table->foreign('tipo_propiedades_id')->references('id')->on('tipo_propiedades');
            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->double('subtotal');
            $table->double('iva');
            $table->double('total');
            $table->unsignedBigInteger('cancelo_id');
            $table->foreign('cancelo_id')->references('id')->on('usuarios'); //en caso de cancelar la venta
            $table->string('motivo_cancelacion');
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('usuarios'); //quien hizo la venta
            //informacion del comprador (titular)
            $table->string('nombre');
            $table->string('apellido_m')->nullable();
            $table->string('apellido_p')->nullable();
            $table->string('domicilio');
            $table->string('ciudad');
            $table->string('estado');
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('rfc')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_nac');
            //modalidad de pago de la venta
            $table->tinyInteger('mensualidades');
            $table->unsignedBigInteger('ventas_referencias_id');
            $table->foreign('ventas_referencias_id')->references('id')->on('ventas_referencias');
            $table->unsignedBigInteger('antiguedad_ventas_id');
            $table->foreign('antiguedad_ventas_id')->references('id')->on('antiguedad_ventas'); //quien hizo la venta
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
        Schema::dropIfExists('ventas_propiedades');
    }
}