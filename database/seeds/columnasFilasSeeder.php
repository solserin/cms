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


            /**propiedad 31 */
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '1' //A
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '2' //B
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '3' //C
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '4' //D
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '5' //E
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '6' //F
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '7' //G
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '8' //H
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '9' //I
            ],
            [
                //id propiedad 31
                'propiedades_id' => '31',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '10' //J
            ],
            /**fin propiedad 31 */



            /**propiedad 32 */
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '2',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '4',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '4',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '13',
                'fin_columna' => '32',
                'fila' => '6' //F
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '16',
                'fin_columna' => '32',
                'fila' => '7' //G
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '19',
                'fin_columna' => '32',
                'fila' => '8' //H
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '22',
                'fin_columna' => '32',
                'fila' => '9' //I
            ],
            [
                //id propiedad 32
                'propiedades_id' => '32',
                'empieza_columna' => '25',
                'fin_columna' => '32',
                'fila' => '10' //J
            ],
            /**fin propiedad 32 */




            /**propiedad 33 */
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '13',
                'fin_columna' => '31',
                'fila' => '1' //A
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '12',
                'fin_columna' => '31',
                'fila' => '2' //B
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '10',
                'fin_columna' => '31',
                'fila' => '3' //C
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '9',
                'fin_columna' => '31',
                'fila' => '4' //D
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '7',
                'fin_columna' => '31',
                'fila' => '5' //E
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '6',
                'fin_columna' => '31',
                'fila' => '6' //F
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '5',
                'fin_columna' => '31',
                'fila' => '7' //G
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '3',
                'fin_columna' => '31',
                'fila' => '8' //H
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '2',
                'fin_columna' => '31',
                'fila' => '9' //I
            ],
            [
                //id propiedad 33
                'propiedades_id' => '33',
                'empieza_columna' => '1',
                'fin_columna' => '31',
                'fila' => '10' //J
            ],
            /**fin propiedad 33 */





            /**propiedad 34 */
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '1' //A
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '2' //B
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '3' //C
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '4' //D
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '5' //E
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '6' //F
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '7' //G
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '8' //H
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '9' //I
            ],
            [
                //id propiedad 34
                'propiedades_id' => '34',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '10' //J
            ],
            /**fin propiedad 34 */



            /**propiedad 35 */
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '6' //F
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '7' //G
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '8' //H
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '9' //I
            ],
            [
                //id propiedad 35
                'propiedades_id' => '35',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '10' //J
            ],
            /**fin propiedad 35 */




            /**propiedad 36 */
            [
                //id propiedad 36
                'propiedades_id' => '36',
                'empieza_columna' => '1',
                'fin_columna' => '14',
                'fila' => '1' //A
            ],
            [
                //id propiedad 36
                'propiedades_id' => '36',
                'empieza_columna' => '1',
                'fin_columna' => '14',
                'fila' => '2' //B
            ],
            [
                //id propiedad 36
                'propiedades_id' => '36',
                'empieza_columna' => '1',
                'fin_columna' => '14',
                'fila' => '3' //C
            ],
            [
                //id propiedad 36
                'propiedades_id' => '36',
                'empieza_columna' => '1',
                'fin_columna' => '14',
                'fila' => '4' //D
            ],
            [
                //id propiedad 36
                'propiedades_id' => '36',
                'empieza_columna' => '1',
                'fin_columna' => '14',
                'fila' => '5' //E
            ],
            /**fin propiedad 36 */

            /**propiedad 37 */
            [
                //id propiedad 37
                'propiedades_id' => '37',
                'empieza_columna' => '7',
                'fin_columna' => '19',
                'fila' => '1' //A
            ],
            [
                //id propiedad 37
                'propiedades_id' => '37',
                'empieza_columna' => '5',
                'fin_columna' => '19',
                'fila' => '2' //B
            ],
            [
                //id propiedad 37
                'propiedades_id' => '37',
                'empieza_columna' => '4',
                'fin_columna' => '19',
                'fila' => '3' //C
            ],
            [
                //id propiedad 37
                'propiedades_id' => '37',
                'empieza_columna' => '2',
                'fin_columna' => '19',
                'fila' => '4' //D
            ],
            [
                //id propiedad 37
                'propiedades_id' => '37',
                'empieza_columna' => '1',
                'fin_columna' => '19',
                'fila' => '5' //E
            ],
            /**fin propiedad 37 */


            /**propiedad 38 */
            [
                //id propiedad 38
                'propiedades_id' => '38',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 38
                'propiedades_id' => '38',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 38
                'propiedades_id' => '38',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 38
                'propiedades_id' => '38',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 38
                'propiedades_id' => '38',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            /**fin propiedad 38 */

            /**propiedad 39 */
            [
                //id propiedad 39
                'propiedades_id' => '39',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 39
                'propiedades_id' => '39',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 39
                'propiedades_id' => '39',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 39
                'propiedades_id' => '39',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 39
                'propiedades_id' => '39',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            /**fin propiedad 39 */


            /**propiedad 40 */
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '19',
                'fila' => '1' //A
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '19',
                'fila' => '2' //B
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '18',
                'fila' => '3' //C
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '18',
                'fila' => '4' //D
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '17',
                'fila' => '5' //E
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '17',
                'fila' => '6' //F
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '16',
                'fila' => '7' //G
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '16',
                'fila' => '8' //H
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '15',
                'fila' => '9' //I
            ],
            [
                //id propiedad 40
                'propiedades_id' => '40',
                'empieza_columna' => '1',
                'fin_columna' => '15',
                'fila' => '10' //J
            ],
            /**fin propiedad 40 */


            /**propiedad 41 */
            [
                //id propiedad 41
                'propiedades_id' => '41',
                'empieza_columna' => '1',
                'fin_columna' => '21',
                'fila' => '1' //A
            ],
            [
                //id propiedad 41
                'propiedades_id' => '41',
                'empieza_columna' => '1',
                'fin_columna' => '21',
                'fila' => '2' //B
            ],
            [
                //id propiedad 41
                'propiedades_id' => '41',
                'empieza_columna' => '1',
                'fin_columna' => '21',
                'fila' => '3' //C
            ],
            [
                //id propiedad 41
                'propiedades_id' => '41',
                'empieza_columna' => '1',
                'fin_columna' => '20',
                'fila' => '4' //D
            ],
            [
                //id propiedad 41
                'propiedades_id' => '41',
                'empieza_columna' => '1',
                'fin_columna' => '20',
                'fila' => '5' //E
            ],
            /**fin propiedad 41 */


            /**propiedad 42 */
            [
                //id propiedad 42
                'propiedades_id' => '42',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '1' //A
            ],
            [
                //id propiedad 42
                'propiedades_id' => '42',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '2' //B
            ],
            [
                //id propiedad 42
                'propiedades_id' => '42',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '3' //C
            ],
            [
                //id propiedad 42
                'propiedades_id' => '42',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '4' //D
            ],
            [
                //id propiedad 42
                'propiedades_id' => '42',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '5' //E
            ],
            /**fin propiedad 42 */



            /**propiedad 43 */
            [
                //id propiedad 43
                'propiedades_id' => '43',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 43
                'propiedades_id' => '43',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 43
                'propiedades_id' => '43',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 43
                'propiedades_id' => '43',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 43
                'propiedades_id' => '43',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            /**fin propiedad 43 */



            /**propiedad 44 */
            [
                //id propiedad 44
                'propiedades_id' => '44',
                'empieza_columna' => '1',
                'fin_columna' => '23',
                'fila' => '1' //A
            ],
            [
                //id propiedad 44
                'propiedades_id' => '44',
                'empieza_columna' => '1',
                'fin_columna' => '23',
                'fila' => '2' //B
            ],
            [
                //id propiedad 44
                'propiedades_id' => '44',
                'empieza_columna' => '1',
                'fin_columna' => '22',
                'fila' => '3' //C
            ],
            [
                //id propiedad 44
                'propiedades_id' => '44',
                'empieza_columna' => '1',
                'fin_columna' => '22',
                'fila' => '4' //D
            ],
            [
                //id propiedad 44
                'propiedades_id' => '44',
                'empieza_columna' => '1',
                'fin_columna' => '22',
                'fila' => '5' //E
            ],
            /**fin propiedad 44 */



            /**propiedad 45 */
            [
                //id propiedad 45
                'propiedades_id' => '45',
                'empieza_columna' => '4',
                'fin_columna' => '33',
                'fila' => '1' //A
            ],
            [
                //id propiedad 45
                'propiedades_id' => '45',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '2' //B
            ],
            [
                //id propiedad 45
                'propiedades_id' => '45',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '3' //C
            ],
            [
                //id propiedad 45
                'propiedades_id' => '45',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '4' //D
            ],
            [
                //id propiedad 45
                'propiedades_id' => '45',
                'empieza_columna' => '1',
                'fin_columna' => '33',
                'fila' => '5' //E
            ],
            /**fin propiedad 45 */


            /**propiedad 46 */
            [
                //id propiedad 46
                'propiedades_id' => '46',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '1' //A
            ],
            [
                //id propiedad 46
                'propiedades_id' => '46',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '2' //B
            ],
            [
                //id propiedad 46
                'propiedades_id' => '46',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '3' //C
            ],
            [
                //id propiedad 46
                'propiedades_id' => '46',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '4' //D
            ],
            [
                //id propiedad 46
                'propiedades_id' => '46',
                'empieza_columna' => '1',
                'fin_columna' => '32',
                'fila' => '5' //E
            ],
            /**fin propiedad 46 */



            /**propiedad 47 */
            [
                //id propiedad 47
                'propiedades_id' => '47',
                'empieza_columna' => '1',
                'fin_columna' => '27',
                'fila' => '1' //A
            ],
            [
                //id propiedad 47
                'propiedades_id' => '47',
                'empieza_columna' => '1',
                'fin_columna' => '26',
                'fila' => '2' //B
            ],
            [
                //id propiedad 47
                'propiedades_id' => '47',
                'empieza_columna' => '1',
                'fin_columna' => '26',
                'fila' => '3' //C
            ],
            [
                //id propiedad 47
                'propiedades_id' => '47',
                'empieza_columna' => '1',
                'fin_columna' => '26',
                'fila' => '4' //D
            ],
            [
                //id propiedad 47
                'propiedades_id' => '47',
                'empieza_columna' => '1',
                'fin_columna' => '25',
                'fila' => '5' //E
            ],
            /**fin propiedad 47 */
        ];

        foreach ($secciones as $key) {
            DB::table('columnas_filas')->insert([
                'propiedades_id' => $key['propiedades_id'],
                'empieza_columna' => $key['empieza_columna'],
                'fin_columna' => $key['fin_columna'],
                'fila' => $key['fila']
            ]);
        }
    }
}