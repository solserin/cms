<?php

use Illuminate\Database\Seeder;

class AjustesInteresesPropiedadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ajustes_intereses_propiedades')->insert(
            [
                'tasa_fija_anual' => 12,
                'dias_antes_vencimiento' => 5,
                'maximo_dias_retraso' => 120,
                'porcentaje_pena_convencional_minima' => 60,
                'minima_partes_cubiertas' => 3,
                'maximo_pagos_vencidos' => 3
            ]
        );
    }
}