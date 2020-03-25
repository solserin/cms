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
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 20000, 'precio_neto' => 20000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 1, 'fecha_hora' => now(), 'actualizo_id' => 1]);

        //duplex
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 30000, 'precio_neto' => 30000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 2, 'fecha_hora' => now(), 'actualizo_id' => 1]);

        //nichos
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 20000, 'precio_neto' => 20000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 3, 'fecha_hora' => now(), 'actualizo_id' => 1]);

        //cuadruplex
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 50000, 'precio_neto' => 50000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 4, 'fecha_hora' => now(), 'actualizo_id' => 1]);

        //triplex
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 30000, 'precio_neto' => 30000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 5, 'fecha_hora' => now(), 'actualizo_id' => 1]);

        //cuadruplex sin terraza
        DB::table('precios_propiedades')->insert(['meses' => 0, 'enganche_inicial' => 50000, 'precio_neto' => 50000, 'tipo_precios_id' => 1, 'tipo_propiedades_id' => 6, 'fecha_hora' => now(), 'actualizo_id' => 1]);
    }
}