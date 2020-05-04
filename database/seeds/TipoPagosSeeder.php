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
        DB::table('tipo_pagos')->insert(['tipo' => 'Abono a Capital']);
        DB::table('tipo_pagos')->insert(['tipo' => 'Abono a Intereses']);
        DB::table('tipo_pagos')->insert(['tipo' => 'Reajuste sistema']);
    }
}