<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposProfecoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_profeco', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('grupo');
            $table->string('ver_nombre');
            $table->unsignedBigInteger('grupo_parent_id')->nullable();
            $table->foreign('grupo_parent_id')->references('id')->on('grupos_profeco');
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
        Schema::dropIfExists('grupos_profeco');
    }
}
