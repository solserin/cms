<?php

use Illuminate\Database\Seeder;

class VelatoriosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('velatorios')->insert(
            [
                'velatorio' => 'Aeternus 1',
                'calle' => 'carretera internacional',
                'num_ext' => '58',
                'num_int' => '',
                'colonia' => 'lópez mateos',
                'cp' => '8140',
                'telefonos' => '(669) 983 15 77',
                'fax' => '(669) 983 15 88',
                'funeraria_id' => '1', //funeraria 1
            ]
        );
    }
}