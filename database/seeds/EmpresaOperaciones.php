<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
         * 1- 001- VENTA DE TERRENOS
         * 2- 002- SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO.
         * 3- 003- SERVICIOS FUNERARIOS
         * 4- 004- VENTA DE PLANES FUNERARIOS A FUTURO
         * 5- 005- SERVICIOS ESPECIALES CON EXTREMIDADES
         * 6- 006- VENTAS EN GRAL.
         */

        /**procesos del cementerio */
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '001', 'descripcion' => 'VENTA DE TERRENOS', 'nombre_corto' => 'TERRENOS']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '002', 'descripcion' => 'SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO', 'nombre_corto' => 'MANTENIMIENTO CEMENTERIO']);
        /**procesos de la funraria */
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '003', 'descripcion' => 'SERVICIOS FUNERARIOS', 'nombre_corto' => 'SERVICIO FUNERARIO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '004', 'descripcion' => 'VENTA DE PLANES FUNERARIOS A FUTURO', 'nombre_corto' => 'PLAN FUNERARIO A FUTURO']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '005', 'descripcion' => 'SERVICIOS ESPECIALES CON EXTREMIDADES', 'nombre_corto' => 'SERVICIO ESPECIAL']);
        DB::table('empresa_operaciones')->insert(['referencia_pago' => '006', 'descripcion' => 'VENTAS EN GRAL.', 'nombre_corto' => 'VENTA GRAL.']);
    }
}