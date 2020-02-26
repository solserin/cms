<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modulos = [
            //1
            [
                'modulo' => 'Usuarios',
                'icon' => 'UserIcon',
                'parent_modulo_id' => '0',
                'url' => '/usuarios',
                'secciones_id' => '1'
            ],
            [
                //2
                'modulo' => 'Empresa',
                'icon' => 'BriefcaseIcon',
                'parent_modulo_id' => '0',
                'url' => '/empresa',
                'secciones_id' => '1'
            ]
        ];

        foreach ($modulos as $key) {
            DB::table('modulos')->insert([
                'modulo' => $key['modulo'],
                'icon' => $key['icon'],
                'parent_modulo_id' => $key['parent_modulo_id'],
                'url' => $key['url'],
                'secciones_id' => $key['secciones_id']
            ]);
        }
    }
}