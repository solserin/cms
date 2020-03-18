<?php

namespace App\Http\Controllers;

use App\User;
use App\Propiedades;
use App\tipoPropiedades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CementerioController extends ApiController
{
    public function get_cementerio(Request $request)
    {
        return
            Propiedades::with('filas_columnas')->with('tipoPropiedad')->orderBy('tipo_propiedades_id', 'asc')->get();
    }

    public function propiedadesById(Request $request)
    {
        //id del conjunto de propieades
        $id_propiedad = $request->id_propiedad;
        return
            Propiedades::with('filas_columnas')->with('tipoPropiedad')->orderBy('tipo_propiedades_id', 'asc')->where('propiedades.id', '=', $id_propiedad)->get();
    }





    public function get_usuarios_para_vendedores()
    {
        return (User::select(
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
            ->where('usuarios.roles_id', '>', '1') //no muestro super usuarios
            ->get());
    }

    //retorna que tipo de venta es de la propiedad, si es de uso inmediato o a futuro
    public function get_ventas_referencias_propiedades()
    {
        return DB::table('ventas_referencias')->where('tipos_venta_id', 1)->get();
    }
}