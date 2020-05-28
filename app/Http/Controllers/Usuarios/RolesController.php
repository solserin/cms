<?php

namespace App\Http\Controllers\Usuarios;

use App\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Modulos;
use App\Secciones;

class RolesController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showAll(
            Roles::select(
                'id as value',
                'rol as label'
            )
                ->where('status', '=', 1)
                ->where('roles.id', '>', 1)
                ->get()
        );
    }



    public function get_modulos_permisos()
    {
        return $modulos = Secciones::with('modulos.permisos')->get();
    }

    /**get_modulos */
    public function get_modulos()
    {
        //obtengo solo los modulos que no dependen de otros
        //estos son los que me interesan para los usuarios

        $modulos = DB::table('secciones')->select(
            'seccion',
            'modu.id as mod_id',
            'modulo'
        )
            ->join('modulos as modu', 'modu.secciones_id', '=', 'secciones.id')
            ->where(
                DB::raw("(select @sub_modulos:=COUNT(DISTINCT(modulos.id)) FROM modulos WHERE parent_modulo_id=modu.id)"),
                '=',
                0
            )
            ->orderBy('modu.id', 'asc')
            ->get();
        return $this->showAll($modulos);
    }



    /**AGREGAR ROLRES */
    public function add_roles(Request $request)
    {
        //verifico que si el rol esta dado de baja solo se habilite nuevamente
        /*$maxima_cantidad = Roles::where('id', $request->venta_id)->first();
        if (!$maxima_cantidad) {
            return $this->errorResponse('Dato no válido', 404);
        }*/
        request()->validate(
            [
                'rol' => 'required|unique:roles',
                'permisos' => 'required',
            ],
            [
                'required' => 'Este dato es obligatorio.',
                'unique' => 'Este rol ya existe'
            ]
        );

        try {
            DB::beginTransaction();
            $rol_id = DB::table('roles')->insertGetId(
                ['rol' => $request->rol]
            );

            foreach ($request->permisos as $permiso) {
                DB::table('roles_permisos')->insert(
                    [
                        'permisos_id' => ($permiso),
                        'roles_id' => $rol_id
                    ]
                );
            }
            DB::commit();
            return $rol_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
    }

    /**OBTENGO LOS PERMISOS QUE TIENE CADA ROL SOBRE LOS MODULOS DEL SISTEMA */
    public function get_rol_permisos(Request $request)
    {
        $rol_id = $request['rol_id'];
        $modulos = DB::table('secciones')->select(
            'modu.id as mod_id',
            'permisos_id'
        )
            ->join('modulos as modu', 'modu.secciones_id', '=', 'secciones.id')
            ->join('modulos_roles_permisos', 'modulos_roles_permisos.modulos_id', '=', 'modu.id')
            ->where(
                DB::raw("(select @sub_modulos:=COUNT(DISTINCT(modulos.id)) FROM modulos WHERE parent_modulo_id=modu.id)"),
                '=',
                0
            )
            ->where('roles_id', '=', $rol_id)
            ->orderBy('modu.id', 'asc')
            ->orderBy('permisos_id', 'asc')
            ->get();

        $modulos_permisos = [];
        foreach ($modulos as $mod) {
            array_push(
                $modulos_permisos,
                $mod->mod_id . "_" . $mod->permisos_id
            );
        }
        return $modulos_permisos;
    }


    /**OBTENGO LOS PERMISOS QUE TIENE CADA ROL SOBRE LOS MODULOS DEL SISTEMA */
    public function update_rol(Request $request)
    {
        $rol_id = $request['id'];
        $rol = $request['rol_modificar'];
        $permisos = $request->roles_set;

        request()->validate(
            [
                'id' => 'required',
                'rol_modificar' => [
                    Rule::unique('roles', 'rol')->ignore($rol_id),
                ],
                'roles_set' => 'required'
            ],
            [
                'id.required' => 'Debe seleccionar un rol',
                'roles_set.required' => 'Debe seleccionar 1 permiso al menos.',
                'unique' => 'Este rol ya existe'
            ]
        );

        try {
            DB::beginTransaction();
            //al pasar la validacion borro todos los permisos del rol
            DB::table('modulos_roles_permisos')->where('roles_id', '=', $rol_id)->delete();
            if (trim($rol)) {
                //si el rol ha cambiado
                $update = DB::table('roles')->where('id',  $rol_id)->update(['rol' => $rol]);
            }
            $id_modulo = 0;
            $id_permiso = 0;
            $valores = "";
            foreach ($permisos as $item) {
                $valores = explode("_", $item);
                $id_modulo = $valores[0];
                $id_permiso = $valores[1];
                //sacando el valor del modulo
                DB::table('modulos_roles_permisos')->insert(
                    [
                        'modulos_id' => ($id_modulo),
                        'permisos_id' => ($id_permiso),
                        'roles_id' => $rol_id
                    ]
                );
            }
            DB::commit();
            return $rol_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
    }



    /**BAJA LOGICA DEL ROL */
    public function delete_rol(Request $request)
    {
        $rol_id = $request['id'];

        request()->validate(
            [
                'id' => 'required'
            ],
            [
                'id.required' => 'Debe seleccionar un rol',
            ]
        );

        //checar si existe el rol
        $rol = DB::table('roles')->select('id')
            ->where('id', '=', $rol_id)
            ->count();
        if ($rol == 0) {
            //no existe
            return $this->errorResponse('Esto rol no existe en la BD.', 409);
        }


        //verifico si el rol puede ser dado de baja por no tener usuarios asignados
        $usuarios = DB::table('usuarios')->select('id')
            ->where('roles_id', '=', $rol_id)
            ->count();
        if ($usuarios > 0) {
            return $this->errorResponse('Error al eliminar, este rol cuenta con usuarios relacionados.', 409);
        } else {
            $deleted = DB::table('roles')->where('id',  $rol_id)->delete();
            return $this->successResponse('Rol eliminado exitosamente.', 200);
        }
    }



























    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}