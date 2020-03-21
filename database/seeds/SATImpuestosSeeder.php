<?php

use Illuminate\Database\Seeder;

class SATImpuestosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('sat_impuestos')->insert([
            'id' => 1,
            'clave' => '001',
            'impuesto' => 'ISR',
            'traslado' => 0,
            'retencion' => 1,
            'porcentaje' => 0.00
        ]);
        
        DB::table('sat_impuestos')->insert([
            'id' => 2,
            'clave' => '002',
            'impuesto' => 'IVA',
            'traslado' => 1,
            'retencion' => 1,
            'porcentaje' => 16.00
        ]);
        /*
        DB::table('sat_impuestos')->insert([
            'id' => 3,
            'clave' => '003',
            'impuesto' => 'IEPS',
            'traslado' => 1,
            'retencion' => 1,
            'porcentaje' => 0.0
        ]);
        */
    }
}
