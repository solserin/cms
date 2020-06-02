<?php

use Illuminate\Database\Seeder;

class PreciosPropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //uniplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 20000,
                'financiamiento' => 1,
                'subtotal' => 16800,
                'impuestos' => 3200,
                'costo_neto' => 20000,
                'tipo_propiedades_id' => 1,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );


        //duplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 26000.00,
                'financiamiento' => 1,
                'subtotal' => 21840,
                'impuestos' => 4160,
                'costo_neto' => 26000,
                'tipo_propiedades_id' => 2,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );

        //nichos
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 20000,
                'financiamiento' => 1,
                'subtotal' => 16800,
                'impuestos' => 3200,
                'costo_neto' => 20000,
                'tipo_propiedades_id' => 3,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );


        //cuadruplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 50000,
                'financiamiento' => 1,
                'subtotal' => 42000,
                'impuestos' => 8000,
                'costo_neto' => 50000,
                'tipo_propiedades_id' => 4,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );



        //triplex
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 30000,
                'financiamiento' => 1,
                'subtotal' => 25200,
                'impuestos' => 4800,
                'costo_neto' => 30000,
                'tipo_propiedades_id' => 5,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );
        //cuadruplex sin terraza
        DB::table('precios_propiedades')->insert(
            [
                'pago_inicial' => 50000,
                'financiamiento' => 1,
                'subtotal' => 42000,
                'impuestos' => 8000,
                'costo_neto' => 50000,
                'tipo_propiedades_id' => 6,
                'fecha_actualizacion' => now(),
                'actualizo_id' => 1,
                'contado_b' => 1,
                'descripcion' => 'Pago de Contado',
                'descripcion_ingles' => 'Spot Price',
                'status' => 1
            ]
        );
    }
}