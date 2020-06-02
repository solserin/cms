<?php

use Illuminate\Database\Seeder;

class AntiguedadOperacion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //decide si la venta fue antes del sistema (por lo del manejo del formato de solicitud, convenio y titulo)
        DB::table('antiguedad_operacion')->insert(['antiguedad' => 'NUEVA OPERACIÓN']);
        DB::table('antiguedad_operacion')->insert(['antiguedad' => 'A/S SIN LIQUIDAR']);
        DB::table('antiguedad_operacion')->insert(['antiguedad' => 'A/S - LIQUIDADA']);
    }
}