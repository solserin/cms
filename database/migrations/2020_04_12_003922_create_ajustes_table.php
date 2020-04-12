<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAjustesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //esta tabla me indica si ya esta funcionando la asgincacion automatica de los numeros de convenio y de titulos
        Schema::create('ajustes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('numero_convenios_sistematizados')->default(false);
            $table->boolean('numero_titulos_sistematizados')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ajustes');
    }
}