<?php

use Illuminate\Database\Seeder;

class AjustesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ajustes')->insert(['numero_convenios_sistematizados' => false, 'numero_titulos_sistematizados' => false]);
    }
}