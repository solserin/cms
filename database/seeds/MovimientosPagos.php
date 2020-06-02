<?php

use Illuminate\Database\Seeder;

class MovimientosPagos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('movimientos_pagos')->insert(['movimiento' => 'Abono a Capital']);
        DB::table('movimientos_pagos')->insert(['movimiento' => 'Abono a Intereses']);
        DB::table('movimientos_pagos')->insert(['movimiento' => 'Descuento x Pronto Pago']);
        DB::table('movimientos_pagos')->insert(['movimiento' => 'Descuento a Capital']);
        DB::table('movimientos_pagos')->insert(['movimiento' => 'Complemento x Cancelación']);
    }
}