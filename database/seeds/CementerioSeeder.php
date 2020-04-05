<?php

use Illuminate\Database\Seeder;

class CementerioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //se inserta un unico registro
        //el modulo de empresa seria solo de modificacion de datos
        DB::table('cementerios')->insert(
            [
                'cementerio' => 'Parque Funerario',
                'calle' => 'Maxipista Mazatlán Culiacán Km. 1',
                'num_ext' => '0',
                'num_int' => '0',
                'colonia' => 'n/a',
                'cp' => 'n/a',
                'ciudad' => 'Mazatlán',
                'estado' => 'Sinaloa',
                'telefono' => '(669) 983 15 77',
                'fax' => '(669) 983 15 88',
                'email' => 'administracion@aeternus.com.mx',
                'funeraria_id' => '1', //funeraria 1
            ]
        );
    }
}