<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ventasReferenciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //aqui agrego las posibles referencias que se pueden aplicar a cobranza
        //referencias para servicios funerarios
        DB::table('ventas_referencias')->insert(['referencia_pagos' => '01', 'tipos_venta_id' => 1, 'tipo_venta' => 'USO INMEDIATO']);
        DB::table('ventas_referencias')->insert(['referencia_pagos' => '02', 'tipos_venta_id' => 1, 'tipo_venta' => 'A FUTURO']);
        //referencias para propieades de cementerio
        DB::table('ventas_referencias')->insert(['referencia_pagos' => '03', 'tipos_venta_id' => 2, 'tipo_venta' => 'USO INMEDIATO']);
        DB::table('ventas_referencias')->insert(['referencia_pagos' => '04', 'tipos_venta_id' => 2, 'tipo_venta' => 'A FUTURO']);
    }
}