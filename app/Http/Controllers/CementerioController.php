<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CementerioController extends ApiController
{
    public function get_list(Request $request)
    {
        $status = $request->status;
        $rol_id = $request->rol_id;
        $nombre = $request->nombre;
        return $this->showAllPaginated(
            User::select(
                'usuarios.id as id_user',
                'nombre',
                'email',
                'genero',
                'imagen',
                'telefono',
                'fecha_alta',
                'roles_id',
                'usuarios.status as estado',
                'rol',
                DB::raw('(CASE 
                        WHEN usuarios.genero = "1" THEN "Hombre"
                        ELSE "Mujer" 
                        END) AS genero_des')
            )
                ->join('roles', 'roles.id', '=', 'usuarios.roles_id')
                //->where('roles_id', ">", 1)
                ->where(function ($q) use ($status) {
                    if ($status != '') {
                        $q->where('usuarios.status', $status);
                    }
                })
                ->where(function ($q) use ($rol_id) {
                    if ($rol_id != '') {
                        $q->where('usuarios.roles_id', $rol_id);
                    }
                })
                ->where(function ($q) use ($nombre) {
                    if ($nombre != '') {
                        $q->where('usuarios.nombre', 'like', '%' . $nombre . '%');
                    }
                })
                ->where('usuarios.roles_id', '>', '1') //no muestro super usuarios
                ->get()
        );
    }
}