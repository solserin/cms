<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PreciosPropieadadesTable extends Migration
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
            $table->decimal('precio_neto', 10, 2);
            $table->integer('meses')->nullable();
            $table->decimal('enganche_inicial', 10, 2);
            //claves foraneas
            $table->unsignedBigInteger('tipo_precios_id');
            $table->foreign('tipo_precios_id')->references('id')->on('tipo_precios');
            $table->unsignedBigInteger('tipo_propiedades_id');
            $table->foreign('tipo_propiedades_id')->references('id')->on('tipo_propiedades');
            $table->dateTime('fecha_hora')->nullable();
            //relacion del usuario
            $table->unsignedBigInteger('actualizo_id');
            $table->foreign('actualizo_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}