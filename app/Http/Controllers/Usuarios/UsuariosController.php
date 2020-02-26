<?php

namespace App\Http\Controllers\Usuarios;


use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

class UsuariosController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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

    public function login_usuario(Request $request)
    {
        //verifico que el usuario este activo
        $resultado = DB::table('usuarios')
            ->select('*')
            ->where('email', '=', $request->username)
            ->where('status', '=', 1)
            ->get();
        if (count($resultado) > 0) {
            if (Hash::check($request->password, $resultado[0]->password)) {
                $client = new \GuzzleHttp\Client();
                try {
                    $response = $client->request('POST', config('services.passport.login_endpoint'), [
                        'form_params' => [
                            'grant_type' => 'password',
                            'client_id' => config('services.passport.client_id'),
                            'client_secret' => config('services.passport.client_secret'),
                            'username' => $request->username,
                            'password' => $request->password
                        ]
                    ]);
                    return $response->getBody();
                } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                    if ($e->getCode() == 400) {
                        return $this->errorResponse('Error. Usuario y/o Password incorrecto.', $e->getCode());
                    } else if ($e->getCode() == 401) {
                        return $this->errorResponse('Error. Usuario y/o Password incorrecto.', $e->getCode());
                    }

                    return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', $e->getCode());
                }
            }
        } else {
            return $this->errorResponse('Error. Usuario y/o Password incorrecto.', 400);
        }
    }



    /**REFRESH TOKEN */
    public function refresh_token(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $request->refresh_token,
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret')
                ]
            ]);
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            return $this->errorResponse('No autenticado', $e->getCode());
        }
    }
    /**FIN DEL REFRESH TOKEN */

    /**OBTIENE TODOS LOS PERMISOS DEL USUARIO EN EL SISTEMA TOMANDO COMO PARAMETRO EL ACCESS TOKEN */
    public function get_permisos(Request $request)
    {
        //VERIFICA SI EL TOKEN ENVIADO EN EL HEADER EXISTE Y RETORNA LOS DATOS DEL USUARIO AL QUE PERTENECE
        //DICHO TOEKN
        if ($request->user()) {
            /**RECIBIMOS COMO PARAMETRO EL TOKEN PARA OBTENER LOS PERMISOS DEL USUARIO*/
            $resultado = DB::table('usuarios')
                ->select('*', 'modulo', 'modulos.id as modulo_id', 'secciones.icon as iconseccion', 'modulos.icon as moduloicon')
                ->join('roles', 'usuarios.roles_id', '=', 'roles.id')
                ->join('modulos_roles_permisos', 'modulos_roles_permisos.roles_id', '=', 'roles.id')
                ->join('modulos', 'modulos_roles_permisos.modulos_id', '=', 'modulos.id')
                ->join('secciones', 'secciones.id', '=', 'modulos.secciones_id')
                ->where('usuarios.id', '=', $request->user()->id)
                //->where('usuarios.roles_id', '=', $request->user()->roles_id)
                ->orderBy('secciones.id', 'asc')
                ->orderBy('modulos.id', 'asc')
                ->get();
            //return $resultado;
            //GRUPOS (independientes y con submenu)
            $grupos_modulos = DB::table('modulos')
                ->select('modulo', 'modulos.id', 'parent_modulo_id', 'url', 'secciones_id', 'modulos.icon')
                ->join('modulos_roles_permisos', 'modulos_roles_permisos.modulos_id', '=', 'modulos.id')
                ->where('parent_modulo_id', '=', 0)
                ->where('roles_id', '=',  $request->user()->roles_id)
                ->distinct('modulo')
                ->get();
            //return $grupos_modulos;
            $grupos = [];
            foreach ($grupos_modulos as $grupo) {
                //definiendo el tipo de grupo (individual o solo)
                $tiene_submodulos = 0;
                foreach ($resultado as $valor) {
                    if ($valor->parent_modulo_id == $grupo->id) {
                        $tiene_submodulos++;
                        break;
                    }
                }
                if ($tiene_submodulos > 0) {
                    //tiene submodulos
                    //buscando submodulos
                    $submodulos = [];
                    $submodulo = '';
                    foreach ($resultado as $key => $valor) {
                        /**SI EXISTE ALGUNA SECCION PODEMOS COMENZAR */
                        //tomara el primer valor
                        if ($submodulo != $valor->modulos_id) {
                            $submodulo = $valor->modulos_id;
                            if ($grupo->id == $valor->parent_modulo_id) {
                                array_push($submodulos, [
                                    'id' => $valor->id,
                                    'url' => $valor->url,
                                    'name' => $valor->modulo,
                                    'slug' => $valor->modulo,
                                    'secciones_id' => $valor->secciones_id,
                                    'icon' => $valor->icon
                                ]);
                            }
                        }
                    }
                    array_push($grupos, [
                        'id' => $grupo->id,
                        'url' => null,
                        'name' => $grupo->modulo,
                        'icon' => $grupo->icon,
                        'secciones_id' => $grupo->secciones_id,
                        'submenu' => $submodulos
                    ]);
                } else {
                    //es un modulo simple
                    array_push($grupos, [
                        'id' => $grupo->id,
                        'url' => $grupo->url,
                        'name' => $grupo->modulo,
                        'slug' => $grupo->modulo,
                        'secciones_id' => $grupo->secciones_id,
                        'icon' => $grupo->icon
                    ]);
                }
            }

            /**FORMATEO LA RESPUESTA DEL MENU */
            $secciones = [];
            $seccion = '';
            $modulos = [];
            foreach ($resultado as $key => $valor) {
                /**SI EXISTE ALGUNA SECCION PODEMOS COMENZAR */
                if ($valor->secciones_id != $seccion) {
                    //tomara el primer valor
                    $seccion = $valor->secciones_id;
                    $modulos = [];
                    foreach ($grupos as $grupo) {
                        if ($grupo['secciones_id'] == $seccion) {
                            array_push($modulos, $grupo);
                        }
                    }

                    //se integra el array final
                    array_push($secciones, [
                        'header' => $valor->seccion,
                        'icon' => $valor->iconseccion,
                        'items' => $modulos
                    ]);
                }
            }
            return $secciones;
        } else
            //no existe el token y regresamos un codigo de error
            return $this->errorResponse('Usuario no autenticado', 401);
    }


    /**OBTIENE TODOS LOS PERMISOS DEL USUARIO EN EL SISTEMA TOMANDO COMO PARAMETRO EL ACCESS TOKEN */
    public function get_perfil(Request $request)
    {
        if ($request->user()) {
            /**RECIBIMOS COMO PARAMETRO EL TOKEN PARA OBTENER LOS PERMISOS DEL USUARIO*/
            $resultado = DB::table('usuarios')
                ->select('usuarios.id as user_id', 'roles_id', 'rol', 'descripcion', 'nombre', 'email', 'imagen', 'usuarios.status as user_status')
                ->join('roles', 'usuarios.roles_id', '=', 'roles.id')
                ->where('usuarios.id', '=', $request->user()->id)
                ->get();
            return $resultado;
        } else
            //no existe el token y regresamos un codigo de error
            return $this->errorResponse('Usuario no autenticado', 401);
    }


    public function get_usuarioById(Request $request)
    {
        return
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
            ->where('usuarios.id', '=', $request->user_id)
            ->get();
    }



    public function logout_usuario()
    {
        //elimina todos los tokens asociados a el usuario logueado con ese token
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return $this->successResponse('Sesión finalizada con éxito.', 200);
    }

    /**verificar el password para proceder con una accion */
    /**OBTIENE TODOS LOS PERMISOS DEL USUARIO EN EL SISTEMA TOMANDO COMO PARAMETRO EL ACCESS TOKEN */
    public function verificar_password(Request $request)
    {
        if ($request->user()) {
            /**RECIBIMOS COMO PARAMETRO EL TOKEN PARA OBTENER LOS PERMISOS DEL USUARIO*/
            $resultado = DB::table('usuarios')
                ->select('*')
                ->where('id', '=', $request->user()->id)
                ->where('status', '=', 1)
                ->get();
            if (count($resultado) > 0) {
                if (Hash::check($request->params['password'], $resultado[0]->password)) {
                    return $this->successResponse('Operación autorizada', 200);
                }
            }
            //no autorizado
            return $this->errorResponse('Contraseña incorrecta.', 403);
        } else
            //no existe el token y regresamos un codigo de error
            return $this->errorResponse('Usuario no autenticado', 401);
    }




    /**AGREGAR USUARIOS */
    public function add_usuario(Request $request)
    {
        request()->validate(
            [
                'rol_id' => 'required',
                'genero' => 'required',
                'nombre' => 'required',
                'usuario' => 'required|email|unique:usuarios,email',
                'password' => 'required',
                'repetir' => 'required|same:password',
            ],
            [
                'genero.required' => 'Ingrese el género del usuario.',
                'rol_id.required' => 'Ingrese el rol del usuario.',
                'nombre.required' => 'Ingrese el nombre del usuario.',
                'usuario.required' => 'Ingrese el email del usuario.',
                'usuario.email' => 'El email debe ser un correo válido.',
                'password.required' => 'debe ingresar una contraseña.',
                'repetir.required' => 'debe confirmar la contraseña.',
                'repetir.same' => 'Las contraseñas no coinciden.',
                'unique' => 'Este nombre de usuario ya ha sido registrado.'
            ]
        );
        return DB::table('usuarios')->insertGetId(
            [
                'roles_id' => $request->rol_id,
                'genero' => $request->genero,
                'nombre' => $request->nombre,
                'email' => $request->usuario,
                'password' => Hash::make($request->password)
            ]
        );
    }


    /**UPDATE USUARIOS */
    public function update_usuario(Request $request)
    {
        $user_id = $request->user_id;
        request()->validate(
            [
                'rol_id' => 'required',
                'genero' => 'required',
                'nombre' => 'required',
                'usuario' => [
                    'required',
                    'email',
                    Rule::unique('usuarios', 'email')->ignore($user_id),
                ],
                'password' => 'required',
                'repetir' => 'required|same:password',
            ],
            [
                'genero.required' => 'Ingrese el género del usuario.',
                'rol_id.required' => 'Ingrese el rol del usuario.',
                'nombre.required' => 'Ingrese el nombre del usuario.',
                'usuario.required' => 'Ingrese el email del usuario.',
                'usuario.email' => 'El email debe ser un correo válido.',
                'password.required' => 'debe ingresar una contraseña.',
                'repetir.required' => 'debe confirmar la contraseña.',
                'repetir.same' => 'Las contraseñas no coinciden.',
                'unique' => 'Este nombre de usuario ya ha sido registrado.'
            ]
        );
        //con cambio de contraseña

        if ($request->password == 'nochanges') {
            return DB::table('usuarios')->where('id', $user_id)->update(
                [
                    'roles_id' => $request->rol_id,
                    'genero' => $request->genero,
                    'nombre' => $request->nombre,
                    'email' => $request->usuario,
                ]
            );
        } else {
            //con cambio de contraseñas
            return DB::table('usuarios')->where('id', $user_id)->update(
                [
                    'roles_id' => $request->rol_id,
                    'genero' => $request->genero,
                    'nombre' => $request->nombre,
                    'email' => $request->usuario,
                    'password' => Hash::make($request->password)
                ]
            );
        }
    }


    /**DELETE USUARIOS */
    public function delete_usuario(Request $request)
    {
        $user_id = $request->user_id;
        request()->validate(
            [
                'user_id' => 'required',
            ],
            [
                'user_id.required' => 'El ID del usuario es necesario.',
            ]
        );
        return DB::table('usuarios')->where('id', $user_id)->update(
            [
                'status' => 0,
            ]
        );
    }

    /**activar USUARIOS */
    public function activate_usuario(Request $request)
    {
        $user_id = $request->user_id;
        request()->validate(
            [
                'user_id' => 'required',
            ],
            [
                'user_id.required' => 'El ID del usuario es necesario.',
            ]
        );
        return DB::table('usuarios')->where('id', $user_id)->update(
            [
                'status' => 1,
            ]
        );
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