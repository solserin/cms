<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $secciones = [
            [
                'seccion' => 'Configuración',
                'icon' => 'SettingsIcon'
            ],
            [
                'seccion' => 'Inventarios',
                'icon' => 'PackageIcon'
            ]
        ];

        foreach ($secciones as $key) {
            DB::table('secciones')->insert([
                'seccion' => $key['seccion'],
                'icon' => $key['icon']
            ]);
        }
    }
}