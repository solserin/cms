<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localidades', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('municipio_id');
            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('cascade');
            $table->string('clave', 4);
            $table->string('nombre', 100);
            $table->string('latitud', 15);
            $table->string('longitud', 15);
            $table->string('altitud', 15);
            $table->string('carta', 10);
            $table->string('ambito', 1);
            $table->integer('poblacion')->default(0);
            $table->integer('masculino')->default(0);
            $table->integer('femenino')->default(0);
            $table->integer('viviendas')->default(0);
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->tinyInteger('activo')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localidades');
    }
}
