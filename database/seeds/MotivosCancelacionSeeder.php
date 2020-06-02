<?php

use Illuminate\Database\Seeder;

class MotivosCancelacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 1- falta de pago
         * 2- a peticion del cliente
         * 3- error de captura
         * 4- Otro
         */
        DB::table('motivos_cancelacion')->insert(['motivo' => 'Falta de pago']);
        DB::table('motivos_cancelacion')->insert(['motivo' => 'A petición del cliente']);
        DB::table('motivos_cancelacion')->insert(['motivo' => 'Error de captura']);
        DB::table('motivos_cancelacion')->insert(['motivo' => 'Otro']);
    }
}