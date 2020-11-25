<?php

namespace App\Http\Controllers;

use App\Afiliaciones;
use App\Articulos;
use App\Categorias;
use App\Clientes;
use App\Escolaridades;
use App\EstadosAfectado;
use App\EstadosCiviles;
use App\Http\Controllers\CementerioController;
use App\LugaresInhumacion;
use App\LugaresServicio;
use App\Operaciones;
use App\PlanConceptosServicioOriginal;
use App\PlanesFunerarios;
use App\PreciosPlanes;
use App\RegistroPublico;
use App\ServiciosFunerarios;
use App\SitiosMuerte;
use App\TiposContratante;
use App\Titulos;
use App\User;
use App\VentasPlanes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PDF;

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
            'descripcion'           => 'required',
            'descripcion_ingles'    => 'required',
            'conceptos.0.conceptos' => [
                'required',
            ],
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'descripcion.required'           => 'Ingrese el nombre del plan funerario.',
            'descripcion_ingles.required'    => 'Ingrese el nombre del plan funerario(en inglés).',
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
                        'plan'               => $request->descripcion,
                        'plan_ingles'        => $request->descripcion_ingles,
                        'nota'               => $request->nota != '' ? $request->nota : '',
                        'nota_ingles'        => $request->nota_ingles != '' ? $request->nota_ingles : '',
                        'registro_id'        => (int) $request->user()->id,
                        'modifico_id'        => (int) $request->user()->id,
                        'fecha_registro'     => now(),
                        'fecha_modificacion' => now(),
                    ]
                );
                /**al registrar el plan, se procede a registrar los conceptos */
                foreach ($request->conceptos as $key_seccion => $seccion) {
                    foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                        DB::table('plan_conceptos')->insert(
                            [
                                'seccion_id'           => ($key_seccion + 1),
                                'concepto'             => $concepto['concepto'],
                                'concepto_ingles'      => $concepto['concepto_ingles'],
                                'planes_funerarios_id' => $id_plan,
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
                        'plan'               => $request->descripcion,
                        'plan_ingles'        => $request->descripcion_ingles,
                        'nota'               => $request->nota != '' ? $request->nota : '',
                        'nota_ingles'        => $request->nota_ingles != '' ? $request->nota_ingles : '',
                        'modifico_id'        => (int) $request->user()->id,
                        'fecha_modificacion' => now(),
                    ]
                );
                /**eliminamos los coceptos originales */
                DB::table('plan_conceptos')->where('planes_funerarios_id', $request->id_plan_modificar)->delete();

                /**al actualizzar el plan, se procede a registrar los conceptos nuevamente*/
                foreach ($request->conceptos as $key_seccion => $seccion) {
                    foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                        DB::table('plan_conceptos')->insert(
                            [
                                'seccion_id'           => ($key_seccion + 1),
                                'concepto'             => $concepto['concepto'],
                                'concepto_ingles'      => $concepto['concepto_ingles'],
                                'planes_funerarios_id' => $request->id_plan_modificar,
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

                    'status' => $status,
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
                    $precio['tipo_financiamiento']        = "Pago Único/Uso Inmediato";
                    $precio['tipo_financiamiento_ingles'] = "Spot Price";
                    $precio['pago_mensual']               = 0;
                } else {
                    $agregar                              = true;
                    $precio['tipo_financiamiento']        = "Pago a " . $precio['financiamiento'] . " Meses/A Futuro";
                    $precio['tipo_financiamiento_ingles'] = $precio['financiamiento'] . "-Month Payment";
                    $precio['pago_mensual']               = ($precio['costo_neto'] - $precio['pago_inicial']) / $precio['financiamiento'];
                }
                /**sacando los descuentos en caso de que tenga pronto pago */
                if ($precio['descuento_pronto_pago_b'] == 1) {
                    $precio['descuento_x_pago']       = round(($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'], 2);
                    $precio['porcentaje_pronto_pago'] = round(100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']), 2);
                } else {
                    $precio['descuento_x_pago']       = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }

            if ($solo_a_futuro == 0) {
                $agregar = true;
            }

            $plan_funerario = [
                'id'          => $plan['id'],
                'plan'        => $plan['plan'],
                'plan_ingles' => $plan['plan_ingles'],
                'nota'        => $plan['nota'],
                'nota_ingles' => $plan['nota_ingles'],
                'status'      => $plan['status'],
                'precios'     => $plan['precios'],
            ];
            $secciones = array();
            $secciones = [
                [
                    'seccion'        => 'incluye',
                    'seccion_ingles' => 'include',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'inhumacion',
                    'seccion_ingles' => 'inhumation',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'cremacion',
                    'seccion_ingles' => 'cremation',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'velacion',
                    'seccion_ingles' => 'wakefulness',
                    'conceptos'      => [],
                ],
            ];
            foreach ($plan['conceptos'] as $key_seccion => $seccion) {
                /**agregando los conceptos segun su seccion */
                if ($seccion['seccion_id'] == 1) {
                    /**incluye */
                    array_push(
                        $secciones[0]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'plan funerario',
                            'seccion'         => 'incluye',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 2) {
                    /**inhumacion */
                    array_push(
                        $secciones[1]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de inhumación',
                            'seccion'         => 'inhumacion',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 3) {
                    /**cremacion */
                    array_push(
                        $secciones[2]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de cremación',
                            'seccion'         => 'cremacion',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 4) {
                    /**velacion */
                    array_push(
                        $secciones[3]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de velación',
                            'seccion'         => 'velacion',
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
            'descripcion'                      => 'required',
            'descripcion_ingles'               => 'required',
            'contado_b.value'                  => 'required|integer|min:0|max:1',
            'financiamiento'                   => '',
            'pago_inicial'                     => '',
            'costo_neto'                       => 'required|numeric|min:1|gte:costo_neto_financiamiento_normal',
            'costo_neto_financiamiento_normal' => 'required|numeric|lte:costo_neto',
            'descuento_pronto_pago_b.value'    => 'required|min:0|max:1|numeric',
            'costo_neto_pronto_pago'           => '',
            'tipo_plan.value'                  => 'required',
        ];

        $mensaje_financiamiento = '';
        $mensaje_pago_inicial   = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento         = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial         = 'Este valor debe ser igual al costo neto, debido a que es precio de contado';
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:64';
            $mensaje_financiamiento         = ' Este dato debe ser "2" Mínimo y "64" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|lte:costo_neto';
            $mensaje_pago_inicial         = 'Este valor debe ser "1.00" Mínimo';
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
            'pago_inicial.min'                     => $mensaje_pago_inicial,
            'pago_inicial.lte'                     => 'Este valor debe ser "1" mínimo, o igual o menor al costo neto',
            'pago_inicial.max'                     => 'Este valor debe ser igual al costo neto, debido a que es precio de contado',
            'financiamiento.min'                   => $mensaje_financiamiento,
            'required'                             => 'Ingrese este dato',
            'numeric'                              => 'Este dato debe ser un número',
            'costo_neto_financiamiento_normal.lte' => 'Esta cantidad debe menor o igual al costo neto',
            'costo_neto.min'                       => 'Esta cantidad debe mayor a cero',
            'costo_neto.gte'                       => 'Esta cantidad debe mayor o igual al costo neto de contado',
            'costo_neto_pronto_pago.gte'           => 'Esta cantidad debe ser mayor o igual al costo neto a precio de contado',
            'costo_neto_pronto_pago.lt'            => 'Este valor debe ser menor al costo neto',
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
            $subtotal   = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva        = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $id_precio = 0;
            $id_precio = DB::table('precios_planes')->insertGetId(
                [
                    'pago_inicial'                     => (float) $request->pago_inicial,
                    'subtotal'                         => $subtotal,
                    'impuestos'                        => $iva,
                    'costo_neto'                       => $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b'          => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago'           => (float) ($request->costo_neto_pronto_pago),
                    'planes_funerarios_id'             => (int) ($request->tipo_plan['value']),
                    'fecha_registro'                   => now(),
                    'fecha_actualizacion'              => now(),
                    'actualizo_id'                     => (int) $request->user()->id,
                    'financiamiento'                   => (int) ($request->financiamiento),
                    'contado_b'                        => (int) ($request->contado_b['value']),
                    'descripcion'                      => $request->descripcion,
                    'descripcion_ingles'               => $request->descripcion_ingles,
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
            'id_precio_modificar'              => 'required',
            'descripcion'                      => 'required',
            'descripcion_ingles'               => 'required',
            'contado_b.value'                  => 'required|integer|min:0|max:1',
            'financiamiento'                   => '',
            'pago_inicial'                     => '',
            'costo_neto'                       => 'required|numeric|min:0|gte:costo_neto_financiamiento_normal',
            'costo_neto_financiamiento_normal' => 'required|numeric|lte:costo_neto',
            'descuento_pronto_pago_b.value'    => 'required|min:0|max:1|numeric',
            'costo_neto_pronto_pago'           => '',
            'tipo_plan.value'                  => 'required',
        ];

        $mensaje_financiamiento = '';
        $mensaje_pago_inicial   = '';
        /**validando si es a contado o credito */
        if ($request->contado_b['value'] == 1) {
            //es a contado
            $validaciones['financiamiento'] = 'required|integer|max:1';
            $mensaje_financiamiento         = ' Este dato debe ser "1" Máximo';

            /**forzando en pago inicial a ser igual al costo neto de la propiedad */
            $validaciones['pago_inicial'] = 'required|numeric|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
            $mensaje_pago_inicial         = 'Este valor debe ser igual al costo neto, debido a que es precio de contado';
        } else {
            /**es a credito */
            $validaciones['financiamiento'] = 'required|integer|min:2|max:64';
            $mensaje_financiamiento         = ' Este dato debe ser "2" Mínimo y "64" Máximo';

            /**se puede mantener el pago inicial por debajo del costo neto */
            $validaciones['pago_inicial'] = 'required|numeric|min:1|lte:costo_neto';
            $mensaje_pago_inicial         = 'Este valor debe ser "1.00" Mínimo';
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
            'pago_inicial.min'                     => $mensaje_pago_inicial,
            'pago_inicial.lte'                     => 'Este valor debe ser "1" mínimo, o igual o menor al costo neto',
            'pago_inicial.max'                     => 'Este valor debe ser igual al costo neto, debido a que es precio de contado',
            'financiamiento.min'                   => $mensaje_financiamiento,
            'required'                             => 'Ingrese este dato',
            'numeric'                              => 'Este dato debe ser un número',
            'costo_neto_financiamiento_normal.lte' => 'Esta cantidad debe menor o igual al costo neto',
            'costo_neto.gte'                       => 'Esta cantidad debe mayor o igual al costo neto de contado',
            'costo_neto.min'                       => 'Esta cantidad debe mayor a cero',
            'costo_neto_pronto_pago.gte'           => 'Esta cantidad debe ser mayor o igual al costo neto a precio de contado',
            'costo_neto_pronto_pago.lt'            => 'Este valor debe ser menor al costo neto',
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
            $subtotal   = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva        = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $res = DB::table('precios_planes')->where('id', $request->id_precio_modificar)->update(
                [
                    'pago_inicial'                     => (float) $request->pago_inicial,
                    'subtotal'                         => $subtotal,
                    'impuestos'                        => $iva,
                    'costo_neto'                       => $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b'          => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago'           => (float) ($request->costo_neto_pronto_pago),
                    'planes_funerarios_id'             => (int) ($request->tipo_plan['value']),
                    'fecha_actualizacion'              => now(),
                    'actualizo_id'                     => (int) $request->user()->id,
                    'financiamiento'                   => (int) ($request->financiamiento),
                    'contado_b'                        => (int) ($request->contado_b['value']),
                    'descripcion'                      => $request->descripcion,
                    'descripcion_ingles'               => $request->descripcion_ingles,
                ]
            );
            /**todo salio bien y se debe de guardar */
            DB::commit();
            return $res > 0 ? $request->id_precio_modificar : 0;
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

                    'status' => $status,
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
        $email             = $request->email_send === 'true' ? true : false;
        $email_to          = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_plan           = $requestVentasList['id_plan'];

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
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView('funeraria/plan_funerario/plan_funerario', ['datos' => $datos_plan, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = strtoupper($datos_plan['plan']) . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('funeraria.plan_funerario.footer'),
        ]);
        if ($datos_plan['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('funeraria.plan_funerario.header'),
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 10.4);
        $pdf->setOption('margin-bottom', 25.4);
        $pdf->setOption('page-size', 'letter');
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
            $enviar_email     = $email_controller->pdf_email(
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
        $email             = $request->email_send === 'true' ? true : false;
        $email_to          = $request->email_address;
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
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView('funeraria/planes_funerarios/planes_funerarios', ['datos' => $planes, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = 'PLANES FUNERARIOS' . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('funeraria.planes_funerarios.footer'),
        ]);
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 20.4);
        $pdf->setOption('margin-right', 20.4);
        $pdf->setOption('margin-top', 10.4);
        $pdf->setOption('margin-bottom', 25.4);
        $pdf->setOption('page-size', 'letter');
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
            $enviar_email     = $email_controller->pdf_email(
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
        $subtotal   = $request->subtotal; //sin iva
        $iva        = $request->impuestos; //solo el iva
        $tasa_iva   = $request->tasa_iva; //sin iva
        $descuento  = $request->descuento;
        $costo_neto = $request->costo_neto;

        /**valdiando que cuadren las cantidades de la venta */
        //validaciones directas sin condicionales
        $validaciones = [
            //datos de la propiedad
            'id_venta'                                               => '',
            /**solo para modificaciones */
            //datos de la venta
            'plan_funerario.value'                                   => 'required',
            'plan_funerario.plan'                                    => 'required',
            /**plan en español */
            'plan_funerario.plan_ingles'                             => 'required',
            'plan_funerario.secciones.*.conceptos.*.seccion'         => 'required',
            'plan_funerario.secciones.*.conceptos.*.concepto'        => 'required',
            'plan_funerario.secciones.*.conceptos.*.concepto_ingles' => 'required',

            'ventaAntiguedad.value'                                  => 'required',
            'id_cliente'                                             => 'required',
            'vendedor.value'                                         => 'required',
            'fecha_venta'                                            => 'required|date',
            'tipo_financiamiento'                                    => 'required',
            /**viene directo del frontend con el valor 2 que es solo a futuro */
            'solicitud'                                              => '',
            'convenio'                                               => '',
            'titulo'                                                 => '',
            /**titular_sustituto */
            'titular_sustituto'                                      => 'required',
            'parentesco_titular_sustituto'                           => 'required',
            'telefono_titular_sustituto'                             => 'required',
            /**beneficiarios */
            'beneficiarios.*.nombre'                                 => [
                'required',
            ],
            'beneficiarios.*.parentesco'                             => [
                'required',
            ],
            'beneficiarios.*.telefono'                               => [
                'required',
            ],
            //info del plan de venta y pagos
            //'planVenta.value' => 'numeric|required',
            'financiamiento'                                         => '',
            'tasa_iva'                                               => 'numeric|required|min:1|max:25',
            'subtotal'                                               => 'numeric|required|min:1',
            'descuento'                                              => 'required|numeric|min:0|max:' . $request->subtotal,
            'impuestos'                                              => 'numeric|required|min:0',
            'costo_neto'                                             => 'numeric|required|min:0',
            'costo_neto_pronto_pago'                                 => 'required|min:1|lte:' . $costo_neto,
            'pago_inicial'                                           => '',
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

        /**validando el pago inicial */
        if ($request->financiamiento == 1) {
            /**cuando es a contado */
            /**es un solo pago de inicio */
            $validaciones['pago_inicial'] = 'numeric|required|min:' . $request->costo_neto . '|max:' . $request->costo_neto;
        } else {
            //cuando es a credito
            $validaciones['pago_inicial'] = 'numeric|required|min:' . ($request->costo_neto * .1) . '|max:' . ($request->costo_neto * .7);
        }

        /**VALIDACIONES CONDICIONADAS*/
        /**deshabilitnado numero de titulo */
        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        /* if ($request->ventaAntiguedad['value'] == 3) {
        //venta de uso inmediato
        $validaciones['titulo'] = 'required';
        //validando de manera manual si el titulo enviado ya esta registrado y esto activa
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
         */

        if ($request->tipo_financiamiento == 1) {
            /**cuando es a contado */
            /**es un solo pago de inicio */
            $validaciones['financiamiento'] = 'numeric|required|min:' . 1 . '|max:' . 1;
        } else {
            //cuando es a credito
            $validaciones['financiamiento'] = 'numeric|required|min:' . 1 . '|max:' . 120;
        }

        //validnado en caso de que sea de uso futuro
        if ($request->tipo_financiamiento == 2) {
            //venta de uso inmediato
            $validaciones['solicitud'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $solicitud = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_solicitud', trim($request->solicitud))->where('operaciones.status', '<>', 0)->first();
            if (!empty($solicitud)) {
                if ($tipo_servicio == 'modificar') {
                    if ($solicitud->id != $request->id_venta) {
                        return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
                    }

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
                ->where('numero_convenio', trim($request->convenio))->where('operaciones.status', '<>', 0)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta) {
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                    }

                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
        } else if ($request->ventaAntiguedad['value'] == 3) {
            /**venta ya liquidada antes del sistema */
            $validaciones['convenio'] = 'required';
            //$validaciones['titulo'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
                ->where('numero_convenio', $request->convenio)->where('operaciones.status', '<>', 0)->first();
            if (!empty($convenio)) {
                if ($tipo_servicio == 'modificar') {
                    if ($convenio->id != $request->id_venta) {
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                    }

                } else {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            }
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            /**deshabilitnado numero de titulo */
            /* $titulo = VentasPlanes::select('ventas_planes.id')->join('operaciones', 'operaciones.ventas_planes_id', '=', 'ventas_planes.id')
        ->where('numero_titulo', $request->titulo)->where('operaciones.status', 1)->first();
        if (!empty($titulo)) {
        if ($tipo_servicio == 'modificar') {
        if ($titulo->id != $request->id_venta)
        return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
        } else {
        return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
        }
        }*/
        }

        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'id_venta.required'     => 'Ingrese un la clave única de la venta para continuar',
            'max'                   => 'verifique la cantidad',
            'required'              => 'Ingrese este dato',
            'numeric'               => 'Este dato debe ser un número',
            'ubicacion.unique'      => 'Este terreno ya fue vendido',
            'solicitud.unique'      => 'Esta solicitud ya fue registrada en otra venta',
            'convenio.unique'       => 'Este convenio ya fue registrado en otra venta',
            'titulo.unique'         => 'Este título ya fue registrado en otra venta',
            'num_operacion.unique'  => 'Este número de operación ya fue capturado',
            //beneficiarios
            '*.nombre.required'     => 'ingrese este dato',
            '*.parentesco.required' => 'ingrese este dato',
            '*.telefono.required'   => 'ingrese este dato',
            'lte'                   => 'verifique la cantidad',
            'unique.num_operacion'  => 'Este número de operación ya fue registrado.',
            'pago_inicial.min'      => 'El valor del pago inicial debe ser mínimo :min',

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
            /**verificando si cambio algo relativo al plan funerario */
            $plan_original = [
                'plan'        => $datos_venta['venta_plan']['nombre_original'],
                'plan_ingles' => $datos_venta['venta_plan']['nombre_original_ingles'],
                'nota'        => $datos_venta['venta_plan']['nota_original'],
                'nota_ingles' => $datos_venta['venta_plan']['nota_original_ingles'],
                'value'       => $datos_venta['venta_plan']['planes_funerarios_id'],
                'secciones'   => $datos_venta['venta_plan']['secciones_original'],
            ];
            /**checando si cambio algo del nombre del plan de venta */
            $es_igual = true;
            if (
                $plan_original['plan'] != $request->plan_funerario['plan'] ||
                $plan_original['plan_ingles'] != $request->plan_funerario['plan_ingles'] ||
                $plan_original['nota'] != $request->plan_funerario['nota']
                || $plan_original['nota_ingles'] != $request->plan_funerario['nota_ingles']
            ) {
                $es_igual = false;
            }
            if ($es_igual == true) {
                foreach ($request->plan_funerario['secciones'] as $key_seccion => $seccion) {
                    if (count($datos_venta['venta_plan']['secciones_original'][$key_seccion]['conceptos']) == count($seccion['conceptos'])) {
                        foreach ($seccion['conceptos'] as $key_concepto => $concepto) {
                            if ($concepto['concepto'] != $datos_venta['venta_plan']['secciones_original'][$key_seccion]['conceptos'][$key_concepto]['concepto']) {
                                $es_igual = false;
                                break;
                            }
                        }
                    } else {
                        $es_igual = false;
                        break;
                    }
                }
            }
            if (
                $request->financiamiento != $datos_venta['financiamiento'] ||
                $request->fecha_venta != $datos_venta['fecha_operacion'] ||
                (round($request->impuestos, 2, PHP_ROUND_HALF_UP) != round($datos_venta['impuestos'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->subtotal, 2, PHP_ROUND_HALF_UP) != round($datos_venta['subtotal'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->costo_neto, 2, PHP_ROUND_HALF_UP) != round($datos_venta['total'], 2, PHP_ROUND_HALF_UP) ||
                    ((float) $request->pago_inicial) != (count($datos_venta['pagos_programados']) > 0 ? ((float) $datos_venta['pagos_programados'][0]['monto_programado']) : 0) ||
                    round($request->descuento, 2, PHP_ROUND_HALF_UP) != round($datos_venta['descuento'], 2, PHP_ROUND_HALF_UP) ||
                    round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP) != round($datos_venta['costo_neto_pronto_pago'], 2, PHP_ROUND_HALF_UP)) ||
                !$es_igual
            ) {
                if ($datos_venta['total'] > 0) {
                    /**si la venta no fue gratis */
                    if ($datos_venta['pagos_realizados'] > 0) {
                        return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, tipo de venta, tipo de plan funerario, tipo de
                financiamiento, etc. Esto se debe a que existen pagos relacionados a esta venta y modificar cantidades o precios causaría que se perdiera la integridad de esta información.', 409);
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
                        'tipo_financiamiento'    => $request->tipo_financiamiento,
                        'vendedor_id'            => (int) $request->vendedor['value'],
                        'planes_funerarios_id'   => $request->plan_funerario['value'],
                        'nombre_original'        => $request->plan_funerario['plan'],
                        'nombre_original_ingles' => $request->plan_funerario['plan_ingles'],
                        'nota_original'          => trim($request->plan_funerario['nota']) != '' ? $request->plan_funerario['nota'] : 'N/A',
                        'nota_original_ingles'   => trim($request->plan_funerario['nota_ingles']) != '' ? $request->plan_funerario['nota_ingles'] : 'N/A',
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
                                'seccion_id'       => $seccion,
                                'ventas_planes_id' => $id_venta,
                                'concepto'         => $concepto['concepto'],
                                'concepto_ingles'  => $concepto['concepto_ingles'],
                            ]
                        );
                    }
                }

                /**a partir de la venta se crea la operaicon */
                $id_operacion = DB::table('operaciones')->insertGetId(
                    [
                        'clientes_id'                      => (int) $request->id_cliente,
                        'ventas_planes_id'                 => $id_venta,
                        /**venta a futuro solamente */
                        'numero_solicitud'                 => ($request->tipo_financiamiento == 2) ? $request->solicitud : null,
                        /**venta  liquidada solamente */
                        'numero_convenio'                  => $CementerioController->generarNumeroConvenio($request),
                        //'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                        'empresa_operaciones_id'           => 4, //venta de planes a futuro
                        'subtotal'                         => $subtotal,
                        'tasa_iva'                         => $tasa_iva,
                        'descuento'                        => $descuento,
                        'impuestos'                        => $iva,
                        'total'                            => $costo_neto,
                        'descuento_pronto_pago_b'          => 1,
                        'costo_neto_pronto_pago'           => round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP),
                        'antiguedad_operacion_id'          => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto'                => $request->titular_sustituto,
                        'parentesco_titular_sustituto'     => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto'       => $request->telefono_titular_sustituto,
                        'financiamiento'                   => $request->financiamiento,
                        'aplica_devolucion_b'              => 0,
                        'costo_neto_financiamiento_normal' => $costo_neto,
                        'comision_venta_neto'              => 0,
                        'fecha_registro'                   => now(),
                        'fecha_operacion'                  => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'registro_id'                      => (int) $request->user()->id,
                        'nota'                             => $request->nota,
                        'status'                           => $costo_neto > 0 ? 1 : '2',
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
                    //$CementerioController->generarNumeroTitulo($id_operacion, true);
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
                        'tipo_financiamiento'    => $request->tipo_financiamiento,
                        'vendedor_id'            => (int) $request->vendedor['value'],
                        'planes_funerarios_id'   => $request->plan_funerario['value'],
                        'nombre_original'        => $request->plan_funerario['plan'],
                        'nombre_original_ingles' => $request->plan_funerario['plan_ingles'],
                        'nota_original'          => trim($request->plan_funerario['nota']) != '' ? $request->plan_funerario['nota'] : 'N/A',
                        'nota_original_ingles'   => trim($request->plan_funerario['nota_ingles']) != '' ? $request->plan_funerario['nota_ingles'] : 'N/A',
                    ]
                );

                DB::table('plan_conceptos_original')->where('ventas_planes_id', $request->id_venta)->delete();
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
                                'seccion_id'       => $seccion,
                                'ventas_planes_id' => $request->id_venta,
                                'concepto'         => $concepto['concepto'],
                                'concepto_ingles'  => $concepto['concepto_ingles'],
                            ]
                        );
                    }
                }

                DB::table('operaciones')->where('id', '=', $datos_venta['operacion_id'])->update(
                    [
                        'clientes_id'                      => (int) $request->id_cliente,
                        /**venta a futuro solamente */
                        'numero_solicitud'                 => ($request->tipo_financiamiento == 2) ? trim($request->solicitud) : null,
                        /**venta  liquidada solamente */
                        'numero_convenio'                  => trim($request->convenio),
                        //'numero_titulo' => trim($request->titulo),
                        'subtotal'                         => $subtotal,
                        'tasa_iva'                         => $tasa_iva,
                        'descuento'                        => $descuento,
                        'impuestos'                        => $iva,
                        'total'                            => $costo_neto,
                        'descuento_pronto_pago_b'          => 1,
                        'costo_neto_pronto_pago'           => round($request->costo_neto_pronto_pago, 2, PHP_ROUND_HALF_UP),
                        'antiguedad_operacion_id'          => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto'                => $request->titular_sustituto,
                        'parentesco_titular_sustituto'     => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto'       => $request->telefono_titular_sustituto,
                        'financiamiento'                   => $request->financiamiento,
                        'costo_neto_financiamiento_normal' => $costo_neto,
                        'status'                           => ($costo_neto > 0 && $datos_venta['saldo_neto'] > 0) ? '1' : '2',
                        'fecha_modificacion'               => now(),
                        'fecha_operacion'                  => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'modifico_id'                      => (int) $request->user()->id,
                        'nota'                             => $request->nota,
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
                        $CementerioController->programarPagos($request, $datos_venta['operacion_id'], $request->id_venta, '004');
                    } else {
                        /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                        if (trim($datos_venta['numero_titulo']) == '') {
                            //$this->generarNumeroTitulo($datos_venta['operacion_id'], true);
                        }
                    }
                }
                //captura de los beneficiarios
                $CementerioController->guardarBeneficiarios($request, $datos_venta['operacion_id']);
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

    /**obtiene todas las ventas para el paginado de ventas de cementerio */
    public function get_ventas(Request $request, $id_venta = 'all', $paginated = false)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular                  = $request->titular;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;
        $fecha_operacion          = $request->fecha_operacion;

        $resultado_query = Operaciones::with('pagosProgramados.pagados')
            ->with('venta_plan.vendedor')
            ->with('venta_plan.conceptos_originales')
            ->with('venta_plan.conceptos_originales')
            ->with('beneficiarios')
            ->with('AjustesPoliticas')
            ->with('cancelador:id,nombre')
            ->with('registro:id,nombre')
            ->where('empresa_operaciones_id', '=', 4)
        /**solo ventas de planes funerarios */
            ->select(
                /**venta operacion */
                'operaciones.id as operacion_id',
                'antiguedad_operacion_id',
                'empresa_operaciones_id',
                'subtotal',
                'tasa_iva',
                'descuento',
                'impuestos',
                'total',
                'descuento_pronto_pago_b',
                'costo_neto_pronto_pago',
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'titular_sustituto',
                'parentesco_titular_sustituto',
                'telefono_titular_sustituto',
                'ventas_planes_id',
                'financiamiento',
                'aplica_devolucion_b',
                'costo_neto_financiamiento_normal',
                'comision_venta_neto',
                'operaciones.status as operacion_status',
                'clientes.id as cliente_id',
                'clientes.nombre',
                'clientes.direccion',
                'clientes.ciudad',
                'clientes.estado',
                'clientes.telefono',
                'clientes.celular',
                'clientes.telefono_extra',
                'clientes.rfc',
                'clientes.email',
                'clientes.fecha_nac',
                DB::raw(
                    'DATE(operaciones.fecha_operacion) as fecha_operacion'
                ),
                DB::raw(
                    'DATE(operaciones.fecha_cancelacion) as fecha_cancelacion_operacion'
                ),
                DB::raw(
                    '(NULL) as fecha_operacion_texto'
                ),
                'operaciones.nota',
                /**fin de datos de  operacion */
                DB::raw(
                    '(0) AS tipo_financimiento_texto'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados'
                ),
                DB::raw(
                    '(0) AS num_pagos_programados_vigentes'
                ),
                DB::raw(
                    '(0) AS intereses'
                ),
                DB::raw(
                    '(0) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_pronto_pago'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS pagos_vencidos'
                ),
                DB::raw(
                    '(0) AS dias_vencidos'
                ),
                DB::raw(
                    '(0) AS pagos_programados_cubiertos'
                ),
                DB::raw(
                    '(0) AS pagos_realizados'
                ),
                DB::raw(
                    '(0) AS pagos_vigentes'
                ),
                DB::raw(
                    '(0) AS pagos_cancelados'
                ),
                DB::raw(
                    '(CASE
                        WHEN operaciones.numero_solicitud <> "" THEN operaciones.numero_solicitud
                        ELSE "N/A"
                        END) AS numero_solicitud_texto'
                ),
                DB::raw(
                    '(CASE
                        WHEN operaciones.numero_titulo <> "" THEN operaciones.numero_titulo
                        ELSE "Pendiente"
                        END) AS numero_titulo_texto'
                ),
                DB::raw(
                    '(NULL) AS status_texto'
                ),
                /*DB::raw(
            '(NULL) AS pagos_realizados_arreglo'
            ),*/
                'operaciones.registro_id',
                'operaciones.cancelo_id',
                'operaciones.modifico_id',
                'operaciones.nota_cancelacion',
                'operaciones.motivos_cancelacion_id',
                'operaciones.cantidad_a_regresar_cancelacion',
                DB::raw(
                    '(NULL) AS motivos_cancelacion_texto'
                )
            )
            ->where(function ($q) use ($id_venta) {
                if (trim($id_venta) == 'all' || $id_venta > 0) {
                    if (trim($id_venta) == 'all') {
                        $q->where('operaciones.ventas_planes_id', '>', $id_venta);
                    } else if ($id_venta > 0) {
                        $q->where('operaciones.ventas_planes_id', '=', $id_venta);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_solicitud', '=', $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_convenio', '=', $numero_control);
                    } else if ($filtro_especifico_opcion == 3) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_titulo', '=', $numero_control);
                    } else {
                        /**filtro por numero de solicitud */
                        // $q->where('ventas_terrenos.id', $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('operaciones.status', '=', $status);
                }
            })
            ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->where('nombre', 'like', '%' . $titular . '%')
            ->orderBy('operaciones.ventas_planes_id', 'desc')
            ->get();
        /**verificando si el usario necesita el resultado paginado, todo o por id */
        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado       = &$resultado_query;
        }

        foreach ($resultado as $index_venta => &$venta) {

            /**DEFINIENDO EL STATUS DE LA VENTA*/
            if ($venta['operacion_status'] == 0) {
                $venta['status_texto'] = 'Cancelada';
                if ($venta['motivos_cancelacion_id'] == 1) {
                    /**fue por fal de pago */
                    $venta['motivos_cancelacion_texto'] = 'falta de pago';
                } elseif ($venta['motivos_cancelacion_id'] == 2) {
                    /**fue por peticion de lciente */
                    $venta['motivos_cancelacion_texto'] = 'a petición del cliente';
                } elseif ($venta['motivos_cancelacion_id'] == 3) {
                    /**fue por error de captura */
                    $venta['motivos_cancelacion_texto'] = 'error de captura';
                }
                /**actualizando el motivo de cancelacion */
            } elseif ($venta['operacion_status'] == 1) {
                $venta['status_texto'] = 'Por Pagar';
            } elseif ($venta['operacion_status'] == 2) {
                $venta['status_texto'] = 'Pagada';
            }

            $venta['fecha_operacion_texto'] = fecha_abr($venta['fecha_operacion']);

            /**aqui se saca el porcentaje para ver cuanto seria el descuento por pago en cada pronto pago */
            $porcentaje_descuento_pronto_pago = 0;
            if ($venta['total'] > 0) {
                $porcentaje_descuento_pronto_pago = ($venta['costo_neto_pronto_pago'] * 100) / ($venta['total']);
            }

            /**tipo de financiamiento texto */
            if ($venta['financiamiento'] == 1) {
                $venta['tipo_financimiento_texto'] = 'Uso inmediato/Pago único';
            } else {
                $venta['tipo_financimiento_texto'] = 'A futuro/'
                    . $venta['financiamiento'] . ' Mes(s)';
            }

            $venta['num_pagos_programados'] = count($venta['pagos_programados']);
            $num_pagos_programados_vigentes = 0;
            if ($venta['num_pagos_programados'] > 0) {
                /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                /**recorriendo arreglo de pagos programados */
                $vencidos                         = 0;
                $pagos_programados_cubiertos      = 0;
                $dias_vencido_primer_pago_vencido = '';
                $pagos_vigentes                   = 0;
                $pagos_cancelados                 = 0;
                $pagos_realizados                 = 0;

                $arreglo_de_pagos_realizados = [];
                /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                foreach ($venta['pagos_programados'] as $index_programado => &$programado) {
                    /**actualizando el concepto del pago */
                    if ($programado['conceptos_pagos_id'] == 1) {
                        $programado['concepto_texto'] = 'Enganche';
                    } elseif ($programado['conceptos_pagos_id'] == 2) {
                        $programado['concepto_texto'] = 'Abono';
                    } else {
                        $programado['concepto_texto'] = 'Pago Único';
                    }

                    /**actualizando fecha de pago abre con helper de fechas */
                    $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);

                    //if ($programado['status'] == 1) {
                    if ($programado['status'] == 1) {
                        $num_pagos_programados_vigentes++;
                    }
                    /**aumento el pago programado vigente */
                    /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                    /**montos segun su tipo de movimiento */
                    $abonado_intereses       = 0;
                    $abonado_capital         = 0;
                    $descontado_pronto_pago  = 0;
                    $descontado_capital      = 0;
                    $complemento_cancelacion = 0;
                    $total_cubierto          = 0;
                    $fecha_ultimo_pago       = '';

                    foreach ($programado['pagados'] as $index_pagados => &$pagado) {
                        /**haciendo el arreglo de pagos realizados limpio(no repetidos) */
                        array_push(
                            $arreglo_de_pagos_realizados,
                            $pagado
                        );

                        if ($pagado['status'] == 1) {
                            /**si esta activo el pago se toma en cuenta el monto de cada operacion */
                            /**tomando en cuenta solo pagos que son parent(todos los tipos menos abono a intereses y descuento por pronto pago, estos 2 tipos
                             * son los que van incluidos dentro de un parent) */
                            // if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                            /**aqui entrarian en los abonos a capital, descuento al capital y complementos por cancelacion*/
                            if ($pagado['movimientos_pagos_id'] == 1) {
                                /**si es de tipo 1, abono a copital, por lo regular podria llevar asociados pagos children
                                 * y se debe de recorrer el foreach para obtener los distintos montos asignados a cada pago programado
                                 */
                                // $pago_total += $pagado['monto'];
                                $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                            } else if ($pagado['movimientos_pagos_id'] == 4) {
                                /**fue descuento al capital */
                                $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                            } else if ($pagado['movimientos_pagos_id'] == 5) {
                                /**fue complemento por cancelacion */
                                $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                            } else if ($pagado['movimientos_pagos_id'] == 2) {
                                /**es tipo interes */
                                if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                    /**es abono de intereses */
                                    $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                    //$pago_total += $pagado['monto'];
                                }
                            } else if ($pagado['movimientos_pagos_id'] == 3) {
                                if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                    /**es descuento por pronto pago */
                                    $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                    //$pago_total += $pagado['monto'];
                                }
                            }

                            /**fecha en que se realizo el ultimo pago */
                            $fecha_ultimo_pago = $pagado['fecha_pago'];
                            // }
                            $pagos_vigentes++;
                        } //fin if pago status=1
                        else {
                            if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                $pagos_cancelados++;
                            }
                        }
                        if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                            $pagos_realizados++;
                        }
                    } //fin foreach pagado

                    /** al final del ciclo se actualizan los valores en el pago programado*/
                    $programado['abonado_capital']           = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                    $programado['abonado_intereses']         = $abonado_intereses;
                    $programado['descontado_pronto_pago']    = $descontado_pronto_pago;
                    $programado['descontado_capital']        = $descontado_capital;
                    $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                    $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                    $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);
                    /**asignando la fecha del pago que liquidado el pago programado */
                    if ($programado['saldo_neto'] <= 0) {
                        $programado['fecha_ultimo_pago']     = $fecha_ultimo_pago;
                        $programado['fecha_ultimo_pago_abr'] = fecha_abr($fecha_ultimo_pago);
                    }
                    /**verificando el estado del pago programado*/
                    /**verificando si la fecha sigue vigente o esta vencida */
                    /**variables para controlar el incremento por intereses */
                    $dias_retrasados_del_pago = 0;
                    $fecha_programada_pago    = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                    /**aqui verifico que si la operacion esta activa genere los intereses acorde al dia de hoy, si esta cancelada que tomen intereses a partir de la fecha de cancelacion */
                    $fecha_para_intereses = date('Y-m-d');
                    if ($venta['operacion_status'] == 0) {
                        if (trim($venta['fecha_cancelacion_operacion']) != '') {
                            $fecha_para_intereses = $venta['fecha_cancelacion_operacion'];
                        }
                    }

                    $fecha_hoy = Carbon::createFromFormat('Y-m-d', $fecha_para_intereses);

                    $interes_generado                = 0;
                    $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                    /**fin varables por intereses */
                    /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                    if (round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP) > 0) {
                        /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                        if (date('Y-m-d', strtotime($programado['fecha_programada'])) < date('Y-m-d')) {
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */

                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                            if ($dias_vencido_primer_pago_vencido == '') {
                                $dias_vencido_primer_pago_vencido = $dias_retrasados_del_pago;
                            }
                            $programado['fecha_a_pagar']     = date('Y-m-d');
                            $programado['fecha_a_pagar_abr'] = fecha_abr(date('Y-m-d'));
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             **/
                            /**aplicando intereses solo a abonos */
                            $interes_generado = 0;
                            if ($programado['conceptos_pagos_id'] == 2) {
                                $interes_generado = round(((($programado['monto_programado'] * ($venta['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago), 0, PHP_ROUND_HALF_UP);
                                if ($interes_generado > 0) {
                                    /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo abono a intereses */
                                    $interes_generado -= $programado['abonado_intereses'];
                                }
                            }

                            /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                            $programado['saldo_neto'] = round($saldo_pago_programado + $interes_generado, 2, PHP_ROUND_HALF_UP);
                            /**la fecha qui es mayor que la fecha programada del pago */
                            $programado['status_pago']       = 0;
                            $programado['status_pago_texto'] = 'Vencido';
                            $vencidos++;
                            $programado['dias_vencido'] = $dias_retrasados_del_pago;
                            $programado['intereses']    = $interes_generado;
                        } else {
                            /**la fecha aun no vence */
                            $programado['fecha_a_pagar']     = $programado['fecha_programada'];
                            $programado['status_pago']       = 1;
                            $programado['status_pago_texto'] = 'Pendiente';
                        }
                    } else {
                        $pagos_programados_cubiertos++;
                        $programado['fecha_a_pagar'] = $fecha_ultimo_pago;
                        /**el pago programado ya fue cubierto */
                        $programado['status_pago']       = 2;
                        $programado['status_pago_texto'] = 'Pagado';
                    }

                    /**monto con pronto pago de cada abono */
                    $programado['monto_pronto_pago'] = round(($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100, 0, PHP_ROUND_HALF_UP);
                    $programado['total_cubierto']    = $abonado_capital + $descontado_pronto_pago + $descontado_capital + $complemento_cancelacion;

                    /**actualizando los totales de montos en la venta */
                    $venta['intereses'] += $interes_generado;
                    $venta['abonado_capital'] += $abonado_capital;
                    $venta['abonado_intereses'] += $abonado_intereses;
                    $venta['descontado_pronto_pago'] += $descontado_pronto_pago;
                    $venta['descontado_capital'] += $descontado_capital;
                    $venta['complementado_cancelacion'] += $complemento_cancelacion;
                    $venta['saldo_neto'] += $saldo_pago_programado + $interes_generado;

                    /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                    $venta['total_cubierto'] += $programado['total_cubierto'];
                    /**verificado el monto que seria con pronnto pago  */
                    //} //fin foreach if status 1 programado
                } //fin foreach programados
                $venta['pagos_realizados']               = $pagos_realizados;
                $venta['pagos_vigentes']                 = $pagos_vigentes;
                $venta['num_pagos_programados_vigentes'] = $num_pagos_programados_vigentes;
                $venta['pagos_cancelados']               = $pagos_cancelados;
                $venta['pagos_programados_cubiertos']    = $pagos_programados_cubiertos;
                $venta['pagos_vencidos']                 = $vencidos;
                $venta['dias_vencidos']                  = $dias_vencido_primer_pago_vencido;
                /**areegloe de todos los pagos limpios(no repetidos) */
                //$venta['pagos_realizados_arreglo'] = $arreglo_de_pagos_realizados;
            } else {
                /**la venta no tiene pagos programados debido a que fue 100% "GRATIS" */
            }

            /**verificando el tipo de venta segun financiamiento*/
            if ($venta['venta_plan']['tipo_financiamiento'] == 1) {
                $venta['venta_plan']['tipo_financiamiento_texto'] = 'Uso Inmediato';
            } else {
                $venta['venta_plan']['tipo_financiamiento_texto'] = 'A Futuro';
            }

            /**agregando los conceptos originales del plan */
            $secciones = [
                [
                    'seccion'        => 'incluye',
                    'seccion_ingles' => 'include',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'inhumacion',
                    'seccion_ingles' => 'inhumation',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'cremacion',
                    'seccion_ingles' => 'cremation',
                    'conceptos'      => [],
                ],
                [
                    'seccion'        => 'velacion',
                    'seccion_ingles' => 'wakefulness',
                    'conceptos'      => [],
                ],
            ];
            foreach ($venta['venta_plan']['conceptos_originales'] as $key_seccion => $seccion) {
                /**agregando los conceptos segun su seccion */
                if ($seccion['seccion_id'] == 1) {
                    /**incluye */
                    array_push(
                        $secciones[0]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'plan funerario',
                            'seccion'         => 'incluye',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 2) {
                    /**inhumacion */
                    array_push(
                        $secciones[1]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de inhumación',
                            'seccion'         => 'inhumacion',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 3) {
                    /**cremacion */
                    array_push(
                        $secciones[2]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de cremación',
                            'seccion'         => 'cremacion',
                        ]
                    );
                } elseif ($seccion['seccion_id'] == 4) {
                    /**velacion */
                    array_push(
                        $secciones[3]['conceptos'],
                        [
                            'concepto'        => $seccion['concepto'],
                            'concepto_ingles' => $seccion['concepto_ingles'],
                            'aplicar_en'      => 'caso de velación',
                            'seccion'         => 'velacion',
                        ]
                    );
                }
            }
            /**push al array padre */
            $venta['venta_plan']['secciones_original'] = $secciones;
        } //fin foreach venta

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_ventas */
    }

    public function documento_solicitud(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_venta = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */

            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/solicitud/documento_solicitud', ['datos' => $datos_venta, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "SOLICITUD TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.solicitud.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.solicitud.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 5.4);
            $pdf->setOption('margin-right', 5.4);
            $pdf->setOption('margin-top', 5.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'SOLICITUD DE PLAN A FUTURO / FUNERARIA AETERNUS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function documento_convenio(Request $request)
    {
        try {
            /*  $id_venta = 3;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_convenio_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/convenio/documento_convenio', ['datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "CONVENIO TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.convenio.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.convenio.header'),
                ]);
            }
            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 20.4);
            $pdf->setOption('margin-right', 20.4);
            $pdf->setOption('margin-top', 15.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'COPIA DEL CONVENIO / CEMENTERIO AETERNUS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function documento_finiquitado(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];
            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /* $id_venta = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/finiquitado/finiquitado', ['datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "CONSTANCIA DE FINIQUITO DE PLAN FUNERARIO " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.finiquitado.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['saldo_neto'] > 0 && $datos_venta['operacion_status'] != 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.finiquitado.no_finiquitado_header'),
                ]);
            }
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.finiquitado.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 14.4);
            $pdf->setOption('margin-right', 14.4);
            $pdf->setOption('margin-top', 24.4);
            $pdf->setOption('margin-bottom', 30.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'CONSTANCIA DE FINIQUITO DE PLAN FUNERARIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function documento_estado_de_cuenta_planes(Request $request)
    {
        try {
            $id_venta = 1;
            $email    = false;
            $email_to = 'hector@gmail.com';
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */

            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */

            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }
            $pagos_operacion = [];
            $client          = new \GuzzleHttp\Client();
            try {
                /**TRAYENDO EL TOKEN PARA CONSUMIR EL SERVICE */
                $response = $client->request('POST', config('services.passport.login_endpoint'), [
                    'form_params' => [
                        'grant_type'    => 'client_credentials',
                        'client_id'     => config('services.passport.client_backend_id'),
                        'client_secret' => config('services.passport.client_backend_secret'),
                    ],
                ]);
                $token = json_decode((string) $response->getBody(), true)['access_token'];
                if ($token == '') {
                    return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', 409);
                }
                $pagos_operacion =
                    json_decode($client->request(
                    'GET',
                    env('APP_URL') . 'pagos/get_pagos_backend/all/false/false?operacion_id=' . $datos_venta['operacion_id'],
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                        ],
                    ]
                )->getBody(), true);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                return $this->errorResponse('Ocurrió un error durante la petición. Por favor reintente.', $e->getCode());
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/estado_cuenta/estado_cuenta', ['pagos_operacion' => $pagos_operacion, 'datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ESTADO CUENTA " . strtoupper($datos_venta['nombre']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.estado_cuenta.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.estado_cuenta.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            //$pdf->setOption('orientation', 'landscape');
            $pdf->setOption('margin-left', 12.4);
            $pdf->setOption('margin-right', 12.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'ESTADO DE CUENTA / PLAN FUNERARIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    public function referencias_de_pago(Request $request, $id_pago = '')
    {
        try {
            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */

            $id_venta = 1;
            $email    = false;
            $email_to = 'hector@gmail.com';

            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/pagos/referencias', ['id_pago' => $id_pago, 'datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "REFERENCIA DE PAGOS TITULAR " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.pagos.footer'),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.pagos.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            $pdf->setOption('orientation', 'landscape');
            $pdf->setOption('margin-left', 13.4);
            $pdf->setOption('margin-right', 13.4);
            $pdf->setOption('margin-top', 9.4);
            $pdf->setOption('margin-bottom', 13.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'REFERENCIAS DE PAGO PLAN FUNERARIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    /**pdf del convenio plan de cementerio */
    public function reglamento_pago(Request $request)
    {
        try {
            $id_venta = 1;
            $email    = false;
            $email_to = 'hector@gmail.com';
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];
            if (empty($datos_venta)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_convenio_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/reglamento_pago/reglamento', ['datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "REGLAMENTO DE PAGO " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.reglamento_pago.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_venta['operacion_status'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.reglamento_pago.header'),
                ]);
            }
            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 20.4);
            $pdf->setOption('margin-right', 20.4);
            $pdf->setOption('margin-top', 10.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'REGLAMENTO DE PAGO / FUNERARIA AETERNUS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    public function acuse_cancelacion(Request $request)
    {
        try {
            /*
            $id_venta = 4;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['venta_id'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            //obtengo la informacion de esa venta
            $datos_venta = $this->get_ventas($request, $id_venta, '')[0];

            if (empty($datos_venta)) {
                /**datos vacios */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/acuse_cancelacion/acuse', ['datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ACUSE DE CANCELACIÓN " . strtoupper($datos_venta['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.acuse_cancelacion.footer'),
            ]);
            if ($datos_venta['operacion_status'] != 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.acuse_cancelacion.header'),
                ]);
            }
            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 13.4);
            $pdf->setOption('margin-right', 13.4);
            $pdf->setOption('margin-top', 9.4);
            $pdf->setOption('margin-bottom', 13.4);
            $pdf->setOption('page-size', 'letter');
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'ACUSE DE CANCELACIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    public function servicio_acuse_cancelacion(Request $request)
    {

        try {
            $id_venta = 1;
            $email    = false;
            $email_to = 'hector@gmail.com';

            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_venta          = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            //obtengo la informacion de esa venta
            $datos_venta = $this->get_solicitudes_servicios($request, $id_venta, '')[0];

            if (empty($datos_venta)) {
                /**datos vacios */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/acuse_cancelacion_servicio/acuse', ['datos' => $datos_venta, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ACUSE DE CANCELACIÓN " . strtoupper($datos_venta['operacion']['cliente']['nombre']) . '.pdf';

            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.acuse_cancelacion_servicio.footer'),
            ]);
            if ($datos_venta['operacion']['operacion_status'] != 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.acuse_cancelacion_servicio.header'),
                ]);
            }
            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 13.4);
            $pdf->setOption('margin-right', 13.4);
            $pdf->setOption('margin-top', 9.4);
            $pdf->setOption('margin-bottom', 13.4);
            $pdf->setOption('page-size', 'letter');
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_venta['nombre']),
                    'ACUSE DE CANCELACIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al cargar los datos.', 409);
        }
    }

    /**CANCELAR LA VENTA */
    public function cancelar_venta(Request $request)
    {
        //return $request->minima_cuota_inicial;
        //validaciones directas sin condicionales
        try {
            //code...
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar este servicio.', 409);
        }
        $datos_venta = $this->get_ventas($request, $request->venta_id, '')[0];

        /**unicamente puede regresarse lo que  se ha cubierto de capital */
        $validaciones = [
            'venta_id'     => 'required',
            'motivo.value' => 'required',
            'cantidad'     => 'numeric|min:0|' . 'max:' . $datos_venta['abonado_capital'],
        ];

        $mensajes = [
            'required' => 'Ingrese este dato',
            'numeric'  => 'Este dato debe ser un número',
            'max'      => 'La cantidad a devolver no debe superar a la cantidad abonada hasta la fecha: $ ' . number_format($datos_venta['abonado_capital'], 2),
            'min'      => 'La cantidad a devolver debe ser mínimo: $ 00.00 Pesos MXN',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validar si la propiedad tiene gente sepultada */
        /**pendiente
         * pendiente
         * pendiente
         * pendiente
         */

        /**validar si la propiedad no fue dada de baja ya */

        if ($datos_venta['operacion_status'] == 0) {
            return $this->errorResponse('Esta venta ya habia sido dada de baja.', 409);
        }

        try {
            DB::beginTransaction();

            DB::table('operaciones')->where('ventas_planes_id', $request->venta_id)->update(
                [
                    'motivos_cancelacion_id'          => $request['motivo.value'],
                    'fecha_cancelacion'               => now(),
                    'cantidad_a_regresar_cancelacion' => (float) $request->cantidad,
                    'cancelo_id'                      => (int) $request->user()->id,
                    'nota_cancelacion'                => $request->comentario,
                    'status'                          => 0,
                ]
            );
            DB::commit();
            return $request->venta_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_personal_recoger()
    {
        //no super usuarios
        /**puesto de venderor id 4 */
        /**obtiene los usuarios con puesto de servicios operativos */
        return User::select('id', 'nombre')
            ->join('usuarios_puestos', 'usuarios_puestos.usuarios_id', '=', 'usuarios.id')
            ->where('roles_id', '>', 1)
            ->where('puestos_id', '=', 4)
            ->where('usuarios.status', '>', 0)
            ->get();
    }

    public function control_solicitud(Request $request, $tipo_servicio = '')
    {

        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }

        //validaciones directas sin condicionales
        $validaciones = [
            'llamada_b'              => 'required',
            'nombre_afectado'        => 'required',
            'fecha_solicitud'        => 'required',
            'causa_muerte'           => 'required',
            'muerte_natural_b.value' => 'required',
            'contagioso_b.value'     => 'required',
            'nombre_informante'      => 'required',
            'telefono_informante'    => 'required',
            'parentesco_informante'  => 'required',
            'recogio.value'          => 'required',
            'id_solicitud'           => '',
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'required' => 'Ingrese este dato',
        ];

        request()->validate(
            $validaciones,
            $mensajes
        );
        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_solicitud = array();
        if ($tipo_servicio == 'modificar') {
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $datos_solicitud = $this->get_solicitudes_servicios($r, $request->id_solicitud)[0];
            if (empty($datos_solicitud)) {
                /**no se encontro los datos */
                return $this->errorResponse('No se encontró la información de la solicitud solicitada', 409);
            } else if ($datos_solicitud['status_b'] == 0) {
                return $this->errorResponse('Esta solicitud ya fue cancelada, no puede modificarse', 409);
            }
        }
        $id_return = 0;
        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                $id_servicio = DB::table('servicios_funerarios')->insertGetId(
                    [
                        'tipo_solicitud_id'     => 1,
                        'llamada_b'             => $request->llamada_b,
                        'nombre_afectado'       => $request->nombre_afectado,
                        'fechahora_solicitud'   => $request->fecha_solicitud,
                        'causa_muerte'          => $request->causa_muerte,
                        'muerte_natural_b'      => $request->muerte_natural_b['value'],
                        'contagioso_b'          => $request->contagioso_b['value'],
                        'nombre_informante'     => $request->nombre_informante,
                        'telefono_informante'   => $request->telefono_informante,
                        'parentesco_informante' => $request->parentesco_informante,
                        'ubicacion_recoger'     => $request->ubicacion_recoger,
                        'recogio_id'            => $request->recogio['value'],
                        'nota_al_recoger'       => $request->nota_al_recoger,
                        'registro_id'           => (int) $request->user()->id,
                        'fechahora_registro'    => now(),
                    ]
                );
                $id_return = $id_servicio;
                /**todo salio bien y se debe de guardar */
            } else {
                /**es modificar */
                DB::table('servicios_funerarios')->where('id', $request->id_solicitud)->update(
                    [
                        'llamada_b'             => $request->llamada_b,
                        'nombre_afectado'       => $request->nombre_afectado,
                        'fechahora_solicitud'   => $request->fecha_solicitud,
                        'causa_muerte'          => $request->causa_muerte,
                        'muerte_natural_b'      => $request->muerte_natural_b['value'],
                        'contagioso_b'          => $request->contagioso_b['value'],
                        'nombre_informante'     => $request->nombre_informante,
                        'telefono_informante'   => $request->telefono_informante,
                        'parentesco_informante' => $request->parentesco_informante,
                        'ubicacion_recoger'     => $request->ubicacion_recoger,
                        'recogio_id'            => $request->recogio['value'],
                        'nota_al_recoger'       => $request->nota_al_recoger,
                        'modifico_id'           => (int) $request->user()->id,
                        'fecha_modificacion'    => now(),
                    ]
                );
                $id_return = $request->id_solicitud;
            }
            DB::commit();
            return $id_return;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function control_contratos(Request $request, $tipo_servicio = '')
    {
        if (!(trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }

        //validaciones directas sin condicionales
        $validaciones = [
            'id_servicio'                                => 'required',
            /**DATOS DEL FALLECIDO */
            'titulo.value'                               => 'required',
            'nombre_afectado'                            => 'required',
            'fecha_nacimiento'                           => 'required',
            'genero.value'                               => 'required',
            'nacionalidad.value'                         => 'required',
            'estado_civil.value'                         => 'required',
            'escolaridad.value'                          => 'required',
            'afiliacion.value'                           => 'required',

            /**DATOS DEL CERTIFICADO MEDICO */
            'fechahora_defuncion'                        => 'required',
            'causa_muerte'                               => 'required',
            'muerte_natural_b.value'                     => 'required',
            'contagioso_b.value'                         => 'required',
            'sitio_muerte.value'                         => 'required',
            'atencion_medica_b.value'                    => 'required',
            'estado_cuerpo.value'                        => 'required',

            /**DESTINOS DEL SERVICIO */
            'embalsamar_b'                               => 'required|numeric|min:0|max:1',
            'preparador'                                 => '',

            'velacion_b'                                 => 'required|numeric|min:0|max:1',
            'lugar_servicio.value'                       => '',
            'direccion_velacion'                         => '',

            'cremacion_b'                                => 'required|numeric|min:0|max:1',
            'fechahora_cremacion'                        => '',
            'fechahora_entrega_cenizas'                  => '',

            'inhumacion_b'                               => 'required|numeric|min:0|max:1',
            'cementerio_servicio.value'                  => '',
            'fechahora_inhumacion'                       => '',
            'ubicacion'                                  => '',
            'ventas_terrenos_id'                         => '',

            'traslado_b'                                 => 'required|numeric|min:0|max:1',
            'fechahora_traslado'                         => '',
            'destino_traslado'                           => '',

            'aseguradora_b'                              => 'required|numeric|min:0|max:1',
            'aseguradora'                                => '',

            'misa_b'                                     => 'required|numeric|min:0|max:1',
            'fechahora_misa'                             => '',
            'iglesia_misa'                               => '',

            'custodia_b'                                 => 'required|numeric|min:0|max:1',

            'material_velacion_b'                        => 'required|numeric|min:0|max:1',
            'material_velacion'                          => '',

            'acta_b'                                     => 'required|numeric|min:0|max:1',
            'folio_acta'                                 => '',
            'fecha_acta'                                 => '',

            /**DATOS DEL CONTRATO */
            'fechahora_contrato'                         => 'required',
            'id_cliente'                                 => 'required|numeric|min:1',
            'tasa_iva'                                   => 'required|numeric|min:14|max:25',

            'plan_funerario_futuro_b.value'              => 'required|numeric|min:0|max:1',
            'id_convenio_plan'                           => '',
            'tipo_contratante.value'                     => '',

            'plan_funerario_inmediato_b.value'           => 'required|numeric|min:0|max:1',
            'plan_funerario.value'                       => '',

            /**ARTICULOS DEL SERVICIO FUNERARIO */
            //'articulos_servicios' => 'required',
            'articulos_servicios.*.id'                   => 'integer|min:1',
            'articulos_servicios.*.cantidad'             => 'integer|min:1',
            'articulos_servicios.*.costo_neto_normal'    => 'numeric|min:0',
            'articulos_servicios.*.costo_neto_descuento' => 'numeric|min:0',
            'articulos_servicios.*.plan_b'               => 'boolean',
            'articulos_servicios.*.descuento_b'          => 'boolean',
            'articulos_servicios.*.facturable_b'         => 'boolean',
        ];

        /**VALIDACIONES CONDICIONADAS */
        if ($request->embalsamar_b == 1) {
            $validaciones['preparador'] = 'required';
        }

        if ($request->velacion_b == 1) {
            $validaciones['lugar_servicio.value'] = 'required|numeric|min:0';
            $validaciones['direccion_velacion']   = 'required';
        }

        if ($request->cremacion_b == 1) {
            $validaciones['fechahora_cremacion']       = 'required';
            $validaciones['fechahora_entrega_cenizas'] = 'required';
        }

        if ($request->inhumacion_b == 1) {
            $validaciones['cementerio_servicio.value'] = 'required|numeric|min:0|max:3';
            $validaciones['fechahora_inhumacion']      = 'required';
            if ($request->cementerio_servicio['value'] == 1) {
                $validaciones['ventas_terrenos_id'] = 'required|numeric|min:0';
            } else {
                $validaciones['ubicacion'] = 'required';
            }
        }

        if ($request->traslado_b == 1) {
            $validaciones['fechahora_traslado'] = 'required';
            $validaciones['destino_traslado']   = 'required';
        }

        if ($request->aseguradora_b == 1) {
            $validaciones['aseguradora'] = 'required';
        }

        if ($request->misa_b == 1) {
            $validaciones['fechahora_misa'] = 'required';
            $validaciones['iglesia_misa']   = 'required';
        }

        if ($request->material_velacion_b == 1) {
            $validaciones['material_velacion.*.id']       = 'required|integer|min:1';
            $validaciones['material_velacion.*.cantidad'] = 'required|integer|min:0';
        }

        if ($request->acta_b == 1) {
            $validaciones['folio_acta'] = 'required';
            $validaciones['fecha_acta'] = 'required';
        }

        if ($request->plan_funerario_futuro_b['value'] == 1) {
            /**tiene un servicio funerario asociado */
            $validaciones['id_convenio_plan'] = 'required|integer|min:1';
        } else {
            /**NO TIENE PLAN DE USO A FUTURO Y SE VERIFICA SI TIENE UNO DE USO INMEDIATO */
            if ($request->plan_funerario_inmediato_b['value'] == 1) {
                $validaciones['plan_funerario.value']      = 'required|integer|min:1';
                $validaciones['plan_funerario.label']      = 'required';
                $validaciones['plan_funerario.costo_neto'] = 'required|numeric|min:0';
                $validaciones['plan_funerario.secciones']  = 'required';
            }
        }
        /**VALIDANDO LOS DATOS EN CASO DE QUE USE UN SERVICIO FUNERARIO*/

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'material_velacion.*.cantidad.min'     => 'La cantidad debe ser mínimo 0',
            'material_velacion.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'required'                             => 'Ingrese este dato',
        ];

        request()->validate(
            $validaciones,
            $mensajes
        );
        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_solicitud = array();
        if ($tipo_servicio == 'modificar') {
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $datos_solicitud = $this->get_solicitudes_servicios($r, $request->id_servicio)[0];
            if (empty($datos_solicitud)) {
                /**no se encontro los datos */
                return $this->errorResponse('No se encontró la información de la solicitud solicitada', 409);
            } else if ($datos_solicitud['status_b'] == 0) {
                return $this->errorResponse('Esta solicitud ya fue cancelada, no puede modificarse', 409);
            }

            if ($request->plan_funerario_futuro_b['value'] == 1) {
                /**utiliza un servicio funerario a futuro */
                /**agregada validacion para la gerencia de no reusar el mismo plan funerario */
                $servicios_planes_usados = ServiciosFunerarios::select('status', 'nombre_afectado')
                    ->where('id', '<>', $request->id_servicio)
                    ->where('ventas_planes_id', '=', $request->id_convenio_plan)->where('status', '<>', 0)->get();
                if (count($servicios_planes_usados) > 0) {
                    /**se han encontrado servicios funerarios donde el plan funerario a futuro ingresado ha sido utilizado */
                    return $this->errorResponse('El plan funerario a futuro seleccionado ya ha sido utilizado por el servicio prestado al finado : ' . $servicios_planes_usados[0]->nombre_afectado, 409);
                }

            }
        }
        $id_return = 0;
        try {
            DB::beginTransaction();
            /**SE COMIENZA EL PROCESO PARA ACTUALIZAR EL CONTRATO */
            DB::table('servicios_funerarios')->where('id', $request->id_servicio)->update(
                [
                    /**ACTUALIZANDO LA PARTE DEL FALLECIDO */
                    'titulos_id'                        => $request->titulo['value'],
                    'nombre_afectado'                   => strtoupper($request->nombre_afectado),
                    'fecha_nacimiento'                  => $request->fecha_nacimiento,
                    'generos_id'                        => $request->genero['value'],
                    'nacionalidades_id'                 => $request->nacionalidad['value'],
                    'lugar_nacimiento'                  => $request->lugar_nacimiento != null ? strtoupper($request->lugar_nacimiento) : null,
                    'ocupacion'                         => $request->ocupacion != null ? strtoupper($request->ocupacion) : null,
                    'direccion_fallecido'               => $request->direccion_fallecido != null ? strtoupper($request->direccion_fallecido) : null,
                    'estados_civiles_id'                => $request->estado_civil['value'],
                    'escolaridades_id'                  => $request->escolaridad['value'],
                    'afiliaciones_id'                   => $request->afiliacion['value'],
                    'afiliacion_nota'                   => $request->afiliacion_nota != null ? strtoupper($request->afiliacion_nota) : null,
                    /**ACTUALIZANDO EL CERTIFICADO DE DEFUNCION */
                    'folio_certificado'                 => $request->acta_b == 1 ? strtoupper($request->folio_certificado) : null,
                    'fechahora_defuncion'               => $request->fechahora_defuncion,
                    'causa_muerte'                      => strtoupper($request->causa_muerte),
                    'muerte_natural_b'                  => $request->muerte_natural_b['value'],
                    'contagioso_b'                      => $request->contagioso_b['value'],
                    'sitios_muerte_id'                  => $request->sitio_muerte['value'],
                    'lugar_muerte'                      => $request->lugar_muerte != null ? strtoupper($request->lugar_muerte) : null,
                    'atencion_medica_b'                 => $request->atencion_medica_b['value'],
                    'enfermedades_padecidas'            => $request->enfermedades_padecidas != null ? strtoupper($request->enfermedades_padecidas) : null,
                    'certificado_informante'            => $request->certificado_informante != null ? strtoupper($request->certificado_informante) : null,
                    'certificado_informante_telefono'   => $request->certificado_informante_telefono != null ? strtoupper($request->certificado_informante_telefono) : null,
                    'certificado_informante_parentesco' => $request->certificado_informante_parentesco != null ? strtoupper($request->certificado_informante_parentesco) : null,
                    'medico_legista'                    => $request->medico_legista != null ? strtoupper($request->medico_legista) : null,
                    'estado_afectado_id'                => $request->estado_cuerpo['value'],
                    /**ACTUALIZANDO LOS DESTINOS DEL SERVICIO */
                    'embalsamar_b'                      => $request->embalsamar_b != 1 ? 0 : 1,
                    'preparador'                        => $request->preparador != null ? ($request->embalsamar_b == 1 ? strtoupper($request->preparador) : null) : null,
                    'medico_responsable_embalsamado'    => $request->embalsamar_b != 1 ? null : ($request->embalsamar_b == 1 ? strtoupper($request->medico_responsable_embalsamado) : null),
                    'velacion_b'                        => $request->velacion_b != 1 ? 0 : 1,
                    'lugares_servicios_id'              => $request->velacion_b != 1 ? null : strtoupper($request->lugar_servicio['value']),
                    'direccion_velacion'                => $request->velacion_b != 1 ? null : strtoupper($request->direccion_velacion),
                    'cremacion_b'                       => $request->cremacion_b != 1 ? 0 : 1,
                    'fechahora_cremacion'               => $request->cremacion_b != 1 ? null : $request->fechahora_cremacion,
                    'fechahora_entrega_cenizas'         => $request->cremacion_b != 1 ? null : $request->fechahora_entrega_cenizas,
                    'descripcion_urna'                  => $request->cremacion_b != 1 ? null : strtoupper($request->descripcion_urna),
                    'inhumacion_b'                      => $request->inhumacion_b != 1 ? 0 : 1,
                    'fechahora_inhumacion'              => $request->inhumacion_b != 1 ? null : $request->fechahora_inhumacion,
                    'cementerios_servicio_id'           => $request->inhumacion_b != 1 ? null : $request->cementerio_servicio['value'],
                    'ventas_terrenos_id'                => $request->inhumacion_b != 1 ? null : ($request->cementerio_servicio['value'] == 1 ? $request->ventas_terrenos_id : null),
                    'nota_ubicacion'                    => $request->inhumacion_b != 1 ? null : ($request->cementerio_servicio['value'] != 1 ? strtoupper($request->ubicacion) : null),
                    'traslado_b'                        => $request->traslado_b != 1 ? 0 : 1,
                    'fechahora_traslado'                => $request->traslado_b != 1 ? null : $request->fechahora_traslado,
                    'destino_traslado'                  => $request->traslado_b != 1 ? null : strtoupper($request->destino_traslado),
                    'aseguradora_b'                     => $request->aseguradora_b != 1 ? 0 : 1,
                    'numero_convenio_aseguradora'       => $request->aseguradora_b != 1 ? null : $request->numero_convenio_aseguradora,
                    'aseguradora'                       => $request->aseguradora_b != 1 ? null : strtoupper($request->aseguradora),
                    'telefono_aseguradora'              => $request->aseguradora_b != 1 ? null : $request->telefono_aseguradora,
                    'misa_b'                            => $request->misa_b != 1 ? 0 : 1,
                    'iglesia_misa'                      => $request->misa_b != 1 ? null : strtoupper($request->iglesia_misa),
                    'direccion_iglesia'                 => $request->misa_b != 1 ? null : strtoupper($request->direccion_iglesia),
                    'fechahora_misa'                    => $request->misa_b != 1 ? null : $request->fechahora_misa,
                    'custodia_b'                        => $request->custodia_b != 1 ? 0 : 1,
                    'responsable_custodia'              => $request->custodia_b != 1 ? null : strtoupper($request->responsable_custodia),
                    'folio_custodia'                    => $request->custodia_b != 1 ? null : $request->folio_custodia,
                    'folio_liberacion'                  => $request->custodia_b != 1 ? null : $request->folio_liberacion,
                    /**MATERIAL DE VELACION */
                    'material_velacion_b'               => $request->material_velacion_b != 1 ? 0 : 1,
                    /**PENDIENTE DE BORRAR MATERIAL EN CASO DE QUE NO LLEVE MATERIAL EL SERVICIO */
                    /**ACTA DE DEFUNCION */
                    'acta_b'                            => $request->acta_b != 1 ? 0 : 1,
                    'fechahora_acta'                    => $request->acta_b != 1 ? null : $request->fecha_acta,
                    'folio_acta'                        => $request->acta_b != 1 ? null : $request->folio_acta,
                    /**DATOS DEL CONTRATO */
                    'fechahora_contrato'                => $request->fechahora_contrato,
                    'parentesco_contratante'            => strtoupper($request->parentesco_contratante),
                    'plan_funerario_futuro_b'           => $request->plan_funerario_futuro_b['value'] != 1 ? 0 : 1,
                    'ventas_planes_id'                  => $request->plan_funerario_futuro_b['value'] != 1 ? null : $request->id_convenio_plan,
                    'tipos_contratante_id'              => $request->plan_funerario_futuro_b['value'] != 1 ? null : $request->tipo_contratante['value'],
                    'plan_funerario_inmediato_b'        => ($request->plan_funerario_futuro_b['value'] == 1) ? 0 : $request->plan_funerario_inmediato_b['value'],
                    'planes_funerarios_id'              => ($request->plan_funerario_inmediato_b['value'] != 1 || $request->plan_funerario_futuro_b['value'] == 1) ? null : $request->plan_funerario['value'],
                    'plan_funerario_original'           => ($request->plan_funerario_inmediato_b['value'] != 1 || $request->plan_funerario_futuro_b['value'] == 1) ? null : $request->plan_funerario['plan'],
                    'costo_plan_original'               => ($request->plan_funerario_inmediato_b['value'] != 1 || $request->plan_funerario_futuro_b['value'] == 1) ? null : $request->plan_funerario['costo_neto'],
                    'modifico_id'                       => (int) $request->user()->id,
                    'fecha_modificacion'                => now(),
                    'registro_contrato_id'              => $datos_solicitud['registro_contrato_id'] == null ? (int) $request->user()->id : $datos_solicitud['registro_contrato_id'],
                    'nota_servicio'                     => strtoupper($request->nota),
                ]
            );

            /**ELIMINANDO EL MATERIAL DE VELACION ANTERIOR */
            DB::table('material_rentado')->where('servicios_funerarios_id', '=', $request->id_servicio)->delete();
            if ($request->material_velacion_b == 1) {
                foreach ($request->material_velacion as $material) {
                    DB::table('material_rentado')->insert(
                        [
                            'servicios_funerarios_id' => $request->id_servicio,
                            'articulos_id'            => $material['id'],
                            'cantidad'                => $material['cantidad'],
                            'nota'                    => $material['nota'],
                        ]
                    );
                }
            }

            /**ACTUALIZANDO LOS CONCEPTOS ORIGINALES DEL PLAN FUNERARIO DE USO INMEDIATO */
            DB::table('plan_conceptos_servicio_original')->where('servicios_funerarios_id', '=', $request->id_servicio)->delete();
            if ($request->plan_funerario_inmediato_b['value'] == 1) {
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
                        DB::table('plan_conceptos_servicio_original')->insert(
                            [
                                'seccion_id'              => $seccion,
                                'servicios_funerarios_id' => $request->id_servicio,
                                'concepto'                => $concepto['concepto'],
                                'concepto_ingles'         => $concepto['concepto_ingles'],
                            ]
                        );
                    }
                }
            }

            /**ACTUALIZANDO EL LA TABLA DE OPERACION */
            $subtotal  = 0;
            $descuento = 0;
            $impuestos = 0;
            $total     = 0;

            //CARGANDO EL INVENTARIO PARA COMPARAR DISPONIBILIDAD
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $inventario_lotes = $this->get_inventario($r);

            /**se inicializan los valores para el id de la operacion y el movimiento en el inventario */
            $id_operacion             = null;
            $id_movimiento_inventario = null;
            if ($datos_solicitud['operacion'] == null && !isset($datos_solicitud['operacion']['movimientoinventario'])) {
                /**LA OPERACION NIO EXISTE Y SE DEBE DE REGISTRAR */
                /**UNA VEZ ARRIBA CALCULADO LOS MONTOS SE PROCEDE A ACTUALIZAR TABLAS */
                $id_operacion = DB::table('operaciones')->insertGetId(
                    [
                        'financiamiento'          => 1,
                        'clientes_id'             => $request->id_cliente,
                        'empresa_operaciones_id'  => 3,
                        'aplica_devolucion_b'     => 0,
                        'fecha_operacion'         => $request->fechahora_contrato,
                        'fecha_registro'          => now(),
                        'servicios_funerarios_id' => $request->id_servicio,
                        'subtotal'                => $subtotal,
                        'descuento'               => 0,
                        'impuestos'               => $impuestos,
                        'total'                   => $total,
                        'tasa_iva'                => $request->tasa_iva,
                        'antiguedad_operacion_id' => 1,
                        'registro_id'             => (int) $request->user()->id,
                        'status'                  => 1,
                    ]
                );
                /**se registra el movimiento en el inventario */
                $id_movimiento_inventario = DB::table('movimientos_inventario')->insertGetId(
                    [
                        'fecha_movimiento'    => $request->fechahora_contrato,
                        'fecha_registro'      => now(),
                        'operaciones_id'      => $id_operacion,
                        'tipo_movimientos_id' => 9, //venta de mercancia
                        'registro_id'         => (int) $request->user()->id,
                        'status'              => 1,
                    ]
                );
            } else {
                /**se toman los ids de la operacion existente */
                $id_operacion             = $datos_solicitud['operacion']['id'];
                $id_movimiento_inventario = $datos_solicitud['operacion']['movimientoinventario']['id'];
            }

            /**verificando si la operacion existia */
            $operacion_existia = isset($datos_solicitud['operacion']['movimientoinventario']) ? true : false;
            /**VERIFICANDO PRIMERO LA EXISTENCIA DE ARTICULOS EN INVENTARIO */
            /**consultas para detalle venta y actualizacion del inventario */
            $detalle_venta      = [];
            $detalle_inventario = [];
            //CARGANDO EL INVENTARIO PARA COMPARAR DISPONIBILIDAD
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $inventario                     = $this->get_inventario($r);
            $subtotal                       = 0;
            $descuento                      = 0;
            $impuestos                      = 0;
            $total                          = 0;
            $articulos_servicios_recorridos = [];
            /**arreglo vacio para que cada que se encuentre en la lista de artivulos enviados se descarte en la proxima vuelta */
            foreach ($request->articulos_servicios as $index_articulo_servicio => $articulo_servicio) {
                if (in_array($index_articulo_servicio, $articulos_servicios_recorridos)) {
                    /**me brinco al siguiente */
                    continue;
                }
                /**busncando articulo en el inventario actual */
                $articulo_encontrado = false;
                foreach ($inventario as $articulo) {
                    if ($articulo_servicio['id'] == $articulo['id']) {
                        $articulo_encontrado = true;
                        /**articulo encontrado */
                        if ($articulo['status'] != 0) {
                            /**comienzo recorrer el invnetario actual para comparar la existencia que pide vender el operador*/
                            if ($articulo['tipo_articulos_id'] != 2) {
                                /**no es de tipo servicio */
                                /**verificando si la operacion ya existia, y ver si la cantidd que pide sigue estando disponible */
                                /**comienza existia   if ($operacion_existia) {*/
                                /**la operacion ya existia, por lo tanto se revisa la existencia actual en el inventario y la que el usuario pide de nuevo */
                                $existe_lote = false;
                                foreach ($articulo['inventario'] as $lote) {
                                    if ($lote['lotes_id'] == $articulo_servicio['lote']) {
                                        /**el lote existe */
                                        $existe_lote     = true;
                                        $tenia_articulos = false;
                                        if (isset($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'])) {
                                            if (count($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario']) > 0) {
                                                /**la operacion ya tenia articulos y servicios agregados y se debe de revisar disponibilidad agregada mas la actual*/
                                                $tenia_articulos = true;
                                            }
                                        }
                                        /**se verifica la existencia que hay actualmente mas la que tiene el servicio actualmente y ver si hay disponibilidad */
                                        /**verifico si el lote fue solicitado en diferentes precios y cantidades */
                                        /**la existencia actual en el inventario de este lote de este articulo esta en $lote['existencia'] */
                                        $cantidad_lote_solicitado = 0;
                                        foreach ($request->articulos_servicios as $index_encontrado => $articulo_servicio_index) {
                                            if ($articulo_servicio_index['id'] == $lote['articulos_id'] && $articulo_servicio_index['lote'] == $lote['lotes_id']) {
                                                /**si el articulo viene varias veces bajo el mismo lote, lo agregamos a la lista para que no se repita
                                                 * y sumamos la cantidad que pide
                                                 */
                                                $cantidad_lote_solicitado += $articulo_servicio_index['cantidad'];
                                                array_push($articulos_servicios_recorridos, $index_encontrado);
                                                /**aqui comenzo a agregar lo que seran los nuevos registros del sistema para el detalle de venta de articulos*/
                                                /**verificando los costos para saber si aplicar costo de plan funeario*/
                                                if ($request->plan_funerario_futuro_b['value'] == 1) {
                                                    /**maneja plan funerario de uso a futuro */
                                                    /**checando que exista el id de un plan funerario de uso a futuro */
                                                    if (trim($request->id_convenio_plan) != '') {
                                                        /**si se capturo el id del plan funerario a futuro vendido */
                                                        if ($articulo_servicio_index['plan_b'] == 1) {
                                                            /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                                            array_push($detalle_venta, [
                                                                'cantidad'                  => $articulo_servicio_index['cantidad'],
                                                                'lotes_id'                  => $articulo_servicio_index['lote'],
                                                                'movimientos_inventario_id' => $id_movimiento_inventario,
                                                                'articulos_id'              => $articulo_servicio_index['id'],
                                                                'costo_neto_normal'         => 0,
                                                                'costo_neto_descuento'      => 0,
                                                                'descuento_b'               => 0,
                                                                'plan_b'                    => 1,
                                                                'facturable_b'              => $articulo_servicio_index['facturable_b'],
                                                            ]);
                                                        } else {
                                                            /**no es parte del plan funerario */
                                                            if ($articulo_servicio_index['descuento_b'] == 1) {
                                                                //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                                                if ($articulo_servicio_index['costo_neto_normal'] >= $articulo_servicio_index['costo_neto_descuento']) {
                                                                    /**si se puede aplicar descuento */
                                                                    if ($articulo_servicio_index['facturable_b'] == 1) {
                                                                        /**se desglosa el IVA */
                                                                        $subtotal += (($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                        $impuestos += ((($articulo_servicio_index['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                        $descuento += ((($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) - ($articulo_servicio_index['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100)))) * $articulo_servicio_index['cantidad']);
                                                                    } else {
                                                                        /**no grava IVA */
                                                                        $subtotal += (($articulo_servicio_index['costo_neto_descuento']) * $articulo_servicio_index['cantidad']);
                                                                        $descuento += ((($articulo_servicio_index['costo_neto_normal']) - ($articulo_servicio_index['costo_neto_descuento'])) * $articulo_servicio_index['cantidad']);
                                                                    }
                                                                    //sumando el total
                                                                    $total += $articulo_servicio_index['costo_neto_descuento'] * $articulo_servicio_index['cantidad'];
                                                                } else {
                                                                    /**no se puede proceder por que el precio de descuento no es correcto */
                                                                    return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                                                }
                                                                /**el registro con descuento_b */
                                                                /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                                                array_push($detalle_venta, [
                                                                    'cantidad'                  => $articulo_servicio_index['cantidad'],
                                                                    'lotes_id'                  => $articulo_servicio_index['lote'],
                                                                    'movimientos_inventario_id' => $id_movimiento_inventario,
                                                                    'articulos_id'              => $articulo_servicio_index['id'],
                                                                    'costo_neto_normal'         => $articulo_servicio_index['costo_neto_normal'],
                                                                    'costo_neto_descuento'      => $articulo_servicio_index['costo_neto_descuento'],
                                                                    'descuento_b'               => 1,
                                                                    'plan_b'                    => 0,
                                                                    'facturable_b'              => $articulo_servicio_index['facturable_b'],
                                                                ]);
                                                            } else {
                                                                /**fueron puros precios sin descuento */
                                                                if ($articulo_servicio_index['facturable_b'] == 1) {
                                                                    /**se desglosa el IVA */
                                                                    $subtotal += (($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                    $impuestos += ((($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                } else {
                                                                    /**no grava IVA */
                                                                    $subtotal += (($articulo_servicio_index['costo_neto_normal']) * $articulo_servicio_index['cantidad']);
                                                                }
                                                                //sumando el total
                                                                $total += $articulo_servicio_index['costo_neto_normal'] * $articulo_servicio_index['cantidad'];
                                                                array_push($detalle_venta, [
                                                                    'cantidad'                  => $articulo_servicio_index['cantidad'],
                                                                    'lotes_id'                  => $articulo_servicio_index['lote'],
                                                                    'movimientos_inventario_id' => $id_movimiento_inventario,
                                                                    'articulos_id'              => $articulo_servicio_index['id'],
                                                                    'costo_neto_normal'         => $articulo_servicio_index['costo_neto_normal'],
                                                                    'costo_neto_descuento'      => 0,
                                                                    'descuento_b'               => 0,
                                                                    'plan_b'                    => 0,
                                                                    'facturable_b'              => $articulo_servicio_index['facturable_b'],
                                                                ]);
                                                            }
                                                        }
                                                    } else {
                                                        return $this->errorResponse('Seleccione un plan funerario para aplicar los descuentos', 409);
                                                    }
                                                } else {
                                                    /**no llevaba plan funerario a futuro */
                                                    if ($request->plan_funerario_inmediato_b['value'] == 1) {
                                                        /**checando que exista el id de un plan funerario de uso a futuro */
                                                        if (trim($request->plan_funerario['value']) == '') {
                                                            /**no se tiene seleccionado un plan de uso inmediato */
                                                            return $this->errorResponse('Seleccione un plan funerario para aplicar los descuentos', 409);
                                                        }
                                                    }
                                                    /**no es parte del plan funerario */
                                                    if ($articulo_servicio_index['descuento_b'] == 1) {
                                                        //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                                        if ($articulo_servicio_index['costo_neto_normal'] >= $articulo_servicio_index['costo_neto_descuento']) {
                                                            /**si se puede aplicar descuento */
                                                            if ($articulo_servicio_index['facturable_b'] == 1) {
                                                                /**se desglosa el IVA */
                                                                $subtotal += (($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                $impuestos += ((($articulo_servicio_index['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                                $descuento += ((($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) - ($articulo_servicio_index['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100)))) * $articulo_servicio_index['cantidad']);
                                                            } else {
                                                                /**no grava IVA */
                                                                $subtotal += (($articulo_servicio_index['costo_neto_descuento']) * $articulo_servicio_index['cantidad']);
                                                                $descuento += ((($articulo_servicio_index['costo_neto_normal']) - ($articulo_servicio_index['costo_neto_descuento'])) * $articulo_servicio_index['cantidad']);
                                                            }
                                                            //sumando el total
                                                            $total += $articulo_servicio_index['costo_neto_descuento'] * $articulo_servicio_index['cantidad'];
                                                        } else {
                                                            /**no se puede proceder por que el precio de descuento no es correcto */
                                                            return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                                        }
                                                        /**el registro con descuento_b */
                                                        /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                                        array_push($detalle_venta, [
                                                            'cantidad'                  => $articulo_servicio_index['cantidad'],
                                                            'lotes_id'                  => $articulo_servicio_index['lote'],
                                                            'movimientos_inventario_id' => $id_movimiento_inventario,
                                                            'articulos_id'              => $articulo_servicio_index['id'],
                                                            'costo_neto_normal'         => $articulo_servicio_index['costo_neto_normal'],
                                                            'costo_neto_descuento'      => $articulo_servicio_index['costo_neto_descuento'],
                                                            'descuento_b'               => 1,
                                                            'plan_b'                    => $articulo_servicio_index['plan_b'],
                                                            'facturable_b'              => $articulo_servicio_index['facturable_b'],
                                                        ]);
                                                    } else {
                                                        /**fueron puros precios sin descuento */
                                                        if ($articulo_servicio_index['facturable_b'] == 1) {
                                                            /**se desglosa el IVA */
                                                            $subtotal += (($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                            $impuestos += ((($articulo_servicio_index['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio_index['cantidad']);
                                                        } else {
                                                            /**no grava IVA */
                                                            $subtotal += (($articulo_servicio_index['costo_neto_normal']) * $articulo_servicio_index['cantidad']);
                                                        }
                                                        //sumando el total
                                                        $total += $articulo_servicio_index['costo_neto_normal'] * $articulo_servicio_index['cantidad'];
                                                        array_push($detalle_venta, [
                                                            'cantidad'                  => $articulo_servicio_index['cantidad'],
                                                            'lotes_id'                  => $articulo_servicio_index['lote'],
                                                            'movimientos_inventario_id' => $id_movimiento_inventario,
                                                            'articulos_id'              => $articulo_servicio_index['id'],
                                                            'costo_neto_normal'         => $articulo_servicio_index['costo_neto_normal'],
                                                            'costo_neto_descuento'      => 0,
                                                            'descuento_b'               => 0,
                                                            'plan_b'                    => $articulo_servicio_index['plan_b'],
                                                            'facturable_b'              => $articulo_servicio_index['facturable_b'],
                                                        ]);
                                                    }
                                                }
                                            }
                                        }

                                        $existencia_inventario_tomando_en_cuenta_el_contrato = 0;
                                        /**verificando si el lote tiene suficiente existencia tomando en cuenta si el contrato ya tiene o no asignado articulos */
                                        if ($tenia_articulos) {
                                            /**se recorre el arreglo de articulos y servicios para sacar la suma de articulos que ya tenia asigando */
                                            foreach ($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'] as $articulo_contrato) {
                                                if ($articulo_contrato['articulos_id'] == $lote['articulos_id'] && $articulo_contrato['lotes_id'] == $lote['lotes_id']) {
                                                    //se encontro el articulo que ya esta registrada en la venta
                                                    /**se debe sumar a la cantidad que ya esta asignado */
                                                    $existencia_inventario_tomando_en_cuenta_el_contrato += $articulo_contrato['cantidad'];
                                                }
                                            }
                                        }
                                        /**se suma la cantidad que esta en el inventario */
                                        $existencia_inventario_tomando_en_cuenta_el_contrato += $lote['existencia'];

                                        if ($existencia_inventario_tomando_en_cuenta_el_contrato < $cantidad_lote_solicitado) {
                                            return $this->errorResponse('No se tiene suficiente cantidad del artículo ' . $articulo_servicio['descripcion'] . ' en el lote ' . $lote['lotes_id'], 409);
                                        } else {
                                            /**aqui comienzo a agregar el detalle de como quedara actualizado la parte del inventario
                                             * con la actualizacion de la nueva demanda de
                                             * articulos
                                             */
                                            array_push($detalle_inventario, [
                                                'lotes_id'     => $lote['lotes_id'],
                                                'articulos_id' => $lote['articulos_id'],
                                                'existencia'   => $existencia_inventario_tomando_en_cuenta_el_contrato - $cantidad_lote_solicitado,
                                            ]);
                                        }
                                        break;
                                    } //fin if existe lote
                                }
                                /**en
                                 * caso de
                                 * que no tenga
                                 * lote
                                 */
                                if ($existe_lote == false) {
                                    /**al no ser encontrado el lote en la bd, el sistema no puede proceder */
                                    return $this->errorResponse('No se encontró el lote ' . $articulo_servicio['lote'], 409);
                                }
                                //return $this->errorResponse('si existia', 409);
                                /**termina existia   if ($operacion_existia) {*/
                            } else {
                                /**
                                 * hasta
                                 * aqui
                                 * no
                                 * importa
                                 * el codigo
                                 * porque no aplica lotes
                                 */
                                /**es de tipo servicio y pasa directo sin tener en cuenta lote ni caducidad */
                                /**verificando los costos para saber si aplicar costo de plan funeario*/
                                if ($request->plan_funerario_futuro_b['value'] == 1) {
                                    /**maneja plan funerario de uso a futuro */
                                    /**checando que exista el id de un plan funerario de uso a futuro */
                                    if (trim($request->id_convenio_plan) != '') {
                                        /**si se capturo el id del plan funerario a futuro vendido */
                                        if ($articulo_servicio['plan_b'] == 1) {
                                            /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                            array_push($detalle_venta, [
                                                'cantidad'                  => $articulo_servicio['cantidad'],
                                                'lotes_id'                  => null,
                                                'movimientos_inventario_id' => $id_movimiento_inventario,
                                                'articulos_id'              => $articulo_servicio['id'],
                                                'costo_neto_normal'         => 0,
                                                'costo_neto_descuento'      => 0,
                                                'descuento_b'               => 0,
                                                'plan_b'                    => 1,
                                                'facturable_b'              => $articulo_servicio['facturable_b'],
                                            ]);
                                        } else {
                                            /**no es parte del plan funerario */
                                            if ($articulo_servicio['descuento_b'] == 1) {
                                                //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                                if ($articulo_servicio['costo_neto_normal'] >= $articulo_servicio['costo_neto_descuento']) {
                                                    /**si se puede aplicar descuento */
                                                    if ($articulo_servicio['facturable_b'] == 1) {
                                                        /**se desglosa el IVA */
                                                        $subtotal += (($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                        $impuestos += ((($articulo_servicio['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                        $descuento += ((($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) - ($articulo_servicio['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100)))) * $articulo_servicio['cantidad']);
                                                    } else {
                                                        /**no grava IVA */
                                                        $subtotal += (($articulo_servicio['costo_neto_descuento']) * $articulo_servicio['cantidad']);
                                                        $descuento += ((($articulo_servicio['costo_neto_normal']) - ($articulo_servicio['costo_neto_descuento'])) * $articulo_servicio['cantidad']);
                                                    }
                                                    //sumando el total
                                                    $total += $articulo_servicio['costo_neto_descuento'] * $articulo_servicio['cantidad'];
                                                } else {
                                                    /**no se puede proceder por que el precio de descuento no es correcto */
                                                    return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                                }
                                                /**el registro con descuento_b */
                                                /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                                array_push($detalle_venta, [
                                                    'cantidad'                  => $articulo_servicio['cantidad'],
                                                    'lotes_id'                  => null,
                                                    'movimientos_inventario_id' => $id_movimiento_inventario,
                                                    'articulos_id'              => $articulo_servicio['id'],
                                                    'costo_neto_normal'         => $articulo_servicio['costo_neto_normal'],
                                                    'costo_neto_descuento'      => $articulo_servicio['costo_neto_descuento'],
                                                    'descuento_b'               => 1,
                                                    'plan_b'                    => 0,
                                                    'facturable_b'              => $articulo_servicio['facturable_b'],
                                                ]);
                                            } else {
                                                /**fueron puros precios sin descuento */
                                                if ($articulo_servicio['facturable_b'] == 1) {
                                                    /**se desglosa el IVA */
                                                    $subtotal += (($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                    $impuestos += ((($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                } else {
                                                    /**no grava IVA */
                                                    $subtotal += (($articulo_servicio['costo_neto_normal']) * $articulo_servicio['cantidad']);
                                                }
                                                //sumando el total
                                                $total += $articulo_servicio['costo_neto_normal'] * $articulo_servicio['cantidad'];
                                                array_push($detalle_venta, [
                                                    'cantidad'                  => $articulo_servicio['cantidad'],
                                                    'lotes_id'                  => null,
                                                    'movimientos_inventario_id' => $id_movimiento_inventario,
                                                    'articulos_id'              => $articulo_servicio['id'],
                                                    'costo_neto_normal'         => $articulo_servicio['costo_neto_normal'],
                                                    'costo_neto_descuento'      => 0,
                                                    'descuento_b'               => 0,
                                                    'plan_b'                    => 0,
                                                    'facturable_b'              => $articulo_servicio['facturable_b'],
                                                ]);
                                            }
                                        }
                                    } else {
                                        return $this->errorResponse('Seleccione un plan funerario para aplicar los descuentos', 409);
                                    }
                                } else {
                                    /**no llevaba plan funerario a futuro */
                                    if ($request->plan_funerario_inmediato_b['value'] == 1) {
                                        /**checando que exista el id de un plan funerario de uso a futuro */
                                        if (trim($request->plan_funerario['value']) == '') {
                                            /**no se tiene seleccionado un plan de uso inmediato */
                                            return $this->errorResponse('Seleccione un plan funerario para aplicar los descuentos', 409);
                                        }
                                    }
                                    /**no es parte del plan funerario */
                                    if ($articulo_servicio['descuento_b'] == 1) {
                                        //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                        if ($articulo_servicio['costo_neto_normal'] >= $articulo_servicio['costo_neto_descuento']) {
                                            /**si se puede aplicar descuento */
                                            if ($articulo_servicio['facturable_b'] == 1) {
                                                /**se desglosa el IVA */
                                                $subtotal += (($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                $impuestos += ((($articulo_servicio['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                                $descuento += ((($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) - ($articulo_servicio['costo_neto_descuento'] / (1 + ($request->tasa_iva / 100)))) * $articulo_servicio['cantidad']);
                                            } else {
                                                /**no grava IVA */
                                                $subtotal += (($articulo_servicio['costo_neto_descuento']) * $articulo_servicio['cantidad']);
                                                $descuento += (($articulo_servicio['costo_neto_normal'] - $articulo_servicio['costo_neto_descuento']) * $articulo_servicio['cantidad']);
                                            }
                                            //sumando el total
                                            $total += $articulo_servicio['costo_neto_descuento'] * $articulo_servicio['cantidad'];
                                        } else {
                                            /**no se puede proceder por que el precio de descuento no es correcto */
                                            return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                        }
                                        /**el registro con descuento_b */
                                        /**lleva descuento, por lo tanto el costo_neto_normal es 0 y lo demas queda sin ser tomando en cuenta ... no causa ningun iva ni descuentos*/
                                        array_push($detalle_venta, [
                                            'cantidad'                  => $articulo_servicio['cantidad'],
                                            'lotes_id'                  => null,
                                            'movimientos_inventario_id' => $id_movimiento_inventario,
                                            'articulos_id'              => $articulo_servicio['id'],
                                            'costo_neto_normal'         => $articulo_servicio['costo_neto_normal'],
                                            'costo_neto_descuento'      => $articulo_servicio['costo_neto_descuento'],
                                            'descuento_b'               => 1,
                                            'plan_b'                    => $articulo_servicio['plan_b'],
                                            'facturable_b'              => $articulo_servicio['facturable_b'],
                                        ]);
                                    } else {
                                        /**fueron puros precios sin descuento */
                                        if ($articulo_servicio['facturable_b'] == 1) {
                                            /**se desglosa el IVA */
                                            $subtotal += (($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                            $impuestos += ((($articulo_servicio['costo_neto_normal'] / (1 + ($request->tasa_iva / 100))) * (($request->tasa_iva / 100))) * $articulo_servicio['cantidad']);
                                        } else {
                                            /**no grava IVA */
                                            $subtotal += (($articulo_servicio['costo_neto_normal']) * $articulo_servicio['cantidad']);
                                        }
                                        //sumando el total
                                        $total += $articulo_servicio['costo_neto_normal'] * $articulo_servicio['cantidad'];
                                        array_push($detalle_venta, [
                                            'cantidad'                  => $articulo_servicio['cantidad'],
                                            'lotes_id'                  => null,
                                            'movimientos_inventario_id' => $id_movimiento_inventario,
                                            'articulos_id'              => $articulo_servicio['id'],
                                            'costo_neto_normal'         => $articulo_servicio['costo_neto_normal'],
                                            'costo_neto_descuento'      => 0,
                                            'descuento_b'               => 0,
                                            'plan_b'                    => $articulo_servicio['plan_b'],
                                            'facturable_b'              => $articulo_servicio['facturable_b'],
                                        ]);
                                    }
                                }
                            }
                        } else {
                            return $this->errorResponse('Revise que los artículos están debidamente habilitados.', 409);
                        }
                        break; //break por que se encontró el articulo en el inventario
                    }
                }
                if ($articulo_encontrado == false) {
                    /**al no ser encontrado el articulo en la bd, el sistema no puede proceder */
                    return $this->errorResponse('No se encontró el articulo ' . $articulo_servicio['descripcion'], 409);
                }
            } //fin foreach articulos servicios

            /**buscamos los articulos que ya estan en el contrato pero que se quitaron y se deben devolver al inventario */
            if (isset($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'])) {
                /**la operacion ya tenia articulos y servicios agregados y se deben de revisar para ver cuales
                 * se quitaron y se deben regresar al inventario
                 */

                $index = [];
                foreach ($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'] as $index_contrato => $articulo_contrato) {
                    if (in_array($index_contrato, $index)) {
                        continue;
                    }
                    /**se revisa cual articulo ya no fue incluido en la nueva peticion y se debe de aumentar esa existencia en el inventario */
                    $esta = false;
                    foreach ($request->articulos_servicios as $index_articulo_servicio => $articulo_servicio) {
                        if ($articulo_servicio['lote'] == $articulo_contrato['lotes_id'] && $articulo_servicio['id'] == $articulo_contrato['articulos_id']) {
                            $esta = true;
                            break;
                        }
                    }

                    //si no esta se aumenta al inventario
                    $suma_quitado = 0;
                    if (!$esta) {
                        /**hago la suma total del material que quitaron para aumentarlo */

                        foreach ($datos_solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'] as $index_sumar => $articulo_contrato_sumar) {
                            if ($articulo_contrato['lotes_id'] == $articulo_contrato_sumar['lotes_id'] && $articulo_contrato['articulos_id'] == $articulo_contrato_sumar['articulos_id']) {
                                array_push($index, $index_sumar);
                                $suma_quitado += $articulo_contrato_sumar['cantidad'];
                            }
                        }
                        $esta_row = false;
                        foreach ($detalle_inventario as $index_detalle => &$detalle) {
                            if ($detalle['lotes_id'] == $articulo_contrato['lotes_id'] && $detalle['articulos_id'] == $articulo_contrato['articulos_id']) {
                                $esta_row = true;
                                $detalle['existencia'] += $suma_quitado;
                            }
                        }
                        if ($esta_row == false) {
                            foreach ($inventario as $articulo) {
                                foreach ($articulo['inventario'] as &$lote) {
                                    if ($lote['lotes_id'] == $articulo_contrato['lotes_id'] && $lote['articulos_id'] == $articulo_contrato['articulos_id']) {
                                        $lote['existencia'] += $suma_quitado;
                                        array_push($detalle_inventario, [
                                            'lotes_id'     => $articulo_contrato['lotes_id'],
                                            'articulos_id' => $articulo_contrato['articulos_id'],
                                            'existencia'   => $lote['existencia'],
                                        ]);
                                    }
                                }
                            }
                        }
                    } //fin de si no esta el articulo ya requerido para el servicio
                }
            } //fin if isset articulos en el contrato

            /**actualizo el inventario */
            //return $this->errorResponse($detalle_inventario, 409);
            foreach ($detalle_inventario as $index_detalle => $detalle) {
                DB::table('inventario')
                    ->where('articulos_id', '=', $detalle['articulos_id'])
                    ->where('lotes_id', '=', $detalle['lotes_id'])->update(
                    [
                        'existencia' => $detalle['existencia'],
                    ]
                );
            }

            /**eliminando los articulos y servicios anteriores */
            DB::table('venta_detalle')->where('movimientos_inventario_id', '=', $id_movimiento_inventario)->delete();
            /**guardando os articulos y servicios nuevos */
            foreach ($detalle_venta as $index_detalle => $detalle) {
                DB::table('venta_detalle')->insert(
                    [
                        'cantidad'                  => $detalle['cantidad'],
                        'lotes_id'                  => $detalle['lotes_id'],
                        'movimientos_inventario_id' => $detalle['movimientos_inventario_id'],
                        'articulos_id'              => $detalle['articulos_id'],
                        'costo_neto_normal'         => $detalle['costo_neto_normal'],
                        'costo_neto_descuento'      => $detalle['costo_neto_descuento'],
                        'descuento_b'               => $detalle['descuento_b'],
                        'plan_b'                    => $detalle['plan_b'],
                        'facturable_b'              => $detalle['facturable_b'],
                    ]
                );
            }

            /**actualizando totales de la operacion */
            DB::table('operaciones')->where('servicios_funerarios_id', $request->id_servicio)->update(
                [
                    'clientes_id'     => $request->id_cliente,
                    'fecha_operacion' => $request->fechahora_contrato,
                    'subtotal'        => $subtotal,
                    'descuento'       => $descuento,
                    'impuestos'       => $impuestos,
                    'total'           => $total,
                    'tasa_iva'        => $request->tasa_iva,
                ]
            );

            /**actualizacion de la programacion de pagos */
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fechahora_contrato)))->add(0, 'day');

            if ($datos_solicitud['operacion'] == null && !isset($datos_solicitud['operacion']['pagos_programados'])) {

                /**si no existe lo vamos a crear */
                /**se registra la referencia para los pagos */
                $id_pago_programado_unico = DB::table('pagos_programados')->insertGetId(
                    [
                        /**utilizo la referencia de pago 004 para servicios funerarios */
                        'num_pago'           => 1, //numero 1, pues es unico
                        'referencia_pago'    => '004' . date('Ymd', strtotime($request->fechahora_contrato)) . '01' . $request->id_servicio, //se crea una referencia para saber a que pago pertenece
                        'fecha_programada'   => $fecha_maxima, //fecha de la venta
                        'conceptos_pagos_id' => 3, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                        'monto_programado'   => $total,
                        'operaciones_id'     => $id_operacion,
                        'status'             => 1,
                    ]
                );
            } else {
                if ($datos_solicitud['operacion']['total_cubierto'] <= $total) {
                    DB::table('pagos_programados')->where('operaciones_id', '=', $id_operacion)->update(
                        [
                            /**utilizo la referencia de pago 004 para servicios funerarios */
                            //'num_pago' => 1, //numero 1, pues es unico
                            //'referencia_pago' => '004' . date('Ymd', strtotime($request->fechahora_contrato)) . '01' . $request->id_servicio, //se crea una referencia para saber a que pago pertenece
                            'fecha_programada' => $fecha_maxima, //fecha de la venta
                            //'conceptos_pagos_id' => 3, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                            'monto_programado' => $total,
                            //'operaciones_id' => $id_operacion,
                            'status'           => 1,
                        ]
                    );
                } else {
                    return $this->errorResponse('Error, Este contrato tiene pagado $' . number_format($datos_solicitud['operacion']['total_cubierto'], 2) . '.', 409);
                }
            }

            /* $datos['subtotal'] = $subtotal;
            $datos['descuento'] = $descuento;
            $datos['impuestos'] = $impuestos;
            $datos['total'] = $total;
            return $this->errorResponse($datos, 409);
             */

            $id_return = $request->id_servicio;
            DB::commit();
            return $id_return;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    /**obtiene los servicios funerarios */
    public function get_solicitudes_servicios(Request $request, $id_servicio = 'all', $paginated = false, $uso_plan_funerario_futuro = 0, $uso_terreno_id = 0)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $fallecido                = $request->fallecido;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;
        $fecha_operacion          = $request->fecha_operacion;
        $resultado_query          = ServiciosFunerarios::select(
            'id',
            'registro_contrato_id',
            'nota_servicio',
            'titulos_id',
            'embalsamar_b',
            'velacion_b',
            'cremacion_b',
            'inhumacion_b',
            'traslado_b',
            'misa_b',
            'aseguradora_b',
            'custodia_b',
            'material_velacion_b',
            'acta_b',
            'planes_funerarios_id',
            'plan_funerario_futuro_b',
            'plan_funerario_inmediato_b',
            /**venta operacion */
            'servicios_funerarios.id as servicio_id',
            'llamada_b',
            'nota_al_recoger',
            'nombre_afectado',
            'causa_muerte',
            'muerte_natural_b',
            'contagioso_b',
            'nombre_informante',
            'telefono_informante',
            'parentesco_informante',
            'ubicacion_recoger',
            'servicios_funerarios.status as status_b',
            'tipo_solicitud_id',
            'recogio_id',
            'fechahora_solicitud as fechahora_solicitud',
            'registro_id',
            'fecha_nacimiento',
            'generos_id',
            'fechahora_cremacion',
            'fechahora_entrega_cenizas',
            'descripcion_urna',
            'fechahora_traslado',
            'destino_traslado',
            'fechahora_misa',
            'iglesia_misa',
            'direccion_iglesia',
            'lugares_servicios_id',
            'direccion_velacion',
            'cementerios_servicio_id',
            'fechahora_inhumacion',
            'ventas_terrenos_id',
            'nota_ubicacion',
            'numero_convenio_aseguradora',
            'aseguradora',
            'telefono_aseguradora',
            'responsable_custodia',
            'folio_custodia',
            'folio_liberacion',
            'folio_acta',
            'fechahora_acta',
            'fechahora_contrato',
            'parentesco_contratante',
            'ventas_planes_id',
            DB::raw(
                '(NULL) as genero_texto'
            ),
            DB::raw(
                '(NULL) as fecha_nacimiento_texto'
            ),
            DB::raw(
                '(NULL) as llamada_texto'
            ),
            DB::raw(
                'DATE(fechahora_solicitud) as fecha_solicitud'
            ),
            DB::raw(
                'TIME(fechahora_solicitud) as hora_solicitud'
            ),
            DB::raw(
                'DATE(fechahora_defuncion) as fecha_muerte'
            ),
            DB::raw(
                'TIME(fechahora_defuncion) as hora_muerte'
            ),
            DB::raw(
                '(NULL) as fecha_muerte_texto'
            ),
            DB::raw(
                '(NULL) as fecha_solicitud_texto'
            ),
            DB::raw(
                'DATE(fechahora_cremacion) as fecha_cremacion'
            ),
            DB::raw(
                'TIME(fechahora_cremacion) as hora_cremacion'
            ),
            DB::raw(
                'DATE(fechahora_entrega_cenizas) as fecha_entrega_cenizas'
            ),
            DB::raw(
                'TIME(fechahora_entrega_cenizas) as hora_entrega_cenizas'
            ),
            DB::raw(
                'DATE(fechahora_traslado) as fecha_traslado'
            ),
            DB::raw(
                'TIME(fechahora_traslado) as hora_traslado'
            ),
            DB::raw(
                'DATE(fechahora_misa) as fecha_misa'
            ),
            DB::raw(
                'TIME(fechahora_misa) as hora_misa'
            ),
            DB::raw(
                'DATE(fechahora_inhumacion) as fecha_inhumacion'
            ),
            DB::raw(
                'TIME(fechahora_inhumacion) as hora_inhumacion'
            ),
            DB::raw(
                'DATE(fechahora_acta) as fecha_acta'
            ),
            DB::raw(
                'DATE(fechahora_contrato) as fecha_contrato'
            ),
            DB::raw(
                'TIME(fechahora_contrato) as hora_contrato'
            ),
            DB::raw(
                '(NULL) as muerte_natural_texto'
            ),
            DB::raw(
                '(NULL) as contagioso_texto'
            ),
            DB::raw(
                '(NULL) as status_texto'
            ),
            DB::raw(
                '(NULL) as tipo_solicitud_texto'
            ),
            DB::raw(
                '(NULL) as atencion_medica_texto'
            ),
            DB::raw(
                '(NULL) as plan_funerario_futuro'
            ),
            DB::raw(
                '(NULL) as plan_funerario_secciones_originales'
            ),
            DB::raw(
                '(NULL) as nombre_titular_plan_funerario_futuro'
            ),
            'plan_funerario_original',
            'costo_plan_original',
            'parentesco_contratante',
            'nacionalidades_id',
            'estados_civiles_id',
            'direccion_fallecido',
            'escolaridades_id',
            'ocupacion',
            'lugar_nacimiento',
            'fechahora_defuncion',
            'atencion_medica_b',
            'enfermedades_padecidas',
            'certificado_informante',
            'certificado_informante_telefono',
            'certificado_informante_parentesco',
            'folio_certificado',
            'medico_legista',
            'sitios_muerte_id',
            'lugar_muerte',
            'afiliaciones_id',
            'afiliacion_nota',
            'estado_afectado_id',
            'medico_responsable_embalsamado',
            'preparador',
            'tipos_contratante_id'
        )
            ->with('registro:id,nombre')
            ->with('nacionalidad')
            ->with('escolaridad')
            ->with('recogio:id,nombre')
            ->with('estado_civil')
            ->with('terreno')
            ->with('titulo')
            ->with('materialrentado')
            ->with('operacion.movimientoinventario.articulosserviciofunerario')
            ->with('operacion.pagosProgramados.pagados')
            ->with('operacion.cliente')
            ->with('operacion.cancelador')
        /**validnado si se hace filtrado de algun plan funerario de uso inmediato */
            ->where(function ($q) use ($uso_plan_funerario_futuro) {
                if (trim($uso_plan_funerario_futuro) != '' && $uso_plan_funerario_futuro > 0) {
                    $q->where('servicios_funerarios.ventas_planes_id', '=', $uso_plan_funerario_futuro);
                }
            })
            ->where(function ($q) use ($uso_terreno_id) {
                if (trim($uso_terreno_id) != '' && $uso_terreno_id > 0) {
                    $q->where('servicios_funerarios.ventas_terrenos_id', '=', $uso_terreno_id);
                }
            })

            ->where(function ($q) use ($id_servicio) {
                if (trim($id_servicio) == 'all' || $id_servicio > 0) {
                    if (trim($id_servicio) == 'all') {
                        $q->where('servicios_funerarios.id', '>', $id_servicio);
                    } else if ($id_servicio > 0) {
                        $q->where('servicios_funerarios.id', '=', $id_servicio);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('servicios_funerarios.id', '=', $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('servicios_funerarios.status', '=', $status);
                }
            })
        //->join('operaciones', 'operaciones.servicios_funerarios_id', '=', 'servicios_funerarios.id')
        //->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->where('nombre_afectado', 'like', '%' . $fallecido . '%')
            ->orderBy('servicios_funerarios.id', 'desc')
            ->get();
        /**verificando si el usario necesita el resultado paginado, todo o por id */
        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado       = &$resultado_query;
        }

        /**traigo el inventario para llenar los datos de los conceptos del contrato */
        $articulos = Articulos::with('categoria')->with('tipo_articulo')->get();

        foreach ($resultado as $index_venta => &$solicitud) {

            if (isset($solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'])) {
                /**actualizo los datos del arreglo de articulos */
                foreach ($solicitud['operacion']['movimientoinventario']['articulosserviciofunerario'] as $index_articulo => &$articulo) {
                    foreach ($articulos as $index_inventario => $inventario) {
                        if ($articulo['articulos_id'] == $inventario['id']) {
                            $articulo['descripcion']   = $inventario['descripcion'];
                            $articulo['categoria']     = $inventario['categoria']['categoria'];
                            $articulo['tipo']          = $inventario['tipo_articulo']['tipo'];
                            $articulo['codigo_barras'] = $inventario['codigo_barras'];
                            /**importa para cuando es con plan funerario a futuro  */
                            if ($solicitud['plan_funerario_futuro_b'] == 1 && $solicitud['ventas_planes_id'] > 0) {
                                if ($articulo['plan_b'] == 1) {
                                    $articulo['subtotal']   = 0;
                                    $articulo['descuento']  = 0;
                                    $articulo['impuestos']  = 0;
                                    $articulo['costo_neto'] = 0;
                                    $articulo['importe']    = 0;
                                } else {
                                    if ($articulo['descuento_b'] == 1) {
                                        //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                        if ($articulo['costo_neto_normal'] >= $articulo['costo_neto_descuento']) {
                                            /**si se puede aplicar descuento */
                                            if ($articulo['facturable_b'] == 1) {

                                                /**se desglosa el IVA */
                                                $articulo['subtotal']   = round((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                                $articulo['impuestos']  = round(((($articulo['costo_neto_descuento'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) * (($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                                $articulo['descuento']  = round(((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) - ($articulo['costo_neto_descuento'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))))), 2, PHP_ROUND_HALF_UP);
                                                $articulo['costo_neto'] = $articulo['costo_neto_descuento'];
                                                $articulo['importe']    = round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                            } else {
                                                /**no grava IVA */
                                                $articulo['subtotal']   = $articulo['costo_neto_normal'];
                                                $articulo['impuestos']  = 0;
                                                $articulo['descuento']  = $articulo['costo_neto_normal'] - $articulo['costo_neto_descuento'];
                                                $articulo['costo_neto'] = $articulo['costo_neto_descuento'];
                                                $articulo['importe']    = $articulo['costo_neto'] * $articulo['cantidad'];
                                            }
                                        } else {
                                            /**no se puede proceder por que el precio de descuento no es correcto */
                                            return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                        }
                                    } else {
                                        /**fueron puros precios sin descuento */
                                        if ($articulo['facturable_b'] == 1) {
                                            $articulo['subtotal']   = round((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                            $articulo['impuestos']  = round(((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) * (($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                            $articulo['descuento']  = 0;
                                            $articulo['costo_neto'] = $articulo['costo_neto_normal'];
                                            $articulo['importe']    = round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                        } else {
                                            /**no grava IVA */
                                            $articulo['subtotal']   = $articulo['costo_neto_normal'];
                                            $articulo['impuestos']  = 0;
                                            $articulo['descuento']  = 0;
                                            $articulo['costo_neto'] = $articulo['costo_neto_normal'];
                                            $articulo['importe']    =
                                                round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                        }
                                    }
                                }
                            } else {
                                /**es con plan funerario de uso inmediato o sin plan */
                                if ($articulo['descuento_b'] == 1) {
                                    //se toma el precio de descuento, verificnado que el precio de descuento es menor o igual al precio de costo neto real
                                    if ($articulo['costo_neto_normal'] >= $articulo['costo_neto_descuento']) {
                                        /**si se puede aplicar descuento */
                                        if ($articulo['facturable_b'] == 1) {

                                            /**se desglosa el IVA */
                                            $articulo['subtotal']   = round((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                            $articulo['impuestos']  = round(((($articulo['costo_neto_descuento'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) * (($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                            $articulo['descuento']  = round(((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) - ($articulo['costo_neto_descuento'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))))), 2, PHP_ROUND_HALF_UP);
                                            $articulo['costo_neto'] = $articulo['costo_neto_descuento'];
                                            $articulo['importe']    = round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                        } else {
                                            /**no grava IVA */
                                            $articulo['subtotal']   = $articulo['costo_neto_normal'];
                                            $articulo['impuestos']  = 0;
                                            $articulo['descuento']  = $articulo['costo_neto_normal'] - $articulo['costo_neto_descuento'];
                                            $articulo['costo_neto'] = $articulo['costo_neto_descuento'];
                                            $articulo['importe']    = $articulo['costo_neto'] * $articulo['cantidad'];
                                        }
                                    } else {
                                        /**no se puede proceder por que el precio de descuento no es correcto */
                                        return $this->errorResponse('Verifique que el costo de descuento es menor que el precio normal', 409);
                                    }
                                } else {
                                    /**fueron puros precios sin descuento */
                                    if ($articulo['facturable_b'] == 1) {
                                        $articulo['subtotal']   = round((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                        $articulo['impuestos']  = round(((($articulo['costo_neto_normal'] / (1 + ($solicitud['operacion']['tasa_iva'] / 100))) * (($solicitud['operacion']['tasa_iva'] / 100)))), 2, PHP_ROUND_HALF_UP);
                                        $articulo['descuento']  = 0;
                                        $articulo['costo_neto'] = $articulo['costo_neto_normal'];
                                        $articulo['importe']    = round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                    } else {
                                        /**no grava IVA */
                                        $articulo['subtotal']   = $articulo['costo_neto_normal'];
                                        $articulo['impuestos']  = 0;
                                        $articulo['descuento']  = 0;
                                        $articulo['costo_neto'] = $articulo['costo_neto_normal'];
                                        $articulo['importe']    =
                                            round($articulo['costo_neto'] * $articulo['cantidad'], 2, PHP_ROUND_HALF_UP);
                                    }
                                }
                            }

                            if ($inventario['tipo_articulos_id'] == 2) {
                                $articulo['lotes_id'] = 'N/A';
                            }
                        }
                    }
                }
            }

            /**definiendo si fue por llamada la solicitud */

            if ($solicitud['llamada_b'] == 1) {
                $solicitud['llamada_texto'] = 'Llamada telefónica';
            } else {
                $solicitud['llamada_texto'] = 'Solicitud en Sucursal';
            }

            /**tipo de solicitud */
            if ($solicitud['tipo_solicitud_id'] == 1) {
                $solicitud['tipo_solicitud_texto'] = 'Servicio Funerario';
            } elseif ($solicitud['tipo_solicitud_id'] == 2) {
                $solicitud['tipo_solicitud_texto'] = 'Exhumación';
            }
            if ($solicitud['contagioso_b'] == 0) {
                $solicitud['contagioso_texto'] = 'NO';
                /**actualizando el motivo de cancelacion */
            } elseif ($solicitud['contagioso_b'] == 1) {
                $solicitud['contagioso_texto'] = 'SI';
            }
            if ($solicitud['muerte_natural_b'] == 0) {
                $solicitud['muerte_natural_texto'] = 'NO';
                /**actualizando el motivo de cancelacion */
            } elseif ($solicitud['muerte_natural_b'] == 1) {
                $solicitud['muerte_natural_texto'] = 'SI';
            }
            $solicitud['fecha_solicitud_texto']  = fecha_abr($solicitud['fecha_solicitud']);
            $solicitud['fecha_nacimiento_texto'] = fecha_abr($solicitud['fecha_nacimiento']);
            if ($solicitud['generos_id'] == 1) {
                $solicitud['genero_texto'] = 'HOMBRE';
            } elseif ($solicitud['generos_id'] == 2) {
                $solicitud['genero_texto'] = 'MUJER';
            }
            $solicitud['fecha_muerte_texto'] = fechahora($solicitud['fechahora_defuncion']);

            if ($solicitud['atencion_medica_b'] == 0) {
                $solicitud['atencion_medica_texto'] = 'NO';
            } elseif ($solicitud['atencion_medica_b'] == 1) {
                $solicitud['atencion_medica_texto'] = 'SI';
            }

            /**agregando la ubicacion del servicio cuando es en el cementerio de la empresa, id 1(cementerio aeternus) */
            $cementerio_controller = new CementerioController();
            $datos_cementerio      = $cementerio_controller->get_cementerio();
            /**verificando que tipo de operacion_empresa es */
            if ($solicitud['inhumacion_b'] == 1 && $solicitud['cementerios_servicio_id'] == 1) {
                if (!is_null($solicitud['terreno'])) {
                    $datos_venta_propiedad                          = $cementerio_controller->get_ventas($request, $solicitud['terreno']['ventas_terrenos_id'], '')[0];
                    $solicitud['terreno']['status_operacion']       = $datos_venta_propiedad['operacion_status'];
                    $solicitud['terreno']['saldo_neto']             = $datos_venta_propiedad['saldo_neto'];
                    $solicitud['terreno']['status_operacion_texto'] = $datos_venta_propiedad['status_texto'];
                    $solicitud['terreno']['ubicacion_servicio']     = strtoupper($cementerio_controller->ubicacion_texto($solicitud['terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'] . '(' . $datos_venta_propiedad['venta_terreno']['tipo_propiedad']['tipo'] . ' convenio ' . $datos_venta_propiedad['numero_convenio'] . ')');
                }
            }

            /**verificando si la operacion esta lleva anexado un plan funerario de venta a futuro para usar */
            if ($solicitud['plan_funerario_futuro_b'] == 1 && trim($solicitud['ventas_planes_id']) != '') {
                /**cargar los datos de la venta de este plan para mandar al frontend */
                $datos_plan                                           = $this->get_ventas($request, $solicitud['ventas_planes_id'])[0];
                $solicitud['plan_funerario_futuro']                   = strtoupper($datos_plan['venta_plan']['nombre_original'] . '(' . $datos_plan['numero_convenio'] . ')');
                $solicitud['plan_funerario_secciones_originales']     = $datos_plan['venta_plan']['secciones_original'];
                $solicitud['nombre_titular_plan_funerario_futuro']    = $datos_plan['nombre'];
                $solicitud['plan_funerario_futuro_status']            = $datos_plan['operacion_status'];
                $solicitud['plan_funerario_futuro_status_texto']      = $datos_plan['status_texto'];
                $solicitud['plan_funerario_futuro_fecha_venta_texto'] = $datos_plan['fecha_operacion_texto'];
                $solicitud['plan_funerario_futuro_saldo_restante']    = $datos_plan['saldo_neto'];
            } else {
                /**verificnado si tiene un plan de servicios de uso inmediato */
                if ($solicitud['plan_funerario_inmediato_b'] == 1 && trim($solicitud['planes_funerarios_id']) != '') {
                    /**si lo tiene y se debe de cargar la lista de conceptos que tiene ese plan funerario */
                    $conceptos = PlanConceptosServicioOriginal::where('servicios_funerarios_id', $solicitud['id'])->get();
                    /**agregando los conceptos originales del plan */
                    $secciones = [
                        [
                            'seccion'        => 'incluye',
                            'seccion_ingles' => 'include',
                            'conceptos'      => [],
                        ],
                        [
                            'seccion'        => 'inhumacion',
                            'seccion_ingles' => 'inhumation',
                            'conceptos'      => [],
                        ],
                        [
                            'seccion'        => 'cremacion',
                            'seccion_ingles' => 'cremation',
                            'conceptos'      => [],
                        ],
                        [
                            'seccion'        => 'velacion',
                            'seccion_ingles' => 'wakefulness',
                            'conceptos'      => [],
                        ],
                    ];
                    foreach ($conceptos as $key_seccion => $seccion) {
                        /**agregando los conceptos segun su seccion */
                        if ($seccion['seccion_id'] == 1) {
                            /**incluye */
                            array_push(
                                $secciones[0]['conceptos'],
                                [
                                    'concepto'        => $seccion['concepto'],
                                    'concepto_ingles' => $seccion['concepto_ingles'],
                                    'aplicar_en'      => 'plan funerario',
                                    'seccion'         => 'incluye',
                                ]
                            );
                        } elseif ($seccion['seccion_id'] == 2) {
                            /**inhumacion */
                            array_push(
                                $secciones[1]['conceptos'],
                                [
                                    'concepto'        => $seccion['concepto'],
                                    'concepto_ingles' => $seccion['concepto_ingles'],
                                    'aplicar_en'      => 'caso de inhumación',
                                    'seccion'         => 'inhumacion',
                                ]
                            );
                        } elseif ($seccion['seccion_id'] == 3) {
                            /**cremacion */
                            array_push(
                                $secciones[2]['conceptos'],
                                [
                                    'concepto'        => $seccion['concepto'],
                                    'concepto_ingles' => $seccion['concepto_ingles'],
                                    'aplicar_en'      => 'caso de cremación',
                                    'seccion'         => 'cremacion',
                                ]
                            );
                        } elseif ($seccion['seccion_id'] == 4) {
                            /**velacion */
                            array_push(
                                $secciones[3]['conceptos'],
                                [
                                    'concepto'        => $seccion['concepto'],
                                    'concepto_ingles' => $seccion['concepto_ingles'],
                                    'aplicar_en'      => 'caso de velación',
                                    'seccion'         => 'velacion',
                                ]
                            );
                        }
                    }
                    /**push al array padre */
                    $venta['venta_plan']['secciones_original'] = $secciones;

                    $solicitud['plan_funerario_secciones_originales'] = $secciones;
                }
            }

            if (isset($solicitud['operacion'])) {
                $solicitud['operacion']['num_pagos_programados'] = count($solicitud['operacion']['pagos_programados']);
                $num_pagos_programados_vigentes                  = 0;
                if ($solicitud['operacion']['num_pagos_programados'] > 0) {
                    /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                    /**recorriendo arreglo de pagos programados */
                    $pagos_programados_cubiertos = 0;
                    $pagos_vigentes              = 0;
                    $pagos_cancelados            = 0;
                    $pagos_realizados            = 0;

                    $solicitud['operacion']['fecha_operacion_texto'] = fecha_abr($solicitud['operacion']['fecha_operacion']);

                    $arreglo_de_pagos_realizados = [];

                    /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                    foreach ($solicitud['operacion']['pagos_programados'] as $index_programado => &$programado) {
                        /**actualizando el concepto del pago */
                        if ($programado['conceptos_pagos_id'] == 1) {
                            $programado['concepto_texto'] = 'Enganche';
                        } elseif ($programado['conceptos_pagos_id'] == 2) {
                            $programado['concepto_texto'] = 'Abono';
                        } else {
                            $programado['concepto_texto'] = 'Pago Único';
                        }

                        /**actualizando fecha de pago abre con helper de fechas */
                        $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);

                        //if ($programado['status'] == 1) {
                        if ($programado['status'] == 1) {
                            $num_pagos_programados_vigentes++;
                        }
                        /**aumento el pago programado vigente */
                        /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                        /**montos segun su tipo de movimiento */
                        $abonado_capital         = 0;
                        $descontado_pronto_pago  = 0;
                        $descontado_capital      = 0;
                        $complemento_cancelacion = 0;
                        $total_cubierto          = 0;
                        $fecha_ultimo_pago       = '';

                        foreach ($programado['pagados'] as $index_pagados => &$pagado) {
                            /**haciendo el arreglo de pagos realizados limpio(no repetidos) */
                            array_push(
                                $arreglo_de_pagos_realizados,
                                $pagado
                            );

                            if ($pagado['status'] == 1) {
                                /**si esta activo el pago se toma en cuenta el monto de cada operacion */
                                /**tomando en cuenta solo pagos que son parent(todos los tipos menos abono a intereses y descuento por pronto pago, estos 2 tipos
                                 * son los que van incluidos dentro de un parent) */
                                // if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                /**aqui entrarian en los abonos a capital, descuento al capital y complementos por cancelacion*/
                                if ($pagado['movimientos_pagos_id'] == 1) {
                                    /**si es de tipo 1, abono a copital, por lo regular podria llevar asociados pagos children
                                     * y se debe de recorrer el foreach para obtener los distintos montos asignados a cada pago programado
                                     */
                                    // $pago_total += $pagado['monto'];
                                    $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 4) {
                                    /**fue descuento al capital */
                                    $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else if ($pagado['movimientos_pagos_id'] == 5) {
                                    /**fue complemento por cancelacion */
                                    $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                }

                                /**fecha en que se realizo el ultimo pago */
                                $fecha_ultimo_pago = $pagado['fecha_pago'];
                                // }
                                $pagos_vigentes++;
                            } //fin if pago status=1
                            else {
                                if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                    $pagos_cancelados++;
                                }
                            }
                            if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                $pagos_realizados++;
                            }
                        } //fin foreach pagado

                        /** al final del ciclo se actualizan los valores en el pago programado*/
                        $programado['abonado_capital']           = round($abonado_capital, 2, PHP_ROUND_HALF_UP);
                        $programado['descontado_capital']        = $descontado_capital;
                        $programado['complementado_cancelacion'] = round($complemento_cancelacion, 2, PHP_ROUND_HALF_UP);

                        $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                        $programado['saldo_neto'] = round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP);
                        /**asignando la fecha del pago que liquidado el pago programado */
                        if ($programado['saldo_neto'] <= 0) {
                            $programado['fecha_ultimo_pago']     = $fecha_ultimo_pago;
                            $programado['fecha_ultimo_pago_abr'] = fecha_abr($fecha_ultimo_pago);
                        }
                        /**verificando el estado del pago programado*/
                        /**verificando si la fecha sigue vigente o esta vencida */
                        /**variables para controlar el incremento por intereses */
                        $dias_retrasados_del_pago = 0;
                        $fecha_programada_pago    = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                        /**aqui verifico que si la operacion esta activa genere los intereses acorde al dia de hoy, si esta cancelada que tomen intereses a partir de la fecha de cancelacion */
                        if ($solicitud['operacion']['operacion_status'] == 0) {
                            if (trim($solicitud['operacion']['fecha_cancelacion_operacion']) != '') {
                                $fecha_para_intereses = $solicitud['operacion']['fecha_cancelacion_operacion'];
                            }
                        }

                        $interes_generado                = 0;
                        $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                        /**fin varables por intereses */
                        /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                        if (round($saldo_pago_programado, 2, PHP_ROUND_HALF_UP) > 0) {
                            $programado['fecha_a_pagar']     = $programado['fecha_programada'];
                            $programado['status_pago']       = 1;
                            $programado['status_pago_texto'] = 'Pendiente';
                        } else {
                            $pagos_programados_cubiertos++;
                            $programado['fecha_a_pagar']
                            = $fecha_ultimo_pago;
                            /**el pago programado ya fue cubierto */
                            $programado['status_pago']       = 2;
                            $programado['status_pago_texto'] = 'Pagado';
                        }

                        /**monto con pronto pago de cada abono */
                        $programado['total_cubierto'] = $abonado_capital + $descontado_pronto_pago + $descontado_capital + $complemento_cancelacion;
                        /**actualizando los totales de montos en la venta */
                        $solicitud['operacion']['abonado_capital'] += $abonado_capital;
                        $solicitud['operacion']['descontado_capital'] += $descontado_capital;
                        $solicitud['operacion']['complementado_cancelacion'] += $complemento_cancelacion;
                        $solicitud['operacion']['saldo_neto'] += $saldo_pago_programado + $interes_generado;
                        /**calculando el total cubierto de la venta, sin intereses pagados, solo lo que ya esta cubierto */
                        $solicitud['operacion']['total_cubierto'] += $programado['total_cubierto'];
                    }
                    $solicitud['operacion']['pagos_realizados']               = $pagos_realizados;
                    $solicitud['operacion']['pagos_vigentes']                 = $pagos_vigentes;
                    $solicitud['operacion']['num_pagos_programados_vigentes'] = $num_pagos_programados_vigentes;
                    $solicitud['operacion']['pagos_cancelados']               = $pagos_cancelados;
                    $solicitud['operacion']['pagos_programados_cubiertos']    = $pagos_programados_cubiertos;
                    /**areegloe de todos los pagos limpios(no repetidos) */
                    //$venta['pagos_realizados_arreglo'] = $arreglo_de_pagos_realizados;
                }
            }

            if (isset($solicitud['operacion']['saldo_neto'])) {
                /**DEFINIENDO EL STATUS DE LA OPERACION*/
                if ($solicitud['operacion']['status'] == 0) {
                    $solicitud['operacion']['status_texto'] = 'Cancelada';
                    $solicitud['status_texto']              = 'Cancelada';

                    if ($solicitud['operacion']['motivos_cancelacion_id'] == 1) {
                        /**fue por fal de pago */
                        $solicitud['operacion']['motivos_cancelacion_texto'] = 'falta de pago';
                    } elseif ($solicitud['operacion']['motivos_cancelacion_id'] == 2) {
                        /**fue por peticion de lciente */
                        $solicitud['operacion']['motivos_cancelacion_texto'] = 'a petición del cliente';
                    } elseif ($solicitud['operacion']['motivos_cancelacion_id'] == 3) {
                        /**fue por error de captura */
                        $solicitud['operacion']['motivos_cancelacion_texto'] = 'error de captura';
                    }
                    /**actualizando el motivo de cancelacion */
                    /**actualizando el motivo de cancelacion */
                } elseif ($solicitud['operacion']['saldo_neto'] == 0) {
                    $solicitud['operacion']['status_texto'] = 'Pagada';
                    $solicitud['status_texto']              = 'Pagada';
                } else {
                    $solicitud['operacion']['status_texto'] = 'Por pagar';
                    $solicitud['status_texto']              = 'Por pagar';
                }
            } else {
                /**ESTATUS D ELA  SOLICITUD */
                /**DEFINIENDO EL STATUS DE LA VENTA*/
                if ($solicitud['status_b'] == 0) {
                    $solicitud['status_texto'] = 'Cancelada';
                    /**actualizando el motivo de cancelacion */
                } else {
                    $solicitud['status_texto'] = 'Activa';
                }
            }
        } //fin foreach venta

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_ventas */
    }

    /**CANCELAR LA VENTA */
    public function cancelar_solicitud(Request $request)
    {
        try {
            //return $request->minima_cuota_inicial;
            //validaciones directas sin condicionales
            $datos_solicitud = $this->get_solicitudes_servicios($request, $request->solicitud_id, '')[0];

            /**unicamente puede regresarse lo que  se ha cubierto de capital */
            $validaciones = [
                'solicitud_id' => 'required',
                'motivo.value' => 'required',
            ];

            $total_cubierto = 0;
            if ($datos_solicitud['operacion'] != null) {
                $validaciones['cantidad'] = 'numeric|min:0|' . 'max:' . $datos_solicitud['operacion']['total_cubierto'];
                $total_cubierto           = $datos_solicitud['operacion']['total_cubierto'];
            }

            $mensajes = [
                'required' => 'Ingrese este dato',
                'numeric'  => 'Este dato debe ser un número',
                'max'      => 'La cantidad a devolver no debe superar a la cantidad abonada hasta la fecha: $ ' . number_format($total_cubierto, 2),
                'min'      => 'La cantidad a devolver debe ser mínimo: $ 00.00 Pesos MXN',
            ];

            request()->validate(
                $validaciones,
                $mensajes
            );
            /**validar si la propiedad tiene gente sepultada */
            /**pendiente
             * pendiente
             * pendiente
             * pendiente
             */

            /**validar si la propiedad no fue dada de baja ya */

            if ($datos_solicitud['status_b'] == 0) {
                return $this->errorResponse('Esta solicitud ya habia sido cancelada.', 409);
            }

            DB::beginTransaction();
            /**verifico si la solicitud tiene servicio funerario sino para eliminar la soliitud */

            DB::table('operaciones')->where('servicios_funerarios_id', $request->solicitud_id)->update(
                [
                    'motivos_cancelacion_id'          => $request['motivo.value'],
                    'fecha_cancelacion'               => now(),
                    'cantidad_a_regresar_cancelacion' => (float) $request->cantidad,
                    'cancelo_id'                      => (int) $request->user()->id,
                    'nota_cancelacion'                => $request->comentario,
                    'status'                          => 0,
                ]
            );

            DB::table('servicios_funerarios')->where('id', $request->solicitud_id)->update(
                [
                    //'cancelo_id' => (int) $request->user()->id,
                    'status' => 0,
                ]
            );

            DB::commit();
            return $request->solicitud_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_hoja_solicitud(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */

            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/hoja_solicitud_servicio_funerario/hoja_solicitud', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "HOJA DE SERVICIO " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.hoja_solicitud_servicio_funerario.footer'),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.hoja_solicitud_servicio_funerario.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 12.4);
            $pdf->setOption('margin-right', 12.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 12.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'HOJA DE SERVICIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function hoja_preautorizacion(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */

            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/hoja_preautorizacion/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "HOJA DE PREAUTORIZACIÓN " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.hoja_preautorizacion.footer'),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.hoja_preautorizacion.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 16.4);
            $pdf->setOption('margin-right', 16.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 24.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'HOJA DE PREAUTORIZACIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function certificado_defuncion(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/certificado_defuncion/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "CERTIFICADO DE DEFUNCION " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            /*$pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('funeraria.certificado_defuncion.footer'),
            ]);*/
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.certificado_defuncion.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 12.4);
            $pdf->setOption('margin-right', 12.4);
            $pdf->setOption('margin-top', 4.4);
            $pdf->setOption('margin-bottom', 4.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'CERTIFICADO DE DEFUNCION',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function instrucciones_servicio_funerario(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/instrucciones_servicio_funerario/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "GUÍA DEL CLIENTE PARA SERVICIOS FUNERARIOS " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.instrucciones_servicio_funerario.footer'),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.instrucciones_servicio_funerario.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 12.4);
            $pdf->setOption('margin-right', 12.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 24.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'GUÍA DEL CLIENTE PARA SERVICIOS FUNERARIOS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function contrato_servicio_funerario(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el rep orte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 3;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $registro      = RegistroPublico::first();

            $pdf = PDF::loadView('funeraria/contrato_servicio_funerario/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa, 'registro' => $registro]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "CONTRATO DE SERVICIO FUNERARIO " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.contrato_servicio_funerario.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.contrato_servicio_funerario.header'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 14.4);
            $pdf->setOption('margin-right', 14.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'CONTRATO DE SERVICIO FUNERARIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function contancia_de_embalsamiento(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/embalsamiento/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "CONSTANCIA DE EMBALSAMIENTO " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.embalsamiento.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.embalsamiento.header'),
                ]);
            } elseif ($datos_solicitud['embalsamar_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.embalsamiento.nohabilitado'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 18.4);
            $pdf->setOption('margin-right', 18.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'CONSTANCIA DE EMBALSAMIENTO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function material_velacion_rentado(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/materialvelacion/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "EQUIPO DE VELACIÓN " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.materialvelacion.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.materialvelacion.header'),
                ]);
            } elseif ($datos_solicitud['material_velacion_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.materialvelacion.nohabilitado'),
                ]);
            }

            $pdf->setOption('margin-left', 18.4);
            $pdf->setOption('margin-right', 18.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'EQUIPO DE VELACIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function entrega_acta_defuncion(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /* $id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/entrega_acta/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ENTREGA DE ACTA DE DEFUNCIÓN " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.entrega_acta.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.entrega_acta.header'),
                ]);
            } elseif ($datos_solicitud['acta_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.entrega_acta.nohabilitado'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 18.4);
            $pdf->setOption('margin-right', 18.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'ENTREGA DE ACTA DE DEFUNCIÓN',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function entrega_cenizas(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /* $id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            /**verificando si el documento aplica para esta solictitud */
            /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
            }*/

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();

            $pdf = PDF::loadView('funeraria/entrega_cenizas/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);

            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ENTREGA DE CENIZAS " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.entrega_cenizas.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.entrega_cenizas.header'),
                ]);
            } elseif ($datos_solicitud['cremacion_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.entrega_cenizas.nohabilitado'),
                ]);
            }

            //$pdf->setOption('grayscale', true);
            //$pdf->setOption('header-right', 'dddd');
            $pdf->setOption('margin-left', 18.4);
            $pdf->setOption('margin-right', 18.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'ENTREGA DE CENIZAS',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function orden_servicio(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email             = $request->email_send === 'true' ? true : false;
            $email_to          = $request->email_address;
            $requestVentasList = json_decode($request->request_parent[0], true);
            $id_servicio       = $requestVentasList['id_servicio'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /* $id_servicio = 1;
            $email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $datos_solicitud = $this->get_solicitudes_servicios($request, $id_servicio, '')[0];
            if (empty($datos_solicitud)) {
                /**datos no encontrados */
                return $this->errorResponse('Error al cargar los datos.', 409);
            }

            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('funeraria/orden_servicio/documento', ['datos' => $datos_solicitud, 'empresa' => $empresa]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = "ÓRDEN DE SERVICIO " . strtoupper($datos_solicitud['nombre_afectado']) . '.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('funeraria.orden_servicio.footer', ['empresa' => $empresa]),
            ]);
            if ($datos_solicitud['status_b'] == 0) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.orden_servicio.header'),
                ]);
            } elseif ($datos_solicitud['operacion'] == null) {
                $pdf->setOptions([
                    'header-html' => view('funeraria.orden_servicio.nohabilitado'),
                ]);
            }
            $pdf->setOption('margin-left', 18.4);
            $pdf->setOption('margin-right', 18.4);
            $pdf->setOption('margin-top', 12.4);
            $pdf->setOption('margin-bottom', 33.4);
            $pdf->setOption('page-size', 'letter');

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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    strtoupper($datos_solicitud['nombre_afectado']),
                    'ÓRDEN DE SERVICIO',
                    $name_pdf,
                    $pdf
                );
                return $enviar_email;
                /**email fin */
            } else {
                return $pdf->inline($name_pdf);
            }
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al solicitar los datos', 409);
        }
    }

    public function get_estados_civiles()
    {
        return EstadosCiviles::select(
            'id',
            DB::raw(
                'UPPER(estado) as estado'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_escolaridades()
    {
        return Escolaridades::select(
            'id',
            DB::raw(
                'UPPER(escolaridad) as escolaridad'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_afiliaciones()
    {
        return Afiliaciones::select(
            'id',
            DB::raw(
                'UPPER(afiliacion) as afiliacion'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_sitios_muerte()
    {
        return SitiosMuerte::select(
            'id',
            DB::raw(
                'UPPER(sitio) as sitio'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_titulos()
    {
        return Titulos::select(
            'id',
            DB::raw(
                'UPPER(titulo) as titulo'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_estados_afectado()
    {
        return EstadosAfectado::select(
            'id',
            DB::raw(
                'UPPER(estado) as estado'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_lugares_velacion()
    {
        return LugaresServicio::select(
            'id',
            DB::raw(
                'UPPER(lugar) as lugar'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_lugares_inhumacion()
    {
        return LugaresInhumacion::select(
            'id',
            DB::raw(
                'UPPER(cementerio) as cementerio'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_material_velacion()
    {
        return LugaresInhumacion::select(
            'id',
            DB::raw(
                'UPPER(cementerio) as cementerio'
            )
        )->orderBy('id', 'asc')->get();
    }

    public function get_tipos_contratante()
    {
        return TiposContratante::select(
            'id',
            DB::raw(
                'UPPER(tipo) as tipo'
            )
        )
            ->whereNotIn('id', [6, 7])
            ->orderBy('id', 'asc')->get();
    }

    /**obteniendo los articulos y servicios para la venta y servicios */
    public function get_inventario(Request $request, $id_articulo = 'all', $paginated = '', $solo_con_existencia = 0, $material_velacion = 0)
    {
        $descripcion     = $request->descripcion;
        $numero_control  = $request->numero_control;
        $categoria_id    = $request->categorias_id;
        $resultado_query = Articulos::select(
            '*',
            DB::raw(
                '(NULL) AS existencia'
            )
        )
            ->with(['inventario' => function ($q) use ($solo_con_existencia) {
                if ($solo_con_existencia != 0) {
                    $q->where('existencia', '>', 0);
                }
            }])

            ->with('categoria')
            ->with('tipo_articulo')
            ->where('categorias_id', '<>', $material_velacion == 0 ? '4' : '')
            ->where('status', '<>', 0)
            ->where('descripcion', 'like', '%' . $descripcion . '%')
            ->where(function ($q) use ($numero_control) {
                if (trim($numero_control) != '') {
                    $q->where('articulos.codigo_barras', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($categoria_id) {
                if (trim($categoria_id) != '') {
                    $q->where('articulos.categorias_id', '=', $categoria_id);
                }
            })
            ->get();

        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado       = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado       = &$resultado_query;
        }

        foreach ($resultado as $key_articulo => &$articulo) {
            if ($articulo['status'] == 1) {
                $articulo['estatus_texto'] = 'Activo';
            } else {
                $articulo['estatus_texto'] = 'Deshabilitado';
            }

            /**actualizando iva texto y caduca texto */
            if ($articulo['grava_iva_b'] == 1) {
                $articulo['grava_iva_texto'] = 'si';
            } else {
                $articulo['grava_iva_texto'] = 'no';
            }
            if ($articulo['caduca_b'] == 1) {
                $articulo['caduca_texto'] = 'si';
            } else {
                $articulo['caduca_texto'] = 'no';
            }

            if ($articulo['tipo_articulos_id'] == 2) {
                $articulo['codigo_barras'] = 'N/A';
            }

            if ($articulo['tipo_articulos_id'] != 2) {
                /**sumando existencia */
                $existencia = 0;
                foreach ($articulo['inventario'] as $key_inventario => &$inventario) {
                    $existencia += $inventario['existencia'];
                }
                $articulo['existencia'] = $existencia;

                if ($existencia < $articulo['minimo']) {
                    $articulo['estatus_inventario_b']     = '0';
                    $articulo['estatus_inventario_texto'] = 'Desabastecido';
                } elseif ($existencia <= $articulo['maximo']) {
                    $articulo['estatus_inventario_b']     = '1';
                    $articulo['estatus_inventario_texto'] = 'Abastecido';
                } else {
                    $articulo['estatus_inventario_b']     = '2';
                    $articulo['estatus_inventario_texto'] = 'Sobrestock';
                }
            } else {
                $articulo['existencia'] = 'N/A';

                $articulo['estatus_inventario_b']     = '1';
                $articulo['estatus_inventario_texto'] = 'N/A';
            }

            /**veirifanco los estatus del inventario */
        }

        return $resultado_query;
    }

    /**categorias para filtrar los articulos en la venta en el servicio funerario */
    public function get_categorias_servicio()
    {
        /**todas menos los de articulos de renta */
        return Categorias::where('departamentos_id', '<>', 3)->get();
    }
}
