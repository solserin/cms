<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class columnasFilasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**ordenando las propiedades cuadriplex para saber las areas activas y cuales no */
        $secciones = [

            /**propiedad 30 */
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '6' //F
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '7' //G
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '8' //H
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '9' //I
            ],
            [
                //id propiedad 30
                'propiedades_id' => '30',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '10' //J
            ],
            /**fin propiedad 30 */
        ];

        foreach ($secciones as $key) {
            DB::table('propiedades')->insert([
                'propiedad_indicador' => $key['propiedad_indicador'],
                'filas' => $key['filas'],
                'columnas' => $key['columnas'],
                'tipo_propiedades_id' => $key['tipo_propiedades_id']
            ]);
        }
    }
}