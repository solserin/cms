<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosPaqueteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos_paquete', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cantidad')->default(0);
            $table->unsignedBigInteger('articulos_parent_id');
            $table->foreign('articulos_parent_id')->references('id')->on('articulos');
            $table->unsignedBigInteger('articulos_id');
            $table->foreign('articulos_id')->references('id')->on('articulos');
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
        Schema::dropIfExists('articulos_paquete');
    }
}
