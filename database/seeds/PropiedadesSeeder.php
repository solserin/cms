<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**dando de alta propiedades uniplex*/
        $secciones = [
            [
                'propiedad_indicador' => 'A',
                'filas' => '15', //15 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'B',
                'filas' => '15', //15 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'D',
                'filas' => '15', //15 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'E',
                'filas' => '24', //24 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'M',
                'filas' => '15', //15 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'N',
                'filas' => '14', //14 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'Ñ',
                'filas' => '14', //14 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],
            [
                'propiedad_indicador' => 'O',
                'filas' => '15', //15 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '1'
            ],


            /**AGREGANDO PROPIEADES DUPLEX */
            [
                'propiedad_indicador' => 'C',
                'filas' => '6', //6 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'F',
                'filas' => '21', //21 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'G',
                'filas' => '10', //10 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'H',
                'filas' => '2', //2 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'I',
                'filas' => '10', //10 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'J',
                'filas' => '2', //2 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'K',
                'filas' => '10', //10 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],
            [
                'propiedad_indicador' => 'L',
                'filas' => '1', //1 PROPIEDA
                'columnas' => '1',
                'tipo_propiedades_id' => '2'
            ],

            //DANDO DE ALTA NICHOS
            [
                'propiedad_indicador' => '1',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '2',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '3',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '4',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '5',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '6',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '7',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '8',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '8',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '9',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '10',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '11',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
            [
                'propiedad_indicador' => '12',
                'filas' => '7', //1 PROPIEADES
                'columnas' => '1',
                'tipo_propiedades_id' => '3'
            ],
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