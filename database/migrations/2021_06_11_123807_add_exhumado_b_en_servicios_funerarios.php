<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExhumadoBEnServiciosFunerarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('servicios_funerarios', function ($table) {
            $table->tinyInteger('exhumado_b')->unsigned()->nullable()->after('tipo_solicitud_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('servicios_funerarios', function ($table) {
            $table->dropColumn('exhumado_b');
        });
    }
}
