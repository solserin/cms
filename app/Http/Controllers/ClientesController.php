<?php

namespace App\Http\Controllers;

use App\Clientes;
use App\Nacionalidades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientesController extends ApiController
{
    public function get_nacionalidades()
    {
        /**todas las nacionalidades */
        return Nacionalidades::get();
    }

    /**obtiene todas los clientes segun los parametros recibidos */
    public function get_clientes(Request $request)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $cliente                  = $request->cliente;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;

        $id_cliente   = $request->id_cliente;
        $rfc          = $request->rfc;
        $celular      = $request->celular;
        $nacionalidad = $request->nacionalidad_id;

        $resultado = $this->showAllPaginated(
            Clientes::select(
                'id',
                'nombre',
                'direccion',
                'email',
                'rfc as rfc_raw',
                'status',
                'razon_social',
                'direccion_fiscal',
                'vivo_b as vivo_b_raw',
                DB::Raw('IFNULL( clientes.rfc , "N/A" ) as rfc'),
                DB::Raw('IFNULL( clientes.celular , "N/A" ) as celular'),
                DB::Raw('IF(clientes.vivo_b=1 , "Vivo","Fallecido" ) as vivo_b'),
                'nacionalidades_id',
                'generos_id'
            )->with('nacionalidad')->with('genero')->where(function ($q) use ($status) {
                if ($status != '') {
                    $q->where('clientes.status', $status);
                }
            })
                ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                    if (trim($numero_control) != '') {
                        if ($filtro_especifico_opcion == 1) {

                            $q->where('clientes.id', '=', $numero_control);
                        } else if ($filtro_especifico_opcion == 2) {

                            $q->where('clientes.rfc', '=', $numero_control);
                        } else if ($filtro_especifico_opcion == 3) {

                            $q->where('clientes.celular', '=', $numero_control);
                        } else {

                            $q->where('clientes.email', $numero_control);
                        }
                    }
                })
                ->where(function ($q) use ($cliente) {
                    if (trim($cliente) != '') {
                        $q->where('clientes.nombre', 'like', '%' . $cliente . '%');
                    }
                })
                ->where(function ($q) use ($id_cliente) {
                    if (trim($id_cliente) != '') {
                        $q->where('clientes.id', '=', $id_cliente);
                    }
                })
                ->where(function ($q) use ($celular) {
                    if (trim($celular) != '') {
                        $q->where('clientes.celular', 'like', '%' . $celular . '%');
                    }
                })
                ->where(function ($q) use ($rfc) {
                    if (trim($rfc) != '') {
                        $q->where('clientes.rfc', 'like', '%' . $rfc . '%');
                    }
                })
                ->where(function ($q) use ($nacionalidad) {
                    if (trim($nacionalidad) != '') {
                        $q->where('clientes.nacionalidades_id', '=', $nacionalidad);
                    }
                })
            /**descartando el cliente publico en general */
                ->whereNotIn('clientes.id', [1, 193])
                ->orderBy('clientes.id', 'desc')
                ->get()
        );

        //se retorna el resultado
        return $resultado;
    }

    /**obtiene el cliente por id */
    public function get_cliente_id(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $resultado  =
        Clientes::select(
            '*',
            'vivo_b as vivo_b_raw',
            DB::Raw('IF(clientes.vivo_b=1 , "VIVO","FALLECIDO" ) as vivo_b'),
            'nacionalidades_id',
            'generos_id'
        )->where('clientes.id', '=', $cliente_id)->with('nacionalidad')->with('genero')
            ->first();
        //se retorna el resultado
        return $resultado;
    }

    public function guardar_cliente(Request $request)
    {
        $validaciones = [
            /**personal */
            'nombre'             => 'required',
            'fecha_nac'          => '',
            'direccion'          => 'required',
            'ciudad'             => 'required',
            'estado'             => 'required',
            'nacionalidad.value' => 'required',
            'email'              => '',
            /**fiscal */
            'rfc'                => '',
            'razon_social'       => '',
            'direccion_fiscal'   => '',
        ];

        /**VALIDACIONES CONDICIONADAS*/

        if (trim($request->email)) {
            $validaciones['email'] = 'email|unique:clientes,email';
        }

        if (trim($request->rfc) != '' || trim($request->razon_social) != '' || trim($request->direccion_fiscal) != '') {
            $validaciones['rfc']              = 'required|unique:clientes,rfc';
            $validaciones['razon_social']     = 'required';
            $validaciones['direccion_fiscal'] = 'required';
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'date_format'  => 'Formato de Fecha yyyy-mm-dd',
            'required'     => 'Este dato es obligatorio',
            'email.email'  => 'Ingrese un email válido',
            'email.unique' => 'Este email ya fue registrado',
            'rfc.unique'   => 'Este RFC ya fue registrado',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        return DB::table('clientes')->insertGetId(
            [
                /**informacion fiscal */
                'generos_id'          => (int) $request->genero['value'],
                'nombre'              => $request->nombre,
                'direccion'           => $request->direccion,
                'ciudad'              => $request->ciudad,
                'estado'              => $request->estado,
                'fecha_nac'           => trim($request->fecha_nac) != '' ? date('Y-m-d H:i:s', strtotime($request->fecha_nac)) : null,
                'telefono'            => trim($request->telefono) != '' ? trim($request->telefono) : null,
                'celular'             => trim($request->celular) != '' ? trim($request->celular) : null,
                'telefono_extra'      => trim($request->telefono_extra) != '' ? trim($request->telefono_extra) : null,
                'email'               => trim($request->email) != '' ? trim($request->email) : null,
                'nacionalidades_id'   => (int) $request->nacionalidad['value'],
                /**informacion fiscal */
                'rfc'                 => trim($request->rfc) != '' ? trim($request->rfc) : null,
                'razon_social'        => trim($request->razon_social) != '' ? trim($request->razon_social) : null,
                'direccion_fiscal'    => trim($request->direccion_fiscal) != '' ? trim($request->direccion_fiscal) : null,
                /**datos del contacto */
                'nombre_contacto'     => $request->nombre_contacto,
                'parentesco_contacto' => $request->parentesco_contacto,
                'telefono_contacto'   => trim($request->telefono_contacto),

                'fecha_registro'      => now(),
                'registro_id'         => (int) $request->user()->id,
                'vivo_b'              => $request->status_cliente,
            ]
        );
    }

    /**modificar clientes */
    public function modificar_cliente(Request $request)
    {
        $validaciones = [
            /**personal */
            'nombre'             => 'required',
            'direccion'          => 'required',
            'ciudad'             => 'required',
            'estado'             => 'required',
            'nacionalidad.value' => 'required',
            'email'              => '',
            /**fiscal */
            'rfc'                => '',
            'razon_social'       => '',
            'direccion_fiscal'   => '',
        ];

        /**VALIDACIONES CONDICIONADAS*/

        if (trim($request->email)) {
            $validaciones['email'] = [
                'email',
                Rule::unique('clientes', 'email')->ignore($request->id_cliente_modificar),
            ];
        }

        if (trim($request->rfc) != '' || trim($request->razon_social) != '' || trim($request->direccion_fiscal) != '') {
            $validaciones['rfc'] = [
                'required',
                Rule::unique('clientes', 'rfc')->ignore($request->id_cliente_modificar),
            ];
            $validaciones['razon_social']     = 'required';
            $validaciones['direccion_fiscal'] = 'required';
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'required'     => 'Este dato es obligatorio',
            'email.email'  => 'Ingrese un email válido',
            'email.unique' => 'Este email ya fue registrado',
            'rfc.unique'   => 'Este RFC ya fue registrado',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        $res = DB::table('clientes')->where('id', $request->id_cliente_modificar)->update(
            [
                /**informacion fiscal */
                'generos_id'          => (int) $request->genero['value'],
                'nombre'              => $request->nombre,
                'direccion'           => $request->direccion,
                'ciudad'              => $request->ciudad,
                'estado'              => $request->estado,
                'fecha_nac'           => trim($request->fecha_nac) != '' ? date('Y-m-d H:i:s', strtotime($request->fecha_nac)) : null,
                'telefono'            => trim($request->telefono) != '' ? trim($request->telefono) : null,
                'celular'             => trim($request->celular) != '' ? trim($request->celular) : null,
                'telefono_extra'      => trim($request->telefono_extra) != '' ? trim($request->telefono_extra) : null,
                'email'               => trim($request->email) != '' ? trim($request->email) : null,
                'nacionalidades_id'   => (int) $request->nacionalidad['value'],
                /**informacion fiscal */
                'rfc'                 => trim($request->rfc) != '' ? trim($request->rfc) : null,
                'razon_social'        => trim($request->razon_social) != '' ? trim($request->razon_social) : null,
                'direccion_fiscal'    => trim($request->direccion_fiscal) != '' ? trim($request->direccion_fiscal) : null,
                /**datos del contacto */
                'nombre_contacto'     => $request->nombre_contacto,
                'parentesco_contacto' => $request->parentesco_contacto,
                'telefono_contacto'   => trim($request->telefono_contacto),

                'fecha_modificacion'  => now(),
                'modifico_id'         => (int) $request->user()->id,
                'vivo_b'              => $request->status_cliente,
            ]
        );

        if ($res > 0) {
            return $request->id_cliente_modificar;
        } else {
            return 0;
        }
    }

    /**DELETE CLIENTES */
    public function baja_cliente(Request $request)
    {
        $cliente_id = $request->cliente_id;
        request()->validate(
            [
                'cliente_id' => 'required',
            ],
            [
                'cliente_id.required' => 'El ID del cliente es necesario.',
            ]
        );
        return DB::table('clientes')->where('id', $cliente_id)->update(
            [
                'status' => 0,
            ]
        );
    }

    public function alta_cliente(Request $request)
    {
        $cliente_id = $request->cliente_id;
        request()->validate(
            [
                'cliente_id' => 'required',
            ],
            [
                'cliente_id.required' => 'El ID del cliente es necesario.',
            ]
        );
        return DB::table('clientes')->where('id', $cliente_id)->update(
            [
                'status' => 1,
            ]
        );
    }
}
