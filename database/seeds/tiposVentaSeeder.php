<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class tiposVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipos_venta')->insert(['tipo' => 'SERVICIOS FUNERARIO']);
        DB::table('tipos_venta')->insert(['tipo' => 'PROPIEDADES EN CEMENTERIO']);
    }
}