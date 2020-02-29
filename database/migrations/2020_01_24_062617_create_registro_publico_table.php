<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroPublicoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_publico', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funeraria_id')->nullable();
            $table->foreign('funeraria_id')->references('id')->on('funeraria');

            $table->unsignedBigInteger('ciudad_np')->nullable();
            $table->foreign('ciudad_np')->references('id')->on('localidades');

            $table->unsignedBigInteger('ciudad_rpc')->nullable();
            $table->foreign('ciudad_rpc')->references('id')->on('localidades');
            

            $table->string('rep_legal');
            $table->string('t_nep');
            $table->date('fecha_tnep');
            $table->string('fe_lic');
            $table->integer('num_np')->default(0)->nullable();
            $table->integer('num_rpc')->default(0)->nullable();
            $table->date('fecha_rpc');
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
        Schema::dropIfExists('registro_publico');
    }
}
