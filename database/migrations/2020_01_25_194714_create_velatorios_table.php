<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVelatoriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('velatorios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funeraria_id')->nullable();
            $table->foreign('funeraria_id')->references('id')->on('funeraria');
            $table->string('velatorio');
            $table->string('calle');
            $table->string('num_ext');
            $table->string('num_int')->nullable();
            $table->string('colonia');
            $table->string('cp')->default(0)->nullable();
            $table->string('telefonos');
            $table->string('fax')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('velatorios');
    }
}