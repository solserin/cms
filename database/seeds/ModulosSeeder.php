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
            [
                //1
                'modulo' => 'Usuarios',
                'icon' => 'fa-users',
                'parent_modulo_id' => '0',
                'url' => '/usuarios',
                'secciones_id' => '1'
            ],
            [
                //2
                'modulo' => 'Inventario',
                'icon' => 'icon-box',
                'parent_modulo_id' => '0',
                'url' => '',
                'secciones_id' => '1'
            ],
            [
                //3
                'modulo' => 'Proveedores',
                'icon' => 'icon-box',
                'parent_modulo_id' => '2',
                'url' => '/proveedores',
                'secciones_id' => '1'
            ],
            [
                //4
                'modulo' => 'Almacen',
                'icon' => 'icon-box',
                'parent_modulo_id' => '2',
                'url' => '/almacen',
                'secciones_id' => '1'
            ],
            [
                //5
                'modulo' => 'Reportes',
                'icon' => 'icon-box',
                'parent_modulo_id' => '2',
                'url' => '/reportes',
                'secciones_id' => '1'
            ],
            [
                //6
                'modulo' => 'Ventas',
                'icon' => 'fa-users',
                'parent_modulo_id' => '0',
                'url' => '/ventas',
                'secciones_id' => '2'
            ],
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