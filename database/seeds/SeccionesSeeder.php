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
        $secciones=[
            [
                'seccion'=>'Personal',
                'icon'=>'fa-users'
            ],
            [
                'seccion'=>'Módulos',
                'icon'=>'fa-briefcase'
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
