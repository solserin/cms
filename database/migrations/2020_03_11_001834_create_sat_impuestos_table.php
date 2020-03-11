<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatImpuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_impuestos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave');
            $table->string('impuesto');
            $table->integer('traslado')->default(1);
            $table->integer('retencion')->default(1);
            $table->decimal('porcentaje', 10, 2);
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
        Schema::dropIfExists('sat_impuestos');
    }
}
