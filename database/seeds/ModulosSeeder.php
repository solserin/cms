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

        $secciones =
            [
                [
                    'seccion' => 'Configuración',
                    'icon' => '',
                ],
                [
                    'seccion' => 'Operaciones',
                    'icon' => '',
                ],
                [
                    'seccion' => 'Reportes',
                    'icon' => '',
                ]
            ];

        $modulos = [
            [ //1
                'modulo' => 'Configuración',
                'icon' => 'SettingsIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 1,
                'status' => 1
            ],
            [ //2
                'modulo' => 'Empresa',
                'icon' => '',
                'parent_modulo_id' => 1,
                'url' => '/configuracion/empresa',
                'secciones_id' => 1,
                'status' => 1
            ],
            [ //3
                'modulo' => 'Roles',
                'icon' => '',
                'parent_modulo_id' => 1,
                'url' => '/configuracion/roles',
                'secciones_id' => 1,
                'status' => 1
            ],
            [ //4
                'modulo' => 'Usuarios',
                'icon' => '',
                'parent_modulo_id' => 1,
                'url' => '/configuracion/usuarios',
                'secciones_id' => 1,
                'status' => 1
            ],

            /**modulos de operaciones */
            [ //5
                'modulo' => 'Clientes',
                'icon' => 'UserIcon',
                'parent_modulo_id' => 0,
                'url' => '/clientes',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //6
                'modulo' => 'Cementerio',
                'icon' => 'PackageIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //7
                'modulo' => 'Financiamientos',
                'icon' => '',
                'parent_modulo_id' => 6,
                'url' => '/cementerio/financiamientos',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //8
                'modulo' => 'Venta de Terrenos',
                'icon' => '',
                'parent_modulo_id' => 6,
                'url' => '/cementerio/ventas',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //9
                'modulo' => 'Cobro de Cuotas',
                'icon' => '',
                'parent_modulo_id' => 6,
                'url' => '/cementerio/cuotas',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //10
                'modulo' => 'Inventario',
                'icon' => 'TruckIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //11
                'modulo' => 'Proveedores',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/proveedores',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //12
                'modulo' => 'Artículos / Servicios',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/articulos',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //13
                'modulo' => 'Ajustes de inventario',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/ajustes',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //14
                'modulo' => 'Compras',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/compras',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //15
                'modulo' => 'Devoluciones',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/devoluciones',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //16
                'modulo' => 'Salidas',
                'icon' => '',
                'parent_modulo_id' => 10,
                'url' => '/inventarios/funeraria/salidas',
                'secciones_id' => 2,
                'status' => 0
            ],


            [ //17
                'modulo' => 'Funeraria',
                'icon' => 'ArchiveIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //18
                'modulo' => 'Financiamientos',
                'icon' => '',
                'parent_modulo_id' => 17,
                'url' => '/funeraria/financiamientos',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //19
                'modulo' => 'Ventas Planes',
                'icon' => '',
                'parent_modulo_id' => 17,
                'url' => '/funeraria/ventas_planes',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //20
                'modulo' => 'Servicios',
                'icon' => '',
                'parent_modulo_id' => 17,
                'url' => '/funeraria/servicios',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //21
                'modulo' => 'Ventas Gral.',
                'icon' => '',
                'parent_modulo_id' => 17,
                'url' => '/funeraria/ventas_generales',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //22
                'modulo' => 'Devoluciones',
                'icon' => '',
                'parent_modulo_id' => 17,
                'url' => '/funeraria/devoluciones',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //23
                'modulo' => 'Pagos',
                'icon' => 'FileTextIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //24
                'modulo' => 'Comisiones',
                'icon' => '',
                'parent_modulo_id' => 23,
                'url' => '/comisiones/pagos',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //25
                'modulo' => 'Finanzas',
                'icon' => 'DollarSignIcon',
                'parent_modulo_id' => 0,
                'url' => '',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //26
                'modulo' => 'Cobranza',
                'icon' => '',
                'parent_modulo_id' => 25,
                'url' => '/finanzas/cobranza',
                'secciones_id' => 2,
                'status' => 1
            ],
            [ //27
                'modulo' => 'Facturación',
                'icon' => '',
                'parent_modulo_id' => 25,
                'url' => '/finanzas/facturacion',
                'secciones_id' => 2,
                'status' => 0
            ],
            [ //28
                'modulo' => 'Reportes',
                'icon' => 'PaperclipIcon',
                'parent_modulo_id' => 0,
                'url' => '/reportes',
                'secciones_id' => 3,
                'status' => 0
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
                'status' => $modulo['status'],
            ]);
        }
    }
}