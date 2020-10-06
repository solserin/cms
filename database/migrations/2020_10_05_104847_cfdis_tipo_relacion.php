<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CfdisTipoRelacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdis_tipo_relacion', function (Blueprint $table) {
            $table->unsignedBigInteger('sat_tipo_relacion_id')->unsigned();
            $table->foreign('sat_tipo_relacion_id')->references('id')->on('sat_tipo_relacion');
            $table->string('cfdis_uuid');
            $table->foreign('cfdis_uuid')->references('uuid')->on('cfdis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cfdis_tipo_relacion');
    }
}
