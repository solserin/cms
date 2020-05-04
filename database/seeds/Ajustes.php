<?php

use Illuminate\Database\Seeder;

class Ajustes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ajustes')->insert(
            [
                'numero_convenios_sistematizados' => 0,
                'numero_titulos_sistematizados' => 0,
            ]
        );
    }
}