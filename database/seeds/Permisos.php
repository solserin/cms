<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Permisos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* -------------------------------------------------------------------------- */
        /*                   creamos los permisos para cada modulos                   */
        /* -------------------------------------------------------------------------- */
        $permisos = [
            /**permisos de empresa */
            [
                'permiso' => 'actualizar información',
                'modulos_id' => 2
            ],
            [
                'permiso' => 'modificar firma electrónica',
                'modulos_id' => 2
            ],
            /**permisos de roles */
            [
                'permiso' => 'crear roles',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'modificar permisos',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'eliminar roles',
                'modulos_id' => 3
            ],
            [
                'permiso' => 'consultar permisos de un rol',
                'modulos_id' => 3
            ],
            /**permisos de usuarios/empleados */
            [
                'permiso' => 'crear usuarios',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'modificar datos de usuario',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'deshabilitar usuarios/empleados',
                'modulos_id' => 4
            ],
            [
                'permiso' => 'consultar información de usuario',
                'modulos_id' => 4
            ],
            /**permisos de clientes */
            [
                'permiso' => 'registrar clientes',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'modificar informacion',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'eliminar clientes',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'consultar información',
                'modulos_id' => 5
            ],
            [
                'permiso' => 'consultar estados de cuenta',
                'modulos_id' => 5
            ],
            /**permisos de cementerio financiamientos */
            [
                'permiso' => 'modificar precios',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'deshabilitar financiamientos',
                'modulos_id' => 7
            ],
            [
                'permiso' => 'consultar planes de financiamientos',
                'modulos_id' => 7
            ],
            /**permisos de cementerio venta de terrenos */
            [
                'permiso' => 'registrar ventas',
                'modulos_id' => 8
            ],
            [
                'permiso' => 'modificar contrato de ventas',
                'modulos_id' => 8
            ],
            [
                'permiso' => 'cancelar contratos',
                'modulos_id' => 8
            ],
            [
                'permiso' => 'consultar contratos',
                'modulos_id' => 8
            ],
            /**permisos de cementerio cobro de cuotas */
            [
                'permiso' => 'crear cobranzas anuales',
                'modulos_id' => 9
            ],
            [
                'permiso' => 'cancelar cobranza anual',
                'modulos_id' => 9
            ],
            [
                'permiso' => 'Actualizar lista de cobranza',
                'modulos_id' => 9
            ],
            /**permisos de invnetario proveedores */
            [
                'permiso' => 'agregar nuevos proveedores',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'modificar datos de proveedor',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'eliminar proveedores',
                'modulos_id' => 11
            ],
            [
                'permiso' => 'consultar datos de proveedor',
                'modulos_id' => 11
            ],
            /**permisos de invnetario articulos */
            [
                'permiso' => 'agregar articulos o servicios',
                'modulos_id' => 12
            ],
            [
                'permiso' => 'Modificar información',
                'modulos_id' => 12
            ],
            [
                'permiso' => 'Deshabilitar artículos o servicio',
                'modulos_id' => 12
            ],
            [
                'permiso' => 'Consultar información de un producto',
                'modulos_id' => 12
            ],
            /**permisos de ajuste de inventario */
            [
                'permiso' => 'Realizar Ajustes de inventario',
                'modulos_id' => 13
            ],
            [
                'permiso' => 'Consultar Ajustes de inventario',
                'modulos_id' => 13
            ],
            /**permisos de inventario crompras */
            [
                'permiso' => 'Capturar compra de mercancía',
                'modulos_id' => 14
            ],
            [
                'permiso' => 'Cancelar compras',
                'modulos_id' => 14
            ],
            [
                'permiso' => 'Consultar detalles de compra',
                'modulos_id' => 14
            ],
            /**permisos de inventario devoluciones */
            [
                'permiso' => 'Hacer devoluciones a proveedor',
                'modulos_id' => 15
            ],
            [
                'permiso' => 'Cancelar devoluciones',
                'modulos_id' => 15
            ],
            [
                'permiso' => 'Consultar devoluciones realizadas',
                'modulos_id' => 15
            ],
            /**permisos de inventario salidas */
            [
                'permiso' => 'Capturar salidas de mercancía del inventario',
                'modulos_id' => 16
            ],
            [
                'permiso' => 'Consultar detalles de salidas',
                'modulos_id' => 16
            ],

            /**permisos de funeraria Financiamientos*/
            [
                'permiso' => 'Crear planes funerarios a futuro',
                'modulos_id' => 18
            ],
            [
                'permiso' => 'Modificar Planes funerarios',
                'modulos_id' => 18
            ],
            [
                'permiso' => 'Deshabilitar planes de venta',
                'modulos_id' => 18
            ],
            [
                'permiso' => 'Consultar precios de planes',
                'modulos_id' => 18
            ],

            /**permisos de funeraria planes a futuro*/
            [
                'permiso' => 'Registrar ventas de planes a futuro',
                'modulos_id' => 19
            ],
            [
                'permiso' => 'Modificar contratos',
                'modulos_id' => 19
            ],
            [
                'permiso' => 'Cancelar contratos',
                'modulos_id' => 19
            ],
            [
                'permiso' => 'Consultar contratos',
                'modulos_id' => 19
            ],
            /**permisos de funeraria servicios funerarios*/
            [
                'permiso' => 'Atender servicios funerarios',
                'modulos_id' => 20
            ],
            [
                'permiso' => 'Modificar contratos de servicios',
                'modulos_id' => 20
            ],
            [
                'permiso' => 'Cancelar servicios funerarios',
                'modulos_id' => 20
            ],
            [
                'permiso' => 'Consultar contratos',
                'modulos_id' => 20
            ],
            /**permisos de funeraria ventas en gral*/
            [
                'permiso' => 'Realizar Ventas',
                'modulos_id' => 21
            ],
            [
                'permiso' => 'Cancelar Ventas',
                'modulos_id' => 21
            ],
            [
                'permiso' => 'Consultar venta',
                'modulos_id' => 21
            ],
            /**permisos de funeraria devolucion de productos*/
            [
                'permiso' => 'Registrar Devoluciones de productos',
                'modulos_id' => 22
            ],
            [
                'permiso' => 'Consultar devolución de productos',
                'modulos_id' => 22
            ],
            /**permisos de  pagos de comisiones*/
            [
                'permiso' => 'Registrar pagos de comisiones',
                'modulos_id' => 24
            ],
            [
                'permiso' => 'Cancelar pagos de comisiones',
                'modulos_id' => 24
            ],
            [
                'permiso' => 'Consultar recibos',
                'modulos_id' => 24
            ],

            /**permisos de finanzas cobranza*/
            [
                'permiso' => 'Recibir pagos',
                'modulos_id' => 26
            ],
            [
                'permiso' => 'Cancelar pagos',
                'modulos_id' => 26
            ],
            [
                'permiso' => 'Consultar pago',
                'modulos_id' => 26
            ],
            /**permisos de facturacion*/
            [
                'permiso' => 'Emitir facturas',
                'modulos_id' => 27
            ],
            [
                'permiso' => 'Cancelar facturas',
                'modulos_id' => 27
            ],
            [
                'permiso' => 'Consultar facturas',
                'modulos_id' => 27
            ],
            /**permisos de reportes*/
            [
                'permiso' => 'Ver reporte de usuarios',
                'modulos_id' => 28
            ],
            [
                'permiso' => 'Ver reporte de clientes',
                'modulos_id' => 28
            ],
        ];

        /**CAPTURANDO LOS PERMISOS EN LA BASE DE DATOS */
        foreach ($permisos as $permiso) {
            DB::table('permisos')->insert([
                'permiso' => $permiso['permiso'],
                'modulos_id' =>  $permiso['modulos_id']
            ]);
        }
    }
}