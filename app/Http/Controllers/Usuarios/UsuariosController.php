<?php
//Commentario
//pruebas de git
namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\EmpresaController;
use App\Puestos;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PDF;

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
        if (!$resultado->isEmpty()) {
            if (Hash::check($request->password, $resultado[0]->password)) {
                $client = new \GuzzleHttp\Client();
                try {
                    $response = $client->request('POST', config('services.passport.login_endpoint'), [
                        'form_params' => [
                            'grant_type'    => 'password',
                            'client_id'     => config('services.passport.client_id'),
                            'client_secret' => config('services.passport.client_secret'),
                            'username'      => $request->username,
                            'password'      => $request->password,
                        ],
                    ]);
                    return $response->getBody();
                } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                    if ($e->getCode() == 400) {
                        return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', $e->getCode());
                    } else if ($e->getCode() == 401) {
                        return $this->errorResponse('Error. Usuario y/o Password incorrecto.', $e->getCode());
                    }
                    return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', $e->getCode());
                }
            } else {
                return $this->errorResponse('Error. Contraseña incorrecta.', 409);
            }

        } else {
            return $this->errorResponse('Error. Usuario no registrado.', 409);
        }
    }

    /**REFRESH TOKEN */
    public function refresh_token(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $request->refresh_token,
                    'client_id'     => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                ],
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
            $resultado = DB::table('usuarios')
                ->select('modulos.status as mod_status', 'secciones.id', 'seccion', 'secciones_id', 'url', 'parent_modulo_id', 'modulo', 'modulos.id as modulo_id', 'secciones.icon as iconseccion', 'modulos.icon as moduloicon')
                ->join('roles', 'usuarios.roles_id', '=', 'roles.id')
                ->join('roles_permisos', 'roles_permisos.roles_id', '=', 'roles.id')
                ->join('permisos', 'roles_permisos.permisos_id', '=', 'permisos.id')
                ->join('modulos', 'permisos.modulos_id', '=', 'modulos.id')
                ->join('secciones', 'secciones.id', '=', 'modulos.secciones_id')
                ->where('usuarios.id', '=', $request->user()->id)
                ->whereNotIn('modulos.id', [10, 13, 14])
            //->where('usuarios.roles_id', '=', $request->user()->roles_id)
                ->orderBy('secciones.id', 'asc')
                ->orderBy('modulos.id', 'asc')
                ->distinct('secciones_id')
            //->toSql();
                ->get();
            //return $resultado;

            //haciendo las secciones que involucra este usuario
            $secciones_detectadas = [];
            $seccion              = 0;
            foreach ($resultado as $key) {
                if ($seccion == 0) {
                    //acaba de empezar el ciclo
                    $seccion = $key->secciones_id;
                    //primer push
                    array_push($secciones_detectadas, [
                        'id'          => $key->secciones_id,
                        'seccion'     => $key->seccion,
                        'iconseccion' => $key->iconseccion,
                    ]);
                } else {
                    if ($key->secciones_id != $seccion) {
                        $seccion = $key->secciones_id;
                        array_push($secciones_detectadas, [
                            'id'          => $key->secciones_id,
                            'seccion'     => $key->seccion,
                            'iconseccion' => $key->iconseccion,
                        ]);
                    }
                }
            }
            //return $secciones_detectadas;

            //return $secciones_detectadas;
            //fin de sacar las secciones que le competen a este usuario
            /**todos los modulos 'independientes y tipos grupo' */
            $grupos_modulos_todos = DB::table('modulos')
                ->select('status', 'modulo', 'modulos.id', 'parent_modulo_id', 'url', 'secciones_id', 'modulos.icon')
                ->where('parent_modulo_id', '=', 0)
                ->get();
            /**fin de todos los modulos */
            //return $grupos_modulos_todos;

            $menu = [];
            //armando el menu
            foreach ($secciones_detectadas as $seccion) {
                //recorriendo cada seccion
                $modulos               = [];
                $grupos                = [];
                $modulos_ids_agregados = [];
                foreach ($grupos_modulos_todos as $grupo) {
                    //aqui checo cuales grupos pertenecen a que seccion
                    if ($grupo->secciones_id == $seccion['id'] && !in_array($grupo->id, $modulos_ids_agregados)) {
                        //aqui comenzamos a ver que tipo de modulo es, si es independiente o pertenece a un submenu
                        //no lleva parent
                        if ($grupo->url != '') {
                            //es un modulo independiente
                            //recorremos el $resultado, son los modulos a los que tiene permiso el usuario segun su rol
                            foreach ($resultado as $agrupados) {
                                if ($agrupados->modulo_id == $grupo->id) {
                                    //si se encuentra este modulo de tipo independiente sin grupo
                                    /**verificando si esta disponible para el resto de usuarios o solo para el superusuario */
                                    if ($agrupados->mod_status == 1 || $request->user()->id == 1) {
                                        array_push($grupos, [
                                            'status'       => $agrupados->mod_status,
                                            'id'           => $grupo->id,
                                            'url'          => $grupo->url,
                                            'name'         => $grupo->modulo,
                                            'slug'         => $grupo->modulo,
                                            'secciones_id' => $grupo->secciones_id,
                                            'icon'         => $grupo->icon,
                                        ]);
                                    }
                                    //se agrega a los modulos que ya fueron agregados al menue
                                    array_push(
                                        $modulos_ids_agregados,
                                        $grupo->id
                                    );
                                    break;
                                }
                            }
                        } else {
                            $modulos = [];
                            //haciendo el submenu
                            //este es en caso es un modulo de tipo agrupado
                            foreach ($resultado as $agrupados) {
                                //se agregan todos los modulos que pertenezcan a este grupo segun su parent_modulo_id
                                if ($agrupados->parent_modulo_id == $grupo->id) {
                                    /**verificando si esta disponible para el resto de usuarios o solo para el superusuario */
                                    if ($agrupados->mod_status == 1 || $request->user()->id == 1) {
                                        array_push($modulos, [
                                            'status'       => $agrupados->mod_status,
                                            'id'           => $agrupados->modulo_id,
                                            'url'          => $agrupados->url,
                                            'name'         => $agrupados->modulo,
                                            'slug'         => $agrupados->modulo,
                                            'secciones_id' => $agrupados->secciones_id,
                                            'icon'         => $agrupados->moduloicon,
                                        ]);
                                    }
                                    //se agregan a esta lista pqra que no vuelvan a ser tomados en cuenta
                                    array_push(
                                        $modulos_ids_agregados,
                                        $grupo->id
                                    );
                                }
                            }
                            //se crea el grupo y se le adjunta su submenu con todos los modulos registrados en la instruccion anterior

                            if (count($modulos) > 0) {
                                array_push($grupos, [
                                    'id'           => $grupo->id,
                                    'url'          => null,
                                    'name'         => $grupo->modulo,
                                    'icon'         => $grupo->icon,
                                    'secciones_id' => $grupo->secciones_id,
                                    'submenu'      => $modulos,
                                ]);
                            }
                        }
                    }
                }
                //se crea el menu perteneciente a la seccion en cola
                array_push($menu, [
                    'header' => $seccion['seccion'],
                    'icon'   => $seccion['iconseccion'],
                    'items'  => $grupos,
                ]);
            }

            return $menu;
        } else
        //no existe el token y regresamos un codigo de error
        {
            return $this->errorResponse('Usuario no autenticado', 401);
        }

    }

    /**OBTIENE TODOS LOS PERMISOS DEL USUARIO EN EL SISTEMA TOMANDO COMO PARAMETRO EL ACCESS TOKEN */
    public function get_perfil(Request $request)
    {
        if ($request->user()) {
            /**RECIBIMOS COMO PARAMETRO EL TOKEN PARA OBTENER LOS PERMISOS DEL USUARIO*/
            $resultado = DB::table('usuarios')
                ->select(
                    'usuarios.id as user_id',
                    'telefono',
                    'celular',
                    'roles_id',
                    'rol',
                    'descripcion',
                    'nombre',
                    'email',
                    'imagen',
                    'usuarios.status as user_status',
                    DB::raw('(CASE
                        WHEN usuarios.genero = "1" THEN "Hombre"
                        ELSE "Mujer"
                        END) AS genero_des')
                )
                ->join('roles', 'usuarios.roles_id', '=', 'roles.id')
                ->where('usuarios.id', '=', $request->user()->id)
                ->get();
            return $resultado;
        } else
        //no existe el token y regresamos un codigo de error
        {
            return $this->errorResponse('Usuario no autenticado', 401);
        }

    }

    public function get_usuarioById(Request $request)
    {
        return
        User::select(
            'usuarios.id',
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
            'domicilio',
            'telefono',
            'celular',
            'tel_contacto',
            'nombre_contacto',
            'parentesco',
            DB::raw('(CASE
                        WHEN usuarios.genero = "1" THEN "Hombre"
                        ELSE "Mujer"
                        END) AS genero_des')
        )
            ->with('puestos')
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
        {
            return $this->errorResponse('Usuario no autenticado', 401);
        }

    }

    /**AGREGAR USUARIOS */
    public function add_usuario(Request $request)
    {
        request()->validate(
            [
                'rol_id'   => 'required',
                'genero'   => 'required',
                'nombre'   => 'required',
                'puestos'  => 'required',
                'usuario'  => 'required|email|unique:usuarios,email',
                'password' => 'required',
                'repetir'  => 'required|same:password',
            ],
            [
                'puestos.required'  => 'Debe seleccionar al menos un puesto para este empleado.',
                'genero.required'   => 'Ingrese el género del usuario.',
                'rol_id.required'   => 'Ingrese el rol del usuario.',
                'nombre.required'   => 'Ingrese el nombre del usuario.',
                'usuario.required'  => 'Ingrese el email del usuario.',
                'usuario.email'     => 'El email debe ser un correo válido.',
                'password.required' => 'debe ingresar una contraseña.',
                'repetir.required'  => 'debe confirmar la contraseña.',
                'repetir.same'      => 'Las contraseñas no coinciden.',
                'unique'            => 'Este nombre de usuario ya ha sido registrado.',
            ]
        );

        try {
            DB::beginTransaction();

            $id_user = DB::table('usuarios')->insertGetId(
                [
                    'roles_id'        => $request->rol_id,
                    'genero'          => $request->genero,
                    'nombre'          => $request->nombre,
                    'email'           => $request->usuario,
                    'password'        => Hash::make($request->password),
                    'domicilio'       => $request->direccion,
                    'telefono'        => $request->telefono,
                    'celular'         => $request->celular,
                    'nombre_contacto' => $request->nombre_contacto,
                    'tel_contacto'    => $request->tel_contacto,
                    'parentesco'      => $request->parentesco_contacto,
                    'created_at'      => now(),
                ]
            );

            /**inserto cada uno de los puestos que tiene este usuario */
            foreach ($request->puestos as $puesto) {
                DB::table('usuarios_puestos')->insert(
                    [
                        'usuarios_id' => $id_user,
                        'puestos_id'  => $puesto,
                    ]
                );
            }
            DB::commit();
            return $id_user;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**UPDATE USUARIOS */
    public function update_usuario(Request $request)
    {
        $user_id = $request->user_id;
        request()->validate(
            [
                'rol_id'   => 'required',
                'genero'   => 'required',
                'puestos'  => 'required',
                'nombre'   => 'required',
                'usuario'  => [
                    'required',
                    'email',
                    Rule::unique('usuarios', 'email')->ignore($user_id),
                ],
                'password' => 'required',
                'repetir'  => 'required|same:password',
            ],
            [
                'puestos.required'  => 'Debe seleccionar al menos un puesto para este empleado.',
                'genero.required'   => 'Ingrese el género del usuario.',
                'rol_id.required'   => 'Ingrese el rol del usuario.',
                'nombre.required'   => 'Ingrese el nombre del usuario.',
                'usuario.required'  => 'Ingrese el email del usuario.',
                'usuario.email'     => 'El email debe ser un correo válido.',
                'password.required' => 'debe ingresar una contraseña.',
                'repetir.required'  => 'debe confirmar la contraseña.',
                'repetir.same'      => 'Las contraseñas no coinciden.',
                'unique'            => 'Este nombre de usuario ya ha sido registrado.',
            ]
        );

        //con cambio de contraseña

        try {
            DB::beginTransaction();

            /**eliminadmos los puestos asociados para crear los nuevos */
            DB::table('usuarios_puestos')->where('usuarios_id', $user_id)->delete();

            //actualizando los datos del usuario
            if ($request->password == 'nochanges') {
                DB::table('usuarios')->where('id', $user_id)->update(
                    [
                        'roles_id'        => $request->rol_id,
                        'genero'          => $request->genero,
                        'nombre'          => $request->nombre,
                        'email'           => $request->usuario,
                        'domicilio'       => $request->direccion,
                        'telefono'        => $request->telefono,
                        'celular'         => $request->celular,
                        'nombre_contacto' => $request->nombre_contacto,
                        'tel_contacto'    => $request->tel_contacto,
                        'parentesco'      => $request->parentesco_contacto,
                        'updated_at'      => now(),
                    ]
                );
            } else {
                //con cambio de contraseñas
                DB::table('usuarios')->where('id', $user_id)->update(
                    [
                        'roles_id'        => $request->rol_id,
                        'genero'          => $request->genero,
                        'nombre'          => $request->nombre,
                        'email'           => $request->usuario,
                        'password'        => Hash::make($request->password),
                        'domicilio'       => $request->direccion,
                        'telefono'        => $request->telefono,
                        'celular'         => $request->celular,
                        'nombre_contacto' => $request->nombre_contacto,
                        'tel_contacto'    => $request->tel_contacto,
                        'parentesco'      => $request->parentesco_contacto,
                        'updated_at'      => now(),
                    ]
                );
            }

            /**inserto cada uno de los puestos que tiene este usuario */
            foreach ($request->puestos as $puesto) {
                DB::table('usuarios_puestos')->insert(
                    [
                        'usuarios_id' => $user_id,
                        'puestos_id'  => $puesto,
                    ]
                );
            }

            DB::commit();
            return $user_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    //se actualiza el perfil de cada usuario desde perfil vue
    public function actualizar_perfil(Request $request)
    {
        $id_usuario = $request->user()->id;
        $usuario    = User::where('id', $id_usuario)->get()->first();

        request()->validate(
            [
                'password'        => 'same:repetirPassword',
                'repetirPassword' => 'same:password',
            ],
            [
                'same' => 'Las contraseñas no coinciden.',
            ]
        );
        //cambios de datos

        return DB::table('usuarios')->where('id', $id_usuario)->update(
            [
                'password'   => trim($request->password) === '' ? $usuario->password : Hash::make($request->password),
                'imagen'     => trim($request->imagen) === '' ? $usuario->imagen : $request->imagen,
                'updated_at' => now(),
            ]
        );
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

    public function pdfs(Request $request)
    {
        $status = $request->status;
        $rol_id = $request->rol_id;
        $nombre = $request->nombre;

        $res = User::select(
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
                        END) AS genero_des'),
            DB::raw('(CASE
                        WHEN usuarios.status = "1" THEN "Activo"
                        ELSE "Sin acceso"
                        END) AS status_des')
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
            ->get();

        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $pdf->setOptions([
            'title'       => 'Reporte de Usuarios',
            'footer-html' => view('footer'),
            'header-html' => view('header'),
        ]);
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 15);

        return $pdf->inline();
    }

    public function get_puestos()
    {
        return Puestos::get();
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
