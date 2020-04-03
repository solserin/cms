<?php

use Illuminate\Database\Seeder;

class SalasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('salas')->insert(
            [
                'velatorios_id' => 1,
                'sala' => 'sala 1',
            ]
        );
        DB::table('salas')->insert(
            [
                'velatorios_id' => 1,
                'sala' => 'sala 2',
            ]
        );
        DB::table('salas')->insert(
            [
                'velatorios_id' => 1,
                'sala' => 'sala 3',
            ]
        );
    }
}