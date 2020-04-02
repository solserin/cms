<?php

namespace App\Http\Controllers;

use App\User;
use App\Propiedades;
use App\SatFormasPago;
use App\tipoPropiedades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CementerioController extends ApiController
{
    public function get_cementerio(Request $request)
    {
        return
            Propiedades::with('filas_columnas')->with('tipoPropiedad')->with('tipoPropiedad.precios')->with('filas_columnas')->orderBy('id', 'asc')->get();
    }

    //obtiene los usuarios para vendedores
    public function get_vendedores()
    {
        return User::get();
    }

    public function get_sat_formas_pago()
    {
        //id del conjunto de propieades

        return
            SatFormasPago::where('clave', '<>', '99')->get();
    }

    /**GUARDAR LA VENTA */
    public function guardar_venta(Request $request)
    {
        //return $request->minima_cuota_inicial;
        //validaciones directas sin condicionales
        $validaciones = [
            //datos de la propiedad
            'tipo_propiedades_id' => 'required|min:1',
            'propiedades_id' => 'required|min:1',
            'ubicacion' => 'required|unique:ventas_propiedades,ubicacion',
            //fin de datos de la propiedad
            //datos de la venta
            'fecha_venta' => 'required|date',
            'ventaAntesdelSistema' => 'required',
            'venta_referencia_id' => 'required',
            'filas.value' => 'required',
            'lotes.value' => '', //modificada segun condiciones
            'vendedor.value' => 'required',

            'num_solicitud' => '',
            'convenio' => '',
            'titulo' => '',

            //info del plan de venta y pagos
            'planVenta.value' => 'required',
            'precio_neto' => 'required|numeric',
            'descuento' => 'nullable|numeric|lte:planVenta.precio_neto',
            'precio_neto' => 'numeric|min:0',
            'enganche_inicial' => 'numeric|min:' . $request->minima_cuota_inicial . '|' . 'max:' . $request->maxima_cuota_inicial,
            'opcionPagar.value' => 'required',
            'formaPago.value' => 'required',
            'banco' => '',
            'ultimosdigitos' => '',

            //enganche inicial sera calculado
            //fin info de plan de ventas y pagos


            //fin de datos de la venta




            //datos del titular
            'titular' => 'required',
            'domicilio' => 'required',
            'ciudad' => 'required',
            'estado' => 'required',
            'celular' => 'required',
            'email' => 'nullable|email',
            'fecha_nac' => 'required|date',
            //fin de datos del titular
        ];

        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->venta_referencia_id == 1 && $request->ventaAntesdelSistema) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required|unique:ventas_propiedades,numero_titulo';
        }

        //validnado en caso de que sea de uso futuro
        if ($request->venta_referencia_id == 2) {
            //venta de uso inmediato
            $validaciones['num_solicitud'] = 'required|unique:ventas_propiedades,numero_solicitud';
            //valido si es de venta antes del sistema
            if ($request->ventaAntesdelSistema) {
                $validaciones['titulo'] = 'required|unique:ventas_propiedades,numero_titulo';
                $validaciones['convenio'] = 'required|unique:ventas_propiedades,numero_convenio';
            }
        }
        //validando si el tipo de pago requiere de banco y digitos
        if ($request->opcionPagar['value'] == 1) {
            //si desea pagar desde la venta
            if ($request->formaPago['value'] > 1) {
                //cuqlquiera menos efectivo
                $validaciones['banco'] = 'required';
            }
            if ($request->formaPago['value'] == 4 || $request->formaPago['value'] == 5) {
                //cuqlquiera menos efectivo
                $validaciones['ultimosdigitos'] = 'nullable|numeric|digits_between:4,4';
            }
        }



        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'required' => 'Ingrese este dato'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );




        return 1;
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

        //return count($request->all());
        //return ($request->all());
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
                    'digits_between:1,2',
                ],
            ],
            [
                '*.precio_neto.required' => 'ingrese este dato.',
                '*.precio_neto.numeric' => 'Ingrese una cantidad correcta.',
                '*.enganche_inicial.lte' => 'El pago inicial debe ser menor o igual al precio neto de la propiedad.',
                '*.enganche_inicial.required' => 'ingrese este dato.',
                '*.meses.numeric' => 'ingrese un número de meses correcto.',
                '*.meses.required' => 'ingrese este dato.',
                '*.meses.digits_between' => 'ingrese este dato (2 dígitos máximo).',
            ]
        );

        //actualizo los nuevos datos de tarifas

        try {
            DB::beginTransaction();
            //elimino todos los datos de esas tarifas
            DB::table('precios_propiedades')->where('tipo_propiedades_id', '=', $request[0]['tipo_propiedades_id'])->delete();

            for ($i = 0; $i < count($request->all()); $i++) {
                DB::table('precios_propiedades')->insert(
                    [
                        'precio_neto' => $request[$i]['precio_neto'],
                        'meses' => $request[$i]['meses'],
                        'enganche_inicial' => $request[$i]['enganche_inicial'],
                        'tipo_precios_id' => $request[$i]['tipo_precios_id'],
                        'tipo_propiedades_id' => $request[0]['tipo_propiedades_id'],
                        'fecha_hora' => now(),
                        'actualizo_id' => $request->user()->id
                    ]
                );
            }

            DB::commit();
            return 1;
        } catch (\Throwable $th) {
            DB::rollBack();
            return 0;
        }
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