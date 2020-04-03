<?php

use Illuminate\Database\Seeder;

class RegistroPublicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('registro_publico')->insert(
            [
                'rep_legal' => 'representante legal',
                't_nep' => '',
                'fecha_tnep' => now(),
                'fe_lic' => '',
                'num_np' => '',
                'ciudad_np' => '',
                'estado_np' => '',
                'rep_legal' => '',
                'estado_rpc' => '',
                'ciudad_rpc' => '',
                'num_rpc' => '',
                'fecha_rpc' => now(),
                'funeraria_id' => '1', //funeraria 1
            ]
        );
    }
}