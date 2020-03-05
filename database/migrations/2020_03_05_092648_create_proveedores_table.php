<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_comercial');
            $table->string('razon_social');
            $table->string('rfc');
            $table->string('nombre_contacto');
            $table->string('telefono');
            $table->string('pagina_web')->nullable();
            $table->string('email');
            $table->string('calle');
            $table->string('col');
            $table->string('num_ext');
            $table->string('num_int')->nullable();
            $table->integer('cp')->default(0);
            $table->string('ciudad');
            $table->string('estado');
            $table->string('pais');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('proveedores');
    }
}
