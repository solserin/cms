<?php

use Illuminate\Database\Seeder;

class FacturacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('facturacion')->insert(
            [
                'fecha_renovacion' => now(),
                'fecha_vencimiento' => now(),
                'password' => '0',
                'funeraria_id' => 1
            ]
        );
    }
}