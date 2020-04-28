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

        //las 3 seciones actuales
        /**
         * Configuración
         * Operaciones
         * 
         */


        $seccion_1 = 1;
        $seccion_2 = 2;

        $secciones =
            [
                [
                    'seccion' => 'Configuración',
                    'icon' => 'SettingsIcon',
                ],
                [
                    'seccion' => 'Operaciones',
                    'icon' => 'PackageIcon',
                ]
            ];

        $modulos = [
            [
                'modulo' => 'Configuración',
                'icon' => 'SettingsIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => $seccion_1,
            ],
            [
                'modulo' => 'Empresa',
                'icon' => 'BriefcaseIcon',
                'parent_modulo_id' => 1,
                'url' => '/configuracion/empresa',
                'secciones_id' => $seccion_1
            ],
            [
                'modulo' => 'Usuarios',
                'icon' => 'UserIcon',
                'parent_modulo_id' => 1,
                'url' => '/configuracion/usuarios',
                'secciones_id' => $seccion_1
            ],

            /**modulos de operaciones */
            [ //4
                'modulo' => 'Clientes',
                'icon' => 'UsersIcon',
                'parent_modulo_id' => 0,
                'url' => '/clientes',
                'secciones_id' => $seccion_2,
            ],
            [ //5
                'modulo' => 'Cementerio',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Venta de Terrenos',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 5,
                'url' => '/inventarios/cementerio/ventas',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Inventario General',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 5,
                'url' => '/inventarios/cementerio/distribucion',
                'secciones_id' => $seccion_2,
            ],
            [ //8
                'modulo' => 'Cobro de Cuotas',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 5,
                'url' => '/inventarios/cementerio/cobranza',
                'secciones_id' => $seccion_2,
            ],
            [ //9
                'modulo' => 'Inventario',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Proveedores.',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 9,
                'url' => '1',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Artículos/Servicios',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 9,
                'url' => '2',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Ajustes de inventario',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 9,
                'url' => '3',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Surtir inventario',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 9,
                'url' => '4',
                'secciones_id' => $seccion_2,
            ],
            [ //14
                'modulo' => 'Salida de Artículos',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 9,
                'url' => '5',
                'secciones_id' => $seccion_2,
            ],
            [ //15
                'modulo' => 'Ventas',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Artículos Funerarios.',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 15,
                'url' => '4',
                'secciones_id' => $seccion_2
            ],
            [ //17
                'modulo' => 'Consumibles',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 15,
                'url' => '12',
                'secciones_id' => $seccion_2
            ],
            [
                'modulo' => 'Servicios Funerarios',
                'icon' => 'UsersIcon',
                'parent_modulo_id' => 0,
                'url' => 'servicios_funerarios',
                'secciones_id' => $seccion_2,
            ],
            [ //19
                'modulo' => 'Cobranza',
                'icon' => 'UsersIcon',
                'parent_modulo_id' => 0,
                'url' => 'cobranza',
                'secciones_id' => $seccion_2,
            ],
            [ //20
                'modulo' => 'Reportes',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => $seccion_2,
            ],
            [
                'modulo' => 'Cobranza.',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 20,
                'url' => 'cobranza',
                'secciones_id' => $seccion_2
            ],
            [
                'modulo' => 'Ventas',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 20,
                'url' => 'ventas',
                'secciones_id' => $seccion_2
            ],
            [
                'modulo' => 'Inventario',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 20,
                'url' => 'inventario',
                'secciones_id' => $seccion_2
            ],
            [
                'modulo' => 'Estados de Cuenta',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 20,
                'url' => 'estados_cuenta',
                'secciones_id' => $seccion_2
            ]
        ];

        foreach ($secciones as $seccion) {
            $seccion_id = DB::table('secciones')->insertGetId([
                'seccion' => $seccion['seccion'],
                'icon' => $seccion['icon']
            ]);
        }

        foreach ($modulos as $modulo) {
            $modulo_id = DB::table('modulos')->insertGetId([
                'modulo' => $modulo['modulo'],
                'icon' =>  $modulo['icon'],
                'parent_modulo_id' => $modulo['parent_modulo_id'],
                'url' =>  $modulo['url'],
                'secciones_id' => $modulo['secciones_id'],
            ]);
        }
    }
}