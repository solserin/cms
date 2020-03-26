<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_compra');
            $table->integer('registro_id')->default(0)->nullable();
            $table->string('referencia_factura');
            $table->unsignedBigInteger('metodos_pago_id')->nullable();
            $table->foreign('metodos_pago_id')->references('id')->on('metodos_pago');
            $table->unsignedBigInteger('proveedores_id')->nullable();
            $table->foreign('proveedores_id')->references('id')->on('proveedores');
            $table->decimal('total_neto', 10, 2);
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
        Schema::dropIfExists('compras');
    }
}
