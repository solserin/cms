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
            $table->unsignedBigInteger('antiguedad_ventas_id');
            $table->foreign('antiguedad_ventas_id')->references('id')->on('antiguedad_ventas'); //VENTA SEGUN SU ANTIGUEDAD
            $table->string('numero_solicitud')->nullable();
            $table->string('numero_convenio')->nullable();
            $table->string('numero_titulo')->nullable();

            /**datos de la ubicacion */
            $table->unsignedBigInteger('propiedades_area_id');
            $table->foreign('propiedades_area_id')->references('id')->on('propiedades');
            $table->string('ubicacion'); //se crea una estructura para poder hacer la relacion de las propiedades
            /**fin de datos de la ubicacion */
            $table->dateTime('fecha_registro');
            $table->date('fecha_venta');

            /**datos en caso de cancelacion */
            $table->string('numero_solicitud_cancelacion')->nullable();
            $table->string('numero_convenio_cancelacion')->nullable();
            $table->string('numero_titulo_cancelacion')->nullable();
            $table->string('ubicacion_cancelacion')->nullable(); //ubicacion en caso de que cancele 
            $table->dateTime('fecha_cancelacion')->nullable();
            $table->unsignedBigInteger('cancelo_id')->nullable();
            $table->foreign('cancelo_id')->references('id')->on('usuarios'); //en caso de cancelar la venta
            $table->string('motivo_cancelacion')->nullable();
            /**fin de datos en caso de cancelacion */

            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('usuarios');
            $table->double('subtotal')->nullable();
            $table->double('descuento')->nullable();
            $table->double('iva')->nullable();
            $table->double('total')->nullable();
            $table->unsignedBigInteger('vendedor_id');
            $table->foreign('vendedor_id')->references('id')->on('usuarios'); //quien hizo la venta

            //informacion del comprador (titular)
            $table->string('nombre');
            $table->string('apellido_m')->nullable();
            $table->string('apellido_p')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado')->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('tel_oficina')->nullable();
            $table->string('rfc')->nullable();
            $table->string('email')->nullable();
            $table->date('fecha_nac')->nullable();
            //modalidad de pago de la venta
            $table->tinyInteger('mensualidades')->nullable();
            $table->unsignedBigInteger('ventas_referencias_id');
            $table->foreign('ventas_referencias_id')->references('id')->on('ventas_referencias');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('ventas_propiedades');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}