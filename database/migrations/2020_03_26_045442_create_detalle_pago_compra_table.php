<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePagoCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pago_compra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('num_cheque')->nullable();
            $table->string('digitos')->nullable();
            $table->string('banco')->nullable();
            $table->string('num_transferencia')->nullable();
            
            $table->unsignedBigInteger('compras_id');
            $table->foreign('compras_id')->references('id')->on('compras');
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
        Schema::dropIfExists('detalle_pago_compra');
    }
}
