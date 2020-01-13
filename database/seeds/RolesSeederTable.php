<?php

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
        $roles=[
            [
                'rol'=>'SuperUsuario',
                'descripcion'=>'Tiene acceso 100 al sistema y solo los administradores del sistema tienen acceso',
            ],
            [
                'rol'=>'Administrador',
                'descripcion'=>'Tiene 100 % al sistema en operación',
            ]
        ];
        foreach ($roles as $key) {
            DB::table('roles')->insert([
                'rol' => $key['rol'],
                'descripcion' => $key['descripcion']
            ]);
        }
        //aqui inserto los permisos en los roles
        //4 permisos (1-Agregar, 2-Editar, 3-Eliminar y 4-Consultar)
        $roles=DB::table('modulos')->select('id')->where('url','<>','')->get();
        foreach ($roles as $key) {
            for ($i=1; $i < 5; $i++) {
                DB::table('modulos_roles_permisos')->insert([
                    'modulos_id' => $key->id,
                    'roles_id' => 1,
                    'permisos_id' => $i
                ]);
            }
        }
    }
}
