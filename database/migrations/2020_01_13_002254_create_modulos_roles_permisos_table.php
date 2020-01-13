<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulosRolesPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulos_roles_permisos', function (Blueprint $table) {
            $table->unsignedInteger('modulos_id')->comment('clave foranea del modulo');
            $table->unsignedInteger('roles_id')->comment('clave foranea del rol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulos_roles_permisos');
    }
}
