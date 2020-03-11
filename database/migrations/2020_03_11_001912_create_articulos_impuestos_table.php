<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosImpuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos_impuestos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('articulos_id');
            $table->foreign('articulos_id')->references('id')->on('articulos');

            $table->unsignedBigInteger('sat_impuestos_id');
            $table->foreign('sat_impuestos_id')->references('id')->on('sat_impuestos');
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
        Schema::dropIfExists('articulos_impuestos');
    }
}
