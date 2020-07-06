<?php

namespace App\Http\Controllers;

use App\PlanesFunerarios;
use App\PreciosPlanes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class FunerariaController extends ApiController
{

    /**REGISTRAR PLAN FUNERARIO*/
    public function control_planes(Request $request, $tipo_servicio = '')
    {

        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }

        //validaciones directas sin condicionales
        $validaciones = [
            'descripcion' => 'required',
            'descripcion_ingles' => 'required',
            'conceptos.0.conceptos' => [
                'required'
            ],
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'descripcion.required' => 'Ingrese el nombre del plan funerario.',
            'descripcion_ingles.required' => 'Ingrese el nombre del plan funerario(en inglés).',
            'conceptos.0.conceptos.required' => 'Debe ingresar al menos 1 Artículo/Servicio que aplique en la sección "Plan Funerario".',
        ];


        request()->validate(
            $validaciones,
            $mensajes
        );

        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_plan = array();
        if ($tipo_servicio == 'modificar') {
            $datos_plan = $this->get_planes($request->id_plan_modificar, '')[0];
            if (empty($datos_plan)) {
                /**no se encontro los datos */
                return $this->errorResponse('No se encontró la información del plan solicitada', 409);
            } else if ($datos_plan['status'] == 0) {
                return $this->errorResponse('Esta plan ya fue cancelado, no puede modificarse', 409);
            }
        }
        $id_return = 0;
        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                $id_plan = DB::table('planes_funerarios')->insertGetId(
                    [
                        'plan' => $request->descripcion,
                        'plan_ingles' => $request->descripcion_ingles,
                        'nota' => $request->nota,
                        'nota_ingles' => $request->nota_ingles,
                        'registro_id' => (int) $request->user()->id,
                        'modifico_id' => (int) $request->user()->id,
                        'fecha_registro' => now(),
                        'fecha_modificacion' => now()
                    ]
                );
                /**al registrar el plan, se procede a registrar los conceptos */
                foreach ($request->conceptos as $key_seccion => $seccion) {
                    foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                        DB::table('plan_conceptos')->insert(
                            [
                                'seccion_id' => ($key_seccion + 1),
                                'concepto' => $concepto['concepto'],
                                'concepto_ingles' => $concepto['concepto_ingles'],
                                'planes_funerarios_id' => $id_plan
                            ]
                        );
                    }
                }
                $id_return = $id_plan;
                /**todo salio bien y se debe de guardar */
            } else {
                /**es modificar */
                DB::table('planes_funerarios')->where('id', $request->id_plan_modificar)->update(
                    [
                        'plan' => $request->descripcion,
                        'plan_ingles' => $request->descripcion_ingles,
                        'nota' => $request->nota,
                        'nota_ingles' => $request->nota_ingles,
                        'modifico_id' => (int) $request->user()->id,
                        'fecha_modificacion' => now()
                    ]
                );
                /**eliminamos los coceptos originales */
                DB::table('plan_conceptos')->where('planes_funerarios_id', $request->id_plan_modificar)->delete();

                /**al actualizzar el plan, se procede a registrar los conceptos nuevamente*/
                foreach ($request->conceptos as $key_seccion => $seccion) {
                    foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                        DB::table('plan_conceptos')->insert(
                            [
                                'seccion_id' => ($key_seccion + 1),
                                'concepto' => $concepto['concepto'],
                                'concepto_ingles' => $concepto['concepto_ingles'],
                                'planes_funerarios_id' => $request->id_plan_modificar
                            ]
                        );
                    }
                }
                $id_return = $request->id_plan_modificar;
            }
            DB::commit();
            return $id_return;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**ENABLE DISABLE PLANES FUENRARIOS*/
    public function enable_disable_planes(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'id_plan' => 'required',
        ];


        $mensajes = [
            'required' => 'Dese ingresar la clave del plan funerario',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );


        /**validar si el precio existe */
        $plan = PlanesFunerarios::where('id', $request->id_plan)
            ->get()
            ->first();

        //definiendo status
        $status = 0;
        if (!empty($plan)) {
            $status = !$plan->status;
        } else {
            return $this->errorResponse('No se encontró este plan funerario en la base de datos', 409);
        }

        try {
            DB::beginTransaction();
            $res = DB::table('planes_funerarios')->where('id', $request->id_plan)->update(
                [

                    'status' =>  $status
                ]
            );
            /**todo salio bien y se debe de modificar */
            DB::commit();
            return $request->id_plan;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }



    /**obtiene todos los planes registrados */
    public function get_planes($id_plan = '')
    {
        $resultado = PlanesFunerarios::with('conceptos')->with('precios')->orderBy('planes_funerarios.id', 'desc')
            ->where(function ($q) use ($id_plan) {
                if (trim($id_plan) != '' && $id_plan > 0) {
                    $q->where('planes_funerarios.id', '=', $id_plan);
                }
            })
            ->get()->toArray();
        /**formateando el resultado */
        $data = array();
        foreach ($resultado as $key_plan => &$plan) {
            /**actualiznado precios textos */
            foreach ($plan['precios'] as $key_precio => &$precio) {
                if ($precio['financiamiento'] == 1) {
                    $precio['tipo_financiamiento'] = "Pago Único/Uso Inmediato";
                    $precio['tipo_financiamiento_ingles'] = "Spot Price";
                    $precio['pago_mensual']
                        = 0;
                } else {
                    $precio['tipo_financiamiento'] = "Pago a " . $precio['financiamiento'] . " Meses/A Futuro";
                    $precio['tipo_financiamiento_ingles'] = $precio['financiamiento'] . "-Month Payment";
                    $precio['pago_mensual']
                        = ($precio['costo_neto'] - $precio['pago_inicial']) / $precio['financiamiento'];
                }
                /**sacando los descuentos en caso de que tenga pronto pago */
                if ($precio['descuento_pronto_pago_b'] == 1) {
                    $precio['descuento_x_pago'] = round(($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'], 2);
                    $precio['porcentaje_pronto_pago'] = round(100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']), 2);
                } else {
                    $precio['descuento_x_pago'] = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }
            $plan_funerario = [
                'id' => $plan['id'],
                'plan' => $plan['plan'],
                'plan_ingles' => $plan['plan_ingles'],
                'nota' => $plan['nota'],
                'nota_ingles' => $plan['nota_ingles'],
                'status' => $plan['status'],
                'secciones' => [],
                'precios' => $plan['precios']
            ];
            $secciones = array();
            $secciones = [
                [
                    'seccion' => 'incluye',
                    'seccion_ingles' => 'include',
                    'conceptos' => []
                ],
                [
                    'seccion' => 'inhumacion',
                    'seccion_ingles' => 'inhumation',
                    'conceptos' => []
                ],
                [
                    'seccion' => 'cremacion',
                    'seccion_ingles' => 'cremation',
                    'conceptos' => []
                ],
                [
                    'seccion' => 'velacion',
                    'seccion_ingles' => 'wakefulness',
                    'conceptos' => []
                ]
            ];
            foreach ($plan['conceptos'] as $key_seccion => $seccion) {
                /**agregando los conceptos segun su seccion */
                if ($seccion['seccion_id'] == 1) {
                    /**incluye */
                    array_push(
                        $secciones[0]['conceptos'],
                        [
                            'concepto' => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en' => 'plan funerario',
                            'seccion' => 'incluye'
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 2) {
                    /**inhumacion */
                    array_push(
                        $secciones[1]['conceptos'],
                        [
                            'concepto' => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en' => 'caso de inhumación',
                            'seccion' => 'inhumacion'
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 3) {
                    /**cremacion */
                    array_push(
                        $secciones[2]['conceptos'],
                        [
                            'concepto' => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en' => 'caso de cremación',
                            'seccion' => 'cremacion'
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 4) {
                    /**velacion */
                    array_push(
                        $secciones[3]['conceptos'],
                        [
                            'concepto' => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en' => 'caso de velación',
                            'seccion' => 'velacion'
                        ]
                    );
                }
            }
            /**push al array padre */
            array_push($plan_funerario['secciones'], $secciones);
            array_push($data, $plan_funerario);
        }
        return $data;
    }



    /**GUARDAR PRECIO*/
    public function registrar_precio(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'descripcion' => 'required',
            'descripcion_ingles' => 'required',
            'contado_b.value' => 'required|integer|min:0|max:1',
            'financiamiento' => '',
            'pago_inicial' => '',
            'costo_neto' => 'required|numeric|min:1|gte:costo_neto_financiamiento_normal',
            'costo_neto_financiamiento_normal' => 'required|numeric|lte:costo_neto',
            'descuento_pronto_pago_b.value' => 'required|min:0|max:1|numeric',
            'costo_neto_pronto_pago' => '',
            'tipo_plan.value' => 'required',
        ];


        $mensaje_financiamiento = '';
        $mensaje_pago_inicial = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial = 'Este valor debe ser igual al costo neto, debido a que es precio de contado';
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:64';
            $mensaje_financiamiento = ' Este dato debe ser "2" Mínimo y "64" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|lte:costo_neto';
            $mensaje_pago_inicial = 'Este valor debe ser "1.00" Mínimo';
        }


        /**validando si aplica descuento */
        if ($request->descuento_pronto_pago_b['value'] == 1) {
            /**si aplica descuento se debe validar que el precio no pase del precio de contado actual */
            $validaciones['costo_neto_pronto_pago'] = 'required|numeric|gte:costo_neto_financiamiento_normal|lt:costo_neto';
        } else {
            /**no aplica descuento */
            $validaciones['costo_neto_pronto_pago'] = '';
            /**se manda cero a la query */
            $request->costo_neto_pronto_pago = $request->costo_neto;
        }


        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'pago_inicial.min' => $mensaje_pago_inicial,
            'pago_inicial.lte' => 'Este valor debe ser "1" mínimo, o igual o menor al costo neto',
            'pago_inicial.max' => 'Este valor debe ser igual al costo neto, debido a que es precio de contado',
            'financiamiento.min' =>  $mensaje_financiamiento,
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'costo_neto_financiamiento_normal.lte' => 'Esta cantidad debe menor o igual al costo neto',
            'costo_neto.min' => 'Esta cantidad debe mayor a cero',
            'costo_neto.gte' => 'Esta cantidad debe mayor o igual al costo neto de contado',
            'costo_neto_pronto_pago.gte' => 'Esta cantidad debe ser mayor o igual al costo neto a precio de contado',
            'costo_neto_pronto_pago.lt' => 'Este valor debe ser menor al costo neto'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );



        /**validar si el precio existe */
        $precio = PreciosPlanes::where('planes_funerarios_id', $request->tipo_plan['value'])
            ->where('financiamiento', $request->financiamiento)
            ->get()
            ->first();

        if (!empty($precio)) {
            /**ya existe el precio */
            if ($precio->status == 1) {
                return $this->errorResponse('Ya existe un precio para con este financiamiento', 409);
            } else {
                return $this->errorResponse('Ya existe un precio para con este financiamiento, solo debe habilitarlo nuevamente', 409);
            }
        }

        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $id_precio = 0;
            $id_precio = DB::table('precios_planes')->insertGetId(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' => $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' => $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b' => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago' => (float) ($request->costo_neto_pronto_pago),
                    'planes_funerarios_id' => (int) ($request->tipo_plan['value']),
                    'fecha_registro' => now(),
                    'fecha_actualizacion' => now(),
                    'actualizo_id' => (int) $request->user()->id,
                    'financiamiento' => (int) ($request->financiamiento),
                    'contado_b' => (int) ($request->contado_b['value']),
                    'descripcion' =>  $request->descripcion,
                    'descripcion_ingles' =>  $request->descripcion_ingles
                ]
            );

            /**todo salio bien y se debe de guardar */
            DB::commit();
            return $id_precio;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**obtiene un precio por id */
    public function get_precio_by_id(Request $request)
    {
        if (!$request->id_precio) {
            return $this->errorResponse('Es necesario un id, para continuar', 409);
        }
        $resultado = PreciosPlanes::where('id', $request->id_precio)->get()->first();
        return $resultado;
    }



    /**MODIFICAR PRECIO*/
    public function update_precio(Request $request)
    {

        //validaciones directas sin condicionales
        $validaciones = [
            'id_precio_modificar' => 'required',
            'descripcion' => 'required',
            'descripcion_ingles' => 'required',
            'contado_b.value' => 'required|integer|min:0|max:1',
            'financiamiento' => '',
            'pago_inicial' => '',
            'costo_neto' => 'required|numeric|min:0|gte:costo_neto_financiamiento_normal',
            'costo_neto_financiamiento_normal' => 'required|numeric|lte:costo_neto',
            'descuento_pronto_pago_b.value' => 'required|min:0|max:1|numeric',
            'costo_neto_pronto_pago' => '',
            'tipo_plan.value' => 'required',
        ];


        $mensaje_financiamiento = '';
        $mensaje_pago_inicial = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial = 'Este valor debe ser igual al costo neto, debido a que es precio de contado';
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:64';
            $mensaje_financiamiento = ' Este dato debe ser "2" Mínimo y "64" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|lte:costo_neto';
            $mensaje_pago_inicial = 'Este valor debe ser "1.00" Mínimo';
        }


        /**validando si aplica descuento */
        if ($request->descuento_pronto_pago_b['value'] == 1) {
            /**si aplica descuento se debe validar que el precio no pase del precio de contado actual */
            $validaciones['costo_neto_pronto_pago'] = 'required|numeric|gte:costo_neto_financiamiento_normal|lte:costo_neto';
        } else {
            /**no aplica descuento */
            $validaciones['costo_neto_pronto_pago'] = '';
            /**se manda cero a la query */
            $request->costo_neto_pronto_pago = $request->costo_neto;
        }


        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'pago_inicial.min' => $mensaje_pago_inicial,
            'pago_inicial.lte' => 'Este valor debe ser "1" mínimo, o igual o menor al costo neto',
            'pago_inicial.max' => 'Este valor debe ser igual al costo neto, debido a que es precio de contado',
            'financiamiento.min' =>  $mensaje_financiamiento,
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'costo_neto_financiamiento_normal.lte' => 'Esta cantidad debe menor o igual al costo neto',
            'costo_neto.gte' => 'Esta cantidad debe mayor o igual al costo neto de contado',
            'costo_neto.min' => 'Esta cantidad debe mayor a cero',
            'costo_neto_pronto_pago.gte' => 'Esta cantidad debe ser mayor o igual al costo neto a precio de contado',
            'costo_neto_pronto_pago.lt' => 'Este valor debe ser menor al costo neto'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );



        /**validar si el precio existe */
        $precio = PreciosPlanes::where('planes_funerarios_id', $request->tipo_plan['value'])
            ->where('financiamiento', $request->financiamiento)
            ->get()
            ->first();
        if (!empty($precio)) {
            /**verificando que el precio no es el mismo para que entre ala siguiente validacion */
            if ($request->id_precio_modificar != $precio->id) {
                /**ya existe el precio */
                if ($precio->status == 1) {
                    return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento', 409);
                } else {
                    return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento, solo debe habilitarlo nuevamente', 409);
                }
            } else {
                /**checando si el mismo esta desactivado */
                return $this->errorResponse('Este precio se encuentra desactivado y no puede modificarse', 409);
            }
        }



        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $res = DB::table('precios_planes')->where('id', $request->id_precio_modificar)->update(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' =>  $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' =>  $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b' => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago' => (float) ($request->costo_neto_pronto_pago),
                    'planes_funerarios_id' => (int) ($request->tipo_plan['value']),
                    'fecha_actualizacion' => now(),
                    'actualizo_id' => (int) $request->user()->id,
                    'financiamiento' => (int) ($request->financiamiento),
                    'contado_b' => (int) ($request->contado_b['value']),
                    'descripcion' =>  $request->descripcion,
                    'descripcion_ingles' =>  $request->descripcion_ingles
                ]
            );
            /**todo salio bien y se debe de guardar */
            DB::commit();
            return  $res > 0 ? $request->id_precio_modificar : 0;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**ENABLE DISABLE PRECIO*/
    public function enable_disable(Request $request)
    {
        //validaciones directas sin condicionales
        $validaciones = [
            'id_precio' => 'required',
        ];
        $mensajes = [
            'required' => 'Dese ingresar la clave del precio',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        /**validar si el precio existe */
        $precio = PreciosPlanes::where('id', $request->id_precio)
            ->get()
            ->first();
        //definiendo status
        $status = 0;
        if (!empty($precio)) {
            $status = !$precio->status;
        } else {
            return $this->errorResponse('No se encontró este precio en la base de datos', 409);
        }
        try {
            DB::beginTransaction();
            $res = DB::table('precios_planes')->where('id', $request->id_precio)->update(
                [

                    'status' =>  $status
                ]
            );
            /**todo salio bien y se debe de modificar */
            DB::commit();
            return $request->id_precio;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}