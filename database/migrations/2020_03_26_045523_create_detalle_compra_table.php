<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('articulos_id')->nullable();
            $table->foreign('articulos_id')->references('id')->on('articulos');
            $table->unsignedBigInteger('compras_id')->nullable();
            $table->foreign('compras_id')->references('id')->on('compras');
            $table->integer('cantidad_compra')->default(0)->nullable();
            $table->decimal('precio_neto', 10, 2);
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
        Schema::dropIfExists('detalle_compra');
    }
}
