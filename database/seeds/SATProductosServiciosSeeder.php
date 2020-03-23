<?php

use Illuminate\Database\Seeder;

class SATProductosServiciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_productos_servicios')->insert(['clave' => '47121703', 'descripcion'=> 'Urnas de incineración o accesorios']);
        DB::table('sat_productos_servicios')->insert(['clave' => '47121705', 'descripcion'=> 'Bolsas de arena para urnas de incineración']);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131501', 'descripcion'=> 'Cajas de cenizas funerarias o urnas de cremación']);
        DB::table('sat_productos_servicios')->insert(['clave' => '48131502', 'descripcion'=> 'Ataúdes']);
        DB::table('sat_productos_servicios')->insert(['clave' => '25101934', 'descripcion'=> 'Coche fúnebre', 'similar' => 'Carroza fúnebre']);
        DB::table('sat_productos_servicios')->insert(['clave' => '60106504', 'descripcion'=> 'Servicio de mesa para ritos fúnebres']);
        DB::table('sat_productos_servicios')->insert(['clave' => '95121503', 'descripcion'=> 'Cafetería']);
        DB::table('sat_productos_servicios')->insert(['clave' => '90101700', 'descripcion'=> 'Servicios de cafetería']);
        DB::table('sat_productos_servicios')->insert(['clave' => '56121400', 'descripcion'=> 'Mobiliario de cafetería y comedor']);
    }
}
