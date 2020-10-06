<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CfdisOperaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cfdis_operaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('operaciones_id')->unsigned();
            $table->foreign('operaciones_id')->references('id')->on('operaciones');
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
        Schema::dropIfExists('cfdis_operaciones');
    }
}