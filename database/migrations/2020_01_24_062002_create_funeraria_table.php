<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunerariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funeraria', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('localidades_id')->nullable();
            $table->unsignedBigInteger('sat_regimenes_id')->nullable();
            $table->foreign('localidades_id')->references('id')->on('localidades');
            $table->string('nombre_comercial');
            $table->string('razon_social');
            $table->string('rfc');
            $table->string('calle');
            $table->string('num_ext');
            $table->string('num_int')->nullable();
            $table->string('colonia');
            $table->integer('cp')->default(0)->nullable();
            $table->string('zona_horaria');
            $table->string('telefono');
            $table->string('ext')->nullable();
            $table->string('fax')->nullable();
            $table->string('email');
            $table->string('facebook')->nullable();
            $table->string('web')->nullable();
            $table->timestamps();
        });
            
        DB::statement("ALTER TABLE funeraria ADD logo MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funeraria');
    }
}
