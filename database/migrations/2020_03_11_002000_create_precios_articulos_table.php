<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreciosArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('precios_articulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('precio', 10, 2);
            $table->unsignedBigInteger('articulos_id');
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedBigInteger('precios_id');
            $table->foreign('precios_id')->references('id')->on('precios');
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
        Schema::dropIfExists('precios_articulos');
    }
}
