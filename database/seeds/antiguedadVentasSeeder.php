<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class antiguedadVentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //decide si la venta fue antes del sistema (por lo del manejo del formato de solicitud, convenio y titulo)
        DB::table('antiguedad_ventas')->insert(['antiguedad' => 'NUEVA VENTA']);
        DB::table('antiguedad_ventas')->insert(['antiguedad' => 'A/S SIN LIQUIDAR']);
        DB::table('antiguedad_ventas')->insert(['antiguedad' => 'A/S - LIQUIDADA']);
    }
}