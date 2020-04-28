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
        DB::table('motivos_cancelacion')->insert(['motivo' => 'Falta de pago']);
        DB::table('motivos_cancelacion')->insert(['motivo' => 'A petición del cliente']);
        DB::table('motivos_cancelacion')->insert(['motivo' => 'Error de captura']);
    }
}