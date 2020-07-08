<?php

namespace App\Http\Controllers;

use PDF;
use App\Clientes;
use App\VentasPlanes;
use App\PreciosPlanes;
use App\PlanesFunerarios;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\CementerioController;

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
            $datos_plan = $this->get_planes(false, $request->id_plan_modificar)[0];
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
    public function get_planes($solo_a_futuro = false, $id_plan = 0)
    {
        $solo_a_futuro = $solo_a_futuro == 'true' ? 1 : 0;

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
            $agregar = false;
            foreach ($plan['precios'] as $key_precio => &$precio) {
                if ($precio['financiamiento'] == 1) {
                    $precio['tipo_financiamiento'] = "Pago Único/Uso Inmediato";
                    $precio['tipo_financiamiento_ingles'] = "Spot Price";
                    $precio['pago_mensual'] = 0;
                } else {
                    $agregar = true;
                    $precio['tipo_financiamiento'] = "Pago a " . $precio['financiamiento'] . " Meses/A Futuro";
                    $precio['tipo_financiamiento_ingles'] = $precio['financiamiento'] . "-Month Payment";
                    $precio['pago_mensual'] = ($precio['costo_neto'] - $precio['pago_inicial']) / $precio['financiamiento'];
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

            if ($solo_a_futuro == 0) {
                $agregar = true;
            }

            $plan_funerario = [
                'id' => $plan['id'],
                'plan' => $plan['plan'],
                'plan_ingles' => $plan['plan_ingles'],
                'nota' => $plan['nota'],
                'nota_ingles' => $plan['nota_ingles'],
                'status' => $plan['status'],
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
            $plan_funerario['secciones'] = $secciones;


            if ($agregar == true) {
                array_push($data, $plan_funerario);
            }
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
                return $this->errorResponse('Ya existe un precio con este financiamiento', 409);
            } else {
                return $this->errorResponse('Ya existe un precio con este financiamiento, solo debe habilitarlo nuevamente', 409);
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
                if ($precio->status != 1) {
                    return $this->errorResponse('Este precio se encuentra desactivado y no puede modificarse.', 409);
                }
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




    public function pdf_plan_funerario(Request $request, $idioma = 'es')
    {
        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /* $id_plan = 1;
        $email = false;
        $email_to = 'hector@gmail.com';
*/
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_plan = $requestVentasList['id_plan'];


        //obtengo la informacion de esa venta
        $datos_plan = $this->get_planes(false, $id_plan)[0];
        if (empty($datos_plan)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
            return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('funeraria/plan_funerario/plan_funerario', ['datos' => $datos_plan, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = strtoupper($datos_plan['plan']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('funeraria.plan_funerario.footer'),
        ]);
        if ($datos_plan['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('funeraria.plan_funerario.header')
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 10.4);
        $pdf->setOption('margin-bottom', 25.4);
        $pdf->setOption('page-size', 'A4');
        if ($email == true) {
            /**email */
            /**
             * parameters lista de la funcion
             * to destinatario
             * to_name nombre del destinatario
             * subject motivo del correo
             * name_pdf nombre del pdf
             * pdf archivo pdf a enviar
             */
            /**quiere decir que el usuario desa mandar el archivo por correo y no consultarlo */
            $email_controller = new EmailController();
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                $request->destinatario,
                $datos_plan['plan'],
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }


    public function planes_funerarios(Request $request, $idioma = 'es')
    {
        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);
        /*
        $id_plan = 0;
        $email = false;
        $email_to = 'hector@gmail.com';
*/
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        //$id_plan = $requestVentasList['id_plan'];


        //obtengo la informacion de esa venta
        $planes = $this->get_planes(false, '');
        if (empty($planes)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
            return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('funeraria/planes_funerarios/planes_funerarios', ['datos' => $planes, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = 'PLANES FUNERARIOS' . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('funeraria.planes_funerarios.footer'),
        ]);
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 10.4);
        $pdf->setOption('margin-bottom', 25.4);
        $pdf->setOption('page-size', 'A4');
        if ($email == true) {
            /**email */
            /**
             * parameters lista de la funcion
             * to destinatario
             * to_name nombre del destinatario
             * subject motivo del correo
             * name_pdf nombre del pdf
             * pdf archivo pdf a enviar
             */
            /**quiere decir que el usuario desa mandar el archivo por correo y no consultarlo */
            $email_controller = new EmailController();
            $enviar_email = $email_controller->pdf_email(
                $email_to,
                $request->destinatario,
                'PLANES FUNERARIOS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }


    /**GUARDAR LA VENTA */
    public function control_ventas(Request $request, $tipo_servicio = '')
    {
        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }

        /**procede la peticion */

        /**aqui comienzan a gurdar los datos */
        $subtotal = $request->subtotal; //sin iva
        $iva = $request->impuestos; //solo el iva
        $descuento = $request->descuento;
        $costo_neto = $request->costo_neto;


        /**valdiando que cuadren las cantidades de la venta */
        //validaciones directas sin condicionales
        $validaciones = [
            //datos de la propiedad
            'id_venta' => '',
            /**solo para modificaciones */
            //datos de la venta
            'plan_funerario.value' => 'required',
            'plan_funerario.label' => 'required',
            /**plan en español */
            'plan_funerario.plan_ingles' => 'required',
            'plan_funerario.secciones.*.conceptos.*.seccion' => 'required',
            'plan_funerario.secciones.*.conceptos.*.concepto' => 'required',
            'plan_funerario.secciones.*.conceptos.*.concepto_ingles' => 'required',

            'ventaAntiguedad.value' => 'required',
            'id_cliente' => 'required',
            'vendedor.value' => 'required',
            'fecha_venta' => 'required|date',
            'tipo_financiamiento' => 'required',
            /**viene directo del frontend con el valor 2 que es solo a futuro */
            'solicitud' => '',
            'convenio' => '',
            'titulo' => '',
            /**titular_sustituto */
            'titular_sustituto' => 'required',
            'parentesco_titular_sustituto' => 'required',
            'telefono_titular_sustituto' => 'required',
            /**beneficiarios */
            'beneficiarios.*.nombre' => [
                'required',
            ],
            'beneficiarios.*.parentesco' => [
                'required',
            ],
            'beneficiarios.*.telefono' => [
                'required',
            ],
            //info del plan de venta y pagos
            'planVenta.value' => 'numeric|required',
            'subtotal' => 'numeric|required|min:1',
            'descuento' => 'required|numeric|min:0|max:' . $request->subtotal,
            'impuestos' => 'numeric|required|min:0',
            'costo_neto' => 'numeric|required|min:0',
            'costo_neto_pronto_pago' => 'required|min:1|lte:' . $costo_neto,
            'pago_inicial' => ''
        ];

        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_venta = array();
        if ($tipo_servicio == 'modificar') {
            $datos_venta = $this->get_ventas($request, $request->id_venta, '')[0];
            if (empty($datos_venta)) {
                /**no se encontro los datos */
                return $this->errorResponse('No se encontró la información de la venta solicitada', 409);
            } else if ($datos_venta['operacion_status'] == 0) {
                return $this->errorResponse('Esta venta ya fue cancelada, no puede modificarse', 409);
            } else {
                /**puede proceder con las valiaciones necesarias para */
            }
        }

        /**si pasan estas condicones podemos continuar */
        /**solo en caso de modificaciones */

        /**validando el pago inicial */
        if ($request->planVenta['value'] == 1) {
            /**cuando es a contado */
            /**es un solo pago de inicio */
            $validaciones['pago_inicial'] = 'numeric|required|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
        } else {
            /**cuando es a credito */
            if ($request->costo_neto > $request->planVenta['pago_inicial']) {
                /**minimo el pago inicial y maximo un 70% del costo neto */
                $validaciones['pago_inicial'] = 'numeric|required|min:' . $request->planVenta['pago_inicial'] . '|max:' . ($request->costo_neto) * .7;
            } else {
                /**si el descuento es menor al pago inicial se forza al usuario a ingresa como pago inicial minmo un 10% del totoa a pagar y un 70% de maximo 
                 * de pago inicial y el resto liquidarlo con los abonos
                 */
                $validaciones['pago_inicial'] = 'numeric|required|min:' . ($request->costo_neto * .1) . '|max:' . ($request->costo_neto * .7);
            }
        }

        /**VALIDACIONES CONDICIONADAS*/

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required';
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_titulo', $request->titulo)->where('operaciones.status', 1)->first();
            if (!empty($titulo)) {
                if ($tipo_servicio == 'modificar') {
                    if ($titulo->id != $request->id_venta)
                        return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
                } else {

                    return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
                }
            }
        }

        //validnado en caso de que sea de uso futuro
        if ($request->tipo_financiamiento == 2) {
            //venta de uso inmediato
            $validaciones['solicitud'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $solicitud = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_solicitud', trim($request->solicitud))->where('operaciones.status', 1)->first();
            if (!empty($solicitud)) {
                if ($tipo_servicio == 'modificar') {
                    if ($solicitud->id != $request->id_venta)
                        return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
                } else {
                    return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
                }
            }
        }


        //valido si es de venta antes del sistema
        if ($request->ventaAntiguedad['value'] == 2) {
            /**venta ya realizada anterior al sistema pero no liquidadada */
            $validaciones['convenio'] = 'required';
            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_convenio', trim($request->convenio))->where('operaciones.status', 1)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta)
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
        } else if ($request->ventaAntiguedad['value'] == 3) {
            /**venta ya liquidada antes del sistema */
            $validaciones['convenio'] = 'required';
            $validaciones['titulo'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_convenio', $request->convenio)->where('operaciones.status', 1)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta)
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_titulo', $request->titulo)->where('operaciones.status', 1)->first();
            if (!empty($titulo)) {
                if ($tipo_servicio == 'modificar') {
                    if ($titulo->id != $request->id_venta)
                        return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                } else {
                    return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                }
            }
        }



        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'id_venta.required' => 'Ingrese un la clave única de la venta para continuar',
            'max' => 'verifique la cantidad',
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'ubicacion.unique' => 'Este terreno ya fue vendido',
            'solicitud.unique' => 'Esta solicitud ya fue registrada en otra venta',
            'convenio.unique' => 'Este convenio ya fue registrado en otra venta',
            'titulo.unique' => 'Este título ya fue registrado en otra venta',
            'num_operacion.unique' => 'Este número de operación ya fue capturado',
            //beneficiarios
            '*.nombre.required' => 'ingrese este dato',
            '*.parentesco.required' => 'ingrese este dato',
            '*.telefono.required' => 'ingrese este dato',
            'lte' => 'verifique la cantidad',
            'unique.num_operacion' => 'Este número de operación ya fue registrado.',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );


        if ($tipo_servicio == 'agregar') {
            /**revisar si el cliente esta vigente para proceder con la venta */
            $cliente = Clientes::where('id', (int) $request->id_cliente)->first();
            if ($cliente->status != 1) {
                /**no esta activo y la venta no puede proceder */
                return $this->errorResponse('Este cliente no se encuentra activo en la base de datos.', 409);
            }
        }

        /**haciendo las validaciones necesarias para saber si se puede proceder con la modificacion de los datos */
        $reprogramar_pagos = false;
        if ($tipo_servicio == 'modificar') {
            /**tiene pagos vigentes, se debe validar lo que no puede modifcar. 
             * todo aquello que altere los precios y totales de la venta, pues al tener pagos realizados vigentes se alteria
             * el contrato y la programacion de pagos
             * solo puede modificar lo que no altere el contrato como cliente,vendedor, cliente
             * titular sustituto
             * beneficiarios
             */
            /**verificando que no hay modificado nada relativo a precios */

            if (
                $request->fecha_venta != $datos_venta['fecha_operacion'] ||
                (round($request->impuestos, 2, PHP_ROUND_HALF_UP) != round($datos_venta['impuestos'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->subtotal, 2, PHP_ROUND_HALF_UP) != round($datos_venta['subtotal'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->costo_neto, 2, PHP_ROUND_HALF_UP) != round($datos_venta['total'], 2, PHP_ROUND_HALF_UP) ||
                    $request->pago_inicial != count($datos_venta['pagos_programados']) > 0 ? round($datos_venta['pagos_programados'][0]['monto_programado'], 2, PHP_ROUND_HALF_UP) : 0 ||
                    round($request->descuento, 2, PHP_ROUND_HALF_UP) != round($datos_venta['descuento'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP) != round($datos_venta['costo_neto_pronto_pago'], 2, PHP_ROUND_HALF_UP))
            ) {

                if ($datos_venta['total'] > 0) {
                    /**si la venta no fue gratis */
                    if ($datos_venta['pagos_realizados'] > 0) {
                        return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, ubicacion, tipo de venta, tipo de 
                financiamiento, etc. Esto se debe a que existen pagos vigentes relacionados a esta venta y modificar cantidades o precios causaría que se perdiera la integridad de esta información.', 409);
                    } else {
                        $reprogramar_pagos = true;
                    }
                } else {
                    /**la venta no fue gratis */
                    return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, ubicacion, tipo de venta, tipo de 
                financiamiento, etc. Esto se debe a que la venta tiene un saldo de $0.00 pesos(está liquidada).', 409);
                }
            }
        }


        /**mando llamar las funciones que ya estan en el controlado de cementerio */
        $CementerioController = new CementerioController();


        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                //venta de uso inmediato y de control sistematizado
                //captura de la venta
                $id_venta = DB::table('ventas_planes')->insertGetId(
                    [
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'planes_funerarios_id' => $request->plan_funerario['value'],
                        'nombre_original' => $request->plan_funerario['label'],
                        'nombre_original_ingles' => $request->plan_funerario['plan_ingles']
                    ]
                );

                /**guardando los conceptos del plan */
                foreach ($request->plan_funerario['secciones'] as $key_seccion => $seccion) {
                    foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                        $seccion = 1;
                        if ($concepto['seccion'] == 'incluye') {
                            $seccion = 1;
                        } elseif ($concepto['seccion'] == 'inhumacion') {
                            $seccion = 2;
                        } elseif ($concepto['seccion'] == 'cremacion') {
                            $seccion = 3;
                        } elseif ($concepto['seccion'] == 'velacion') {
                            $seccion = 4;
                        } else {
                            /**error no existe el concepto */
                            return $this->errorResponse('Los conceptos no siguen el formato correcto.', 409);
                        }
                        DB::table('plan_conceptos_original')->insert(
                            [
                                'seccion_id' => $seccion,
                                'ventas_planes_id' => $id_venta,
                                'concepto' => $concepto['concepto'],
                                'concepto_ingles' => $concepto['concepto_ingles']
                            ]
                        );
                    }
                }


                /**a partir de la venta se crea la operaicon */
                $id_operacion = DB::table('operaciones')->insertGetId(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        'ventas_planes_id' => $id_venta,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? $request->solicitud : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' =>  $CementerioController->generarNumeroConvenio($request),
                        'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                        'empresa_operaciones_id' => 4, //venta de planes a futuro
                        'subtotal' => $subtotal,
                        'descuento' => $descuento,
                        'impuestos' => $iva,
                        'total' => $costo_neto,
                        'descuento_pronto_pago_b' => $request->planVenta['descuento_pronto_pago_b'],
                        'costo_neto_pronto_pago' => round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP),
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->planVenta['value'],
                        'aplica_devolucion_b' => 0,
                        'costo_neto_financiamiento_normal' => round($request->planVenta['costo_neto_financiamiento_normal'], 2, PHP_ROUND_HALF_UP),
                        'comision_venta_neto' => 0,
                        'fecha_registro' => now(),
                        'fecha_operacion' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'registro_id' => (int) $request->user()->id,
                        'nota' => $request->nota,
                        'status' => $costo_neto > 0 ? 1 : '2',
                    ]
                );
                /**guardando los datos de la tasa para intereses */
                $CementerioController->guardarAjustesPoliticas($request, $id_operacion);
                /**programacion de pagos */
                if ($costo_neto > 0) {
                    /**si la cantidad que resta a pagar es mayor a cero se manda llamar la programcion de pagos */
                    $CementerioController->programarPagos($request, $id_operacion, $id_venta, '004');
                } else {
                    /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                    $CementerioController->generarNumeroTitulo($id_operacion, true);
                }
                //captura de los beneficiarios
                $CementerioController->guardarBeneficiarios($request, $id_operacion);
                /**guardar venta parte final */
                /**captura de pagos */
                /**fin de captura de pagos */
            }
            /**fin if servicio tipo agregar */
            else {
                /**es modificar */
                DB::table('ventas_planes')->where('id', '=', $request->id_venta)->update(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'tipo_financiamiento' => $request->tipo_financiamiento,
                        'salarios_minimos' => $request->salarios_minimos
                    ]
                );

                DB::table('operaciones')->where('id', '=', $datos_venta['operacion_id'])->update(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? trim($request->solicitud) : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' => trim($request->convenio),
                        'numero_titulo' => trim($request->titulo),
                        'subtotal' => $subtotal,
                        'descuento' => $descuento,
                        'impuestos' => $iva,
                        'total' => $costo_neto,
                        'descuento_pronto_pago_b' => $request->planVenta['descuento_pronto_pago_b'],
                        'costo_neto_pronto_pago' => round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP),
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->planVenta['value'],
                        'costo_neto_financiamiento_normal' => $request->planVenta['costo_neto_financiamiento_normal'],
                        'status' => ($costo_neto > 0 && $datos_venta['saldo_neto'] > 0) ? '1' : '2',
                        'fecha_modificacion' => now(),
                        'fecha_operacion' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'modifico_id' => (int) $request->user()->id,
                        'nota' => $request->nota,
                    ]
                );

                /**verificamos si tiene pagos realizados para saber si podemos eliminar los pagos existentes o si solo cancelarlos */
                if ($reprogramar_pagos == true) {
                    if ($datos_venta['pagos_realizados'] > 0) {
                        /**cancelamos los pagos programados vigentes */
                        DB::table('pagos_programados')->where('operaciones_id', '=', $datos_venta['operacion_id'])->update(
                            [
                                'status' => 0,
                            ]
                        );
                    } else {
                        /**podemos eliminar los pagos programados viegente para rehacerlo */
                        DB::table('pagos_programados')->where('operaciones_id', '=', $datos_venta['operacion_id'])->delete();
                    }
                    /**programacion de pagos */
                    if ($costo_neto > 0) {
                        /**si la cantidad que resta a pagar es mayor a cero se manda llamar la programcion de pagos */
                        $this->programarPagos($request, $datos_venta['operacion_id'], $request->id_venta);
                    } else {
                        /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                        if (trim($datos_venta['numero_titulo']) == '') {
                            $this->generarNumeroTitulo($datos_venta['operacion_id'], true);
                        }
                    }
                }
                //captura de los beneficiarios
                $this->guardarBeneficiarios($request, $datos_venta['operacion_id']);
                /**pendiente hacer modificacion de progrmacion de pagos */
            } //fin else de modificar venta de propiedad

            DB::commit();
            return
                $tipo_servicio == 'agregar' ? $id_venta : $request->id_venta;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        /**FIN DE VENTA A USO INMEDIATO */
        /**********FIN DE CASOS DE VENTA 1 - NUEVA VENTA "SISTEMATIZADA" */
    }
}