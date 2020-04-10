<?php

use Illuminate\Database\Seeder;

class TipoPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_pagos')->insert(['tipo' => 'Enganche']);
        DB::table('tipo_pagos')->insert(['tipo' => 'Abono']);
        DB::table('tipo_pagos')->insert(['tipo' => 'Liquidación']);
    }
}