<?php

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'rol' => 'SuperUsuario',
                'descripcion' => 'Tiene acceso 100 al sistema y solo los administradores del sistema tienen acceso',
            ],
            [
                'rol' => 'Admin',
                'descripcion' => 'Tiene acceso 100 al sistema, sin permisos administrador de sistemas(puesto).',
            ]
        ];
        foreach ($roles as $key) {
            DB::table('roles')->insert([
                'rol' => $key['rol'],
                'descripcion' => $key['descripcion']
            ]);
        }

        $roles = [
            '1',
            '2'
        ];
        //aqui inserto los permisos en los roles
        //4 permisos (1-Agregar, 2-Editar, 3-Eliminar y 4-Consultar)
        $modulos = DB::table('modulos')->select('id')->where('url', '<>', '')->get();
        foreach ($modulos as $key) {
            $roles_res = DB::table('roles')->get();
            foreach ($roles_res as $rol) {
                for ($i = 1; $i < 5; $i++) {
                    DB::table('modulos_roles_permisos')->insert([
                        'modulos_id' => $key->id,
                        'roles_id' => $rol->id,
                        'permisos_id' => $i
                    ]);
                }
            }
        }
    }
}