<?php

use Illuminate\Database\Seeder;

class EmpresaOperaciones extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 1- 001- VENTA DE TERRENOS DE USO INMEDIATO
         * 2- 002- VENTA DE TERRENOS DE USO A FUTURO
         * 3- 003- SERVICIOS FUNERARIOS DE USO INMEDIATO
         * 4- 004- SERVICIOS FUNERARIOS DE USO A FUTURO
         * 5- 005- VENTA DE ARTICULOS FUNERARIOS
         * 6- 006- VENTA DE CONSUMIBLES
         * 7- 007- COBRO DE CUOTAS DE MANTENIMIENTO ANUAL
         */

        /**procesos del cementerio */
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '001', 'descripcion' => 'VENTA DE TERRENOS DE USO INMEDIATO', 'nombre_corto' => 'TERRENO USO INMEDIATO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '002', 'descripcion' => 'VENTA DE TERRENOS DE USO A FUTURO', 'nombre_corto' => 'TERRENO A FUTURO']);

        /**procesos de la funraria */
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '003', 'descripcion' => 'SERVICIOS FUNERARIOS DE USO INMEDIATO', 'nombre_corto' => 'SERVICIO FUNERARIO INMEDIATO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '004', 'descripcion' => 'SERVICIOS FUNERARIOS DE USO A FUTURO', 'nombre_corto' => 'SERVICIO FUNERARIOS A FUTURO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '005', 'descripcion' => 'VENTA DE ARTICULOS FUNERARIOS', 'nombre_corto' => 'VENTA DE ARTICULO FUNERARIO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '006', 'descripcion' => 'VENTA DE CONSUMIBLES', 'nombre_corto' => 'VENTA DE CONSUMIBLE']);
        /**procesos del cementerio */
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '007', 'descripcion' => 'COBRO DE CUOTAS DE MANTENIMIENTO ANUAL', 'nombre_corto' => 'CUOTA DE MANTENIMIENTO ANUAL']);
    }
}