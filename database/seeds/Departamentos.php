<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Departamentos extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**tipo de articulos del inventario */
        $departamentos = [
            [
                'departamento' => 'Artículos Funerarios',
                'categorias' => [
                    'ataudes', 'urnas'
                ]
            ],
            [
                'departamento' => 'Servicios Funerarios',
                'categorias' => [
                    'servicios funerarios'
                ]
            ],
            [
                'departamento' => 'Equipo y Material de Velación',
                'categorias' => [
                    'equipo de velación'
                ]
            ],
            [
                'departamento' => 'Productos de Cafetería',
                'categorias' => [
                    'cafetería'
                ]
            ]
        ];
        try {
            DB::beginTransaction();
            foreach ($departamentos as $departamento) {
                $id = DB::table('departamentos')->insertGetId([
                    'departamento' => $departamento['departamento']
                ]);
                foreach ($departamento['categorias'] as $categoria) {
                    DB::table('categorias')->insert([
                        'categoria' => $categoria,
                        'departamentos_id' => $id
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}