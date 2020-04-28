<?php

use Illuminate\Database\Seeder;

class ConceptosPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conceptos_pagos')->insert(['concepto' => 'Enganche']);
        DB::table('conceptos_pagos')->insert(['concepto' => 'Abono']);
        DB::table('conceptos_pagos')->insert(['concepto' => 'Pago Único']);
    }
}