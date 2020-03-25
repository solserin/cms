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

    //retorna los tipos de propiedad
    public function tipoPropiedades()
    {
        return DB::table('tipo_propiedades')->get();
    }


    //retorna los tipos de propiedad
    public function get_propiedades_by_tipo(Request $request)
    {
        //id del conjunto de propieades
        $id_propiedad_tipo = $request->id_propiedad_tipo;
        return
            Propiedades::where('propiedades.tipo_propiedades_id', '=', $id_propiedad_tipo)->get();
    }

    //retorna los datos de columnas_filas para saber en que numero de lote inicia y acaba una fila de una terraza
    public function get_columna_fila_terraza(Request $request)
    {
        //id del conjunto de propieades
        $propiedades_id = $request->propiedades_id;
        $fila = $request->fila;
        return DB::table('columnas_filas')->where('fila', $fila)->where('propiedades_id', $propiedades_id)->get();
    }

    //retorna los tipos de precios y tarifas segun las propiedadad
    public function precios_tarifas()
    {
        return tipoPropiedades::with('precios.tipo')->get();
    }

    /**UPDATE precios de tarifas */
    public function actualizar_precios_tarifas(Request $request)
    {
        //return $request;


        //creando los valores que necesito validar
        request()->validate(
            [
                '*.precio_neto' => [
                    'required',
                    'numeric',
                ],
                '*.enganche_inicial' => [
                    'required',
                    'numeric',
                    'lte:*.precio_neto'
                ],
                '*.meses' => [
                    'required',
                    'integer',
                    'digits_between:1,2'
                ],
            ],

            [
                '*.precio_neto.required' => 'ingrese este dato.',

                '*.enganche_inicial.lte' => 'El pago inicial debe ser menor o igual al precio neto de la propiedad.',
                '*.enganche_inicial.required' => 'ingrese este dato.',

                '*.meses.numeric' => 'ingrese un número de meses correcto.',
                '*.meses.required' => 'ingrese este dato.',
                '*.meses.digits_between' => 'ingrese este dato (2 dígitos máximo).',
            ]
        );



        /*$user_id = $request->user_id;
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
                    'domicilio' => $request->direccion,
                    'telefono' => $request->telefono,
                    'celular' => $request->celular,
                    'nombre_contacto' => $request->nombre_contacto,
                    'tel_contacto' => $request->tel_contacto,
                    'parentesco' => $request->parentesco_contacto,
                    'updated_at' => now(),
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
                    'password' => Hash::make($request->password),
                    'domicilio' => $request->direccion,
                    'telefono' => $request->telefono,
                    'celular' => $request->celular,
                    'nombre_contacto' => $request->nombre_contacto,
                    'tel_contacto' => $request->tel_contacto,
                    'parentesco' => $request->parentesco_contacto,
                    'updated_at' => now(),
                ]
            );
        }
        */
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