<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BeneficiariosTerrenos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiarios_terrenos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->nullable();
            $table->string('parentesco')->nullable();
            $table->string('telefono')->nullable();
            $table->unsignedBigInteger('ventas_terrenos_id');
            $table->foreign('ventas_terrenos_id')->references('id')->on('ventas_terrenos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiarios_terrenos');
    }
}