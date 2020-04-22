<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Ajustes;
use Carbon\Carbon;
use App\Propiedades;
use App\SatFormasPago;
use App\tipoPropiedades;
use App\AntiguedadesVenta;
use App\VentasPropiedades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\PagosProgramadosPropiedades;
use Illuminate\Support\Facades\Mail;
use NumerosEnLetras;

class CementerioController extends ApiController
{
    public function get_cementerio()
    {
        return
            Propiedades::with('filas_columnas')->with('tipoPropiedad')->with('tipoPropiedad.precios')->with('filas_columnas')->orderBy('id', 'asc')->get();
    }

    //obtiene los usuarios para vendedores
    public function get_vendedores()
    {
        //no super usuarios
        return User::where('roles_id', '>', 1)->get();
    }

    public function get_sat_formas_pago()
    {
        //id del conjunto de propieades

        return
            SatFormasPago::where('clave', '<>', '99')->where('clave', '<>', '25')->get();
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
            'ventaAntiguedad.value' => 'required',
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
            'num_operacion' => '',


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
        ];

        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->venta_referencia_id == 1 && $request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required|unique:ventas_propiedades,numero_titulo';
        }

        //validnado en caso de que sea de uso futuro
        if ($request->venta_referencia_id == 2) {
            //venta de uso inmediato
            $validaciones['num_solicitud'] = 'required|unique:ventas_propiedades,numero_solicitud';
            //valido si es de venta antes del sistema
            if ($request->ventaAntiguedad['value'] == 2) {
                $validaciones['convenio'] = 'required|unique:ventas_propiedades,numero_convenio';
            } else if ($request->ventaAntiguedad['value'] == 3) {
                $validaciones['convenio'] = 'required|unique:ventas_propiedades,numero_convenio';
                $validaciones['titulo'] = 'required|unique:ventas_propiedades,numero_titulo';
            }
        }
        //validando si el tipo de pago requiere de banco y digitos
        if ($request->opcionPagar['value'] == 1) {
            //si desea pagar desde la venta
            if ($request->formaPago['value'] > 1) {
                //cuqlquiera menos efectivo
                $validaciones['banco'] = 'required';
            }


            //pago con trasferencia de fondos
            if ($request->formaPago['value'] == 3) {
                //cuqlquiera menos efectivo
                if (trim($validaciones['num_operacion']) != '') {
                    $validaciones['num_operacion'] = 'unique:pagos_propiedades,referencia_operacion';
                }
            }


            if ($request->formaPago['value'] == 4 || $request->formaPago['value'] == 5) {
                //cuqlquiera menos efectivo
                $validaciones['ultimosdigitos'] = 'nullable|numeric|digits_between:4,4';
            }
        }



        /**FIN DE  VALIDACIONES CONDICIONADAS*/

        $mensajes = [
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'ubicacion.unique' => 'Este terreno ya fue vendido',
            'num_solicitud.unique' => 'Esta solicitud ya fue registrada en otra venta',
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

        /**aqui comienzan a gurdar los datos */
        $subtotal = (((float) $request->planVenta['precio_neto'])) * .84; //sin iva
        $iva = (((float) $request->planVenta['precio_neto'])) * .16; //solo el iva
        $descuento = (float) $request->descuento;
        $total_neto = $subtotal + $iva - $descuento;



        //aqui procedemos a guardar segun los datas recibidos


        //if ($request->venta_referencia_id == 1 && $request->ventaAntiguedad['value'] == 1) {
        /**********CASO DE VENTA 1 - NUEVA VENTA "SISTEMATIZADA" */
        /**VENTA DE USO INMEDIATO */
        //no se captura ningun numero de referencia, pues el numero de titulo se genera al cubrir la totalidad de la venta
        //return $request->vendedor['value'];
        try {
            DB::beginTransaction();
            $id_venta = 0;
            //venta de uso inmediato y de control sistematizado
            //captura de la venta
            $id_venta = DB::table('ventas_propiedades')->insertGetId(
                [
                    /**venta a futuro solamente */
                    'numero_solicitud' => ($request->venta_referencia_id == 2) ? $request->num_solicitud : null,
                    /**venta  liquidada solamente */
                    'numero_convenio' => $this->generarNumeroConvenio($request),
                    'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                    'antiguedad_ventas_id' => (int) $request->ventaAntiguedad['value'],
                    'propiedades_area_id' => (int) $request->propiedades_id,
                    /**la ubicacion consiste de 4 valores id_tipo_propiedad-id_propiedad-fila-lote */
                    /**ejem 4-29-1-3 */
                    'ubicacion' => $request->ubicacion,
                    'fecha_registro' => now(),
                    'fecha_venta' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                    'registro_id' => (int) $request->user()->id,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'iva' => $iva,
                    'total' => $total_neto,
                    'vendedor_id' => (int) $request->vendedor['value'],
                    'nombre' => $request->titular,
                    'fecha_nac' => date('Y-m-d', strtotime($request->fecha_nac)),
                    'domicilio' => $request->domicilio,
                    'ciudad' => $request->ciudad,
                    'estado' => $request->estado,
                    'telefono' => $request->tel_domicilio,
                    'tel_oficina' => $request->tel_oficina,
                    'celular' => $request->celular,
                    //agregar'tel_oficina' => $request->ubicacion,
                    'rfc' => $request->rfc,
                    'email' => $request->email,
                    'mensualidades' => (int) $request->planVenta['value'],
                    'enganche_inicial_plan_origen' => $request->planVenta['enganche_inicial'],
                    'ventas_referencias_id' => (int) $request->venta_referencia_id,
                ]
            );
            //captura de los beneficiarios
            $this->guardarBeneficiariosVenta($request, $id_venta);



            /**captura de pagos */
            $this->programarPagosVenta($request, $id_venta);

            /**fin de captura de pagos */
            DB::commit();
            return $id_venta;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
        /**FIN DE VENTA A USO INMEDIATO */
        /**********FIN DE CASOS DE VENTA 1 - NUEVA VENTA "SISTEMATIZADA" */
    }

    /**generar numero de convenio automatico */
    public function generarNumeroConvenio(Request $request)
    {
        $numero_convenio = 0;

        if ($request->ventaAntiguedad['value'] == 1) {
            //debo generar el numero de convenio
            //para aquellas ventas que son a futuro pero del sistema nuevo, con control sistematizado, el orden de convenios con sistemas comenzará con el numero
            //500 (quinientos)
            //determino si ya esta en funcion la asignacion de numeros de convenios automaticos
            $ajustes = Ajustes::first();
            if ($ajustes->numero_convenios_sistematizados == true) {
                //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                //$numero_convenio = ((int) VentasPropiedades::where('antiguedad_ventas_id', 1)->where('ventas_referencias_id', 2)->max('numero_convenio')) + 1;
                $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_convenio NOT LIKE '%[^0-9]%' THEN numero_convenio END) as int)) AS max_numero_convenio  from ventas_propiedades where antiguedad_ventas_id=1"));
                $ultimo_convenio = json_decode(json_encode($result), true)[0]['max_numero_convenio'];
                $numero_convenio = ((float) $ultimo_convenio) + 1;
                if ($numero_convenio < 500) {
                    //lo forzo a iniciar desde el 500
                    $numero_convenio = 500;
                }
            } else {
                //comenzamos en numero 500 (quinientos) y marcamos numero_convenios_sistematizados como true en la base de datos
                $ajustes->numero_convenios_sistematizados = true;
                $ajustes->timestamps = false;
                $ajustes->save();
                $numero_convenio = 500;
            }
        } else {
            //se debe ingresar manualmente
            $numero_convenio = $request->convenio; //es el que capturo el usuario
        }

        //se retorna el nuevo numero de convenio generado
        return $numero_convenio;
    }


    /**generar numero de convenio titulo */
    public function generarNumeroTitulo($id_venta = 0)
    {
        //$id_venta = 52;
        if ($id_venta > 0) {
            //aqui se comienza a generar el numero de titulo
            /***el numero de titulo que se genera con el sistema 
             * es para aqeullas ventas que son nueva venta antiguedad 1 y ventas que no estan
             * liquidadas antiguedad 2
             * ya que estas ventas no llevan el numero de titulo asigando por el usuario
             */

            /**el numero de titulo se asigna para aquellas ventas que ya fueron liquidadas
             * por lo tanto debemos checar en los pagos hechos a esta venta y ver si el total de pagos realizados
              es igual al total neto de la venta
             */
            $pagos_venta = VentasPropiedades::with(['pagosProgramados.pagosRealizados' => function ($q) {
                $q->where('status', '=', 1);
            }])->find($id_venta)->toArray();

            //debemos verificar que la venta no sea liquidada antes del sistema ps deberia llevar el titulo asigando por el usuario y no el calculado
            if ($pagos_venta['antiguedad_ventas_id'] != 3) {
                //la venta no fue hecha y liquidada antes del sistema

                //return $pagos_venta['pagos_programados'];
                $total_pagado = 0;
                foreach ($pagos_venta['pagos_programados'] as $pago_programado) {
                    foreach ($pago_programado['pagos_realizados'] as $pago_realizado) {
                        $total_pagado += $pago_realizado['total'];
                    }
                }

                $numero_titulo = 0;


                //checando si la suma de pagos es igual al total de la venta para generale un numero de titulo por haber cubierto la deuda
                if ($total_pagado == $pagos_venta['total']) {
                    //venta cubierta el 100%
                    //500 (quinientos)
                    //determino si ya esta en funcion la asignacion de numeros de titulos automaticos
                    $ajustes = Ajustes::first();
                    if ($ajustes->numero_titulos_sistematizados == true) {
                        //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                        $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_titulo NOT LIKE '%[^0-9]%' THEN numero_titulo END) as int)) AS max_numero_titulo  from ventas_propiedades"));
                        $ultimo_titulo = json_decode(json_encode($result), true)[0]['max_numero_titulo'];
                        $numero_titulo = $ultimo_titulo + 1;
                    } else {
                        //comenzamos en numero 500 (quinientos) y marcamos numero_titulos_sistematizados como true en la base de datos
                        $ajustes->numero_titulos_sistematizados = true;
                        $ajustes->timestamps = false;
                        $ajustes->save();
                        $numero_titulo = 500;
                    }

                    $venta = VentasPropiedades::find($id_venta);
                    //actualizamos la venta con su nuevo numero de titulo
                    $venta->numero_titulo = $numero_titulo;
                    $venta->timestamps = false;
                    $venta->save();
                }
            }
        }
    }



    //guarda los beneficiarios de la venta de una propiedad
    public function guardarBeneficiariosVenta(Request $request, $id_venta = 0)
    {
        //id del conjunto de propieades
        for ($i = 0; $i < count($request['beneficiarios']); $i++) {
            DB::table('beneficiarios_propiedades')->insert(
                [
                    'nombre' => $request['beneficiarios'][$i]['nombre'],
                    'parentesco' => $request['beneficiarios'][$i]['parentesco'],
                    'telefono' => $request['beneficiarios'][$i]['telefono'],
                    'ventas_propiedades_id' => $id_venta,
                ]
            );
        }
    }

    //guarda los beneficiarios de la venta de una propiedad
    public function programarPagosVenta(Request $request, $id_venta = 0)
    {
        /**aqui comienzan a gurdar los datos */
        $subtotal = (((float) $request->planVenta['precio_neto'])) * .84; //sin iva
        $iva = (((float) $request->planVenta['precio_neto'])) * .16; //solo el iva
        $descuento = (float) $request->descuento;
        $total_neto = $subtotal + $iva - $descuento;
        //verificando si la venta viene con algun descuento

        /**como se genera la referencia del pago para realizar pago en bancos */
        //se compone de la referencia de la venta segun el tipo de venta que es, la fecha
        //venta_referencia_id
        /**asi se compone una referencia para un pago */
        /**
         * se compone de la clave de referencia del tipo de venta segun la tabla ventas_referencias (2 digitos)
         * fecha programada del pago(8 digitos)
         * numero de pago 01,02,12,18,24,32,maximo son 64 etc. (2 digitos)
         * id de la venta, puede ir desde los 4 hasta los 5 digitos
         * ejemplo de una referencia
         * 0120200411011  // venta de propiedad de uso inmediato, fecha 11 de abril 2020, pago 01 y venta id 1
         */

        if ($total_neto == 0) {
            //este es un pago especial
            //la venta tiene 100% de descuento
            //sin importar el plan de venta solo se programara un solo pago y se registrará el pago automaticmante con el la forma de pago del sat
            //clave 25, remision de deuda

            $id_pago_programado_venta_gratis = DB::table('pagos_programados_propiedades')->insertGetId(
                [
                    'num_pago' => '01', //numero 1, pues es unico
                    'fecha_programada' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)), //fecha de la venta
                    'ventas_propiedades_id' => $id_venta, //id de la venta
                    'tipo_pagos_id' => 3, //3-liquidacion //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                    'referencia_pago' => '01' . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'descuento' => $descuento,
                    'total' => $total_neto,
                ]
            );
            //se paga automaticamente este tipo de ventas
            DB::table('pagos_propiedades')->insert(
                [
                    'pagos_programados_propiedades_id' => $id_pago_programado_venta_gratis,
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'descuento' => $descuento,
                    'total' => $total_neto,
                    'fecha_pago' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)), //fecha de la venta
                    'fecha_registro' =>  now(), //fecha de la venta
                    'registro_id' => (int) $request->user()->id,
                    'cobrador_id' => (int) $request->vendedor['value'],
                    'sat_formas_pago_id' => 6, //remision de deuda la forma pago del sat
                ]
            );

            //se corre el proceso para ver si ya esta liquidada la venta y generar el numero de titulo
            $this->generarNumeroTitulo($id_venta);
        } else {
            //puede que venga con descuento pero no es del 100%
            //determinamos que tipo de ventas
            if ($request->venta_referencia_id == 1 || (int) $request->planVenta['value'] == 0) {
                //de uso inmediato sin importar si es seleccionado a futuro o inmediato ya que selecciono pagarlo de contado
                /**se crea un solo pago */
                //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
                $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(3, 'day');
                $id_pago_programado_unico = DB::table('pagos_programados_propiedades')->insertGetId(
                    [

                        'num_pago' => 1, //numero 1, pues es unico
                        'fecha_programada' => $fecha_maxima, //fecha de la venta
                        'ventas_propiedades_id' => $id_venta, //id de la venta
                        'tipo_pagos_id' => 3, //3-liquidacion //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                        'referencia_pago' => '01' . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'subtotal' => $subtotal,
                        'iva' => $iva,
                        'descuento' => $descuento,
                        'total' => $total_neto
                    ]
                );
                //viendo si quiere registrar el abono inicial desde la venta
                if ($request->opcionPagar['value'] == 1) {
                    //quiere registrar el enganche inicial, osea el valor de la propiedad de una vez
                    DB::table('pagos_propiedades')->insert(
                        [
                            'pagos_programados_propiedades_id' => $id_pago_programado_unico,
                            'subtotal' => $subtotal,
                            'iva' => $iva,
                            'descuento' => $descuento,
                            'total' => $total_neto,
                            'fecha_pago' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)), //fecha de la venta
                            'fecha_registro' => now(), //fecha de la venta
                            'registro_id' => (int) $request->user()->id,
                            'cobrador_id' => (int) $request->vendedor['value'],
                            'sat_formas_pago_id' => $request->formaPago['value'], //
                            'banco' => ($request->formaPago['value'] != '1') ? $request->banco : null,
                            'num_cheque' => ($request->formaPago['value'] == '2') ? $request->num_cheque : null,
                            'referencia_operacion' => ($request->formaPago['value'] == '3') ? $request->num_operacion : null,
                            'ultimos_cuatro' => ($request->formaPago['value'] == '4' || $request->formaPago['value'] == '5') ? $request->ultimosdigitos : null,
                        ]
                    );
                }
                //se corre el proceso para ver si ya esta liquidada la venta y generar el numero de titulo
                $this->generarNumeroTitulo($id_venta);
            } else {

                //registro el enganche
                /**los pagos deben llevar los valores en proporcion al descuento 
                 * por decir asi, cuando el precio lleva descuento se debe de repartir el descuento total entre los diferentes pagos
                 * segun el porcentaje del pago
                 */


                $enganche_incial = (float) $request->enganche_inicial;
                $resto_a_mensualidades = (float) $request->precio_neto - (float) $request->enganche_inicial;

                $porcentaje_enganche_inicial = ($enganche_incial * 100) / (float) $request->precio_neto;
                /**obtengo el porcentaje que le corresponde a esos pagos segun el plan de venta */
                $porcentaje_resto_a_mensualidades = (100 - $porcentaje_enganche_inicial) / (int) $request->planVenta['value'];

                $sub_total_pago_enganche_sin_descuento = ((float) $request->enganche_inicial) + (($descuento * $porcentaje_enganche_inicial) / 100);

                //enganche inicial mandado mas lo descontado para sacar impuestos completos
                $subtotal_enganche = $sub_total_pago_enganche_sin_descuento * .84;
                $iva_enganche = $sub_total_pago_enganche_sin_descuento * .16;
                $descuento_enganche = ($descuento * $porcentaje_enganche_inicial) / 100;

                $total_enganche = $subtotal_enganche + $iva_enganche - $descuento_enganche;
                //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
                $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(3, 'day');
                $id_pago_programado_enganche = DB::table('pagos_programados_propiedades')->insertGetId(
                    [
                        'num_pago' => 1, //numero 1, pues es enganche
                        'fecha_programada' => $fecha_maxima, //fecha de la venta
                        'ventas_propiedades_id' => $id_venta, //id de la venta
                        'tipo_pagos_id' => 1, //1-enganche //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                        'referencia_pago' => '02'
                            /**tipo 02 por ser a meses */
                            . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'subtotal' => $subtotal_enganche,
                        'iva' => $iva_enganche,
                        'descuento' => $descuento_enganche,
                        'total' => $total_enganche
                    ]
                );

                /**verifico si pago el enganchde desde la venta */
                if ($request->opcionPagar['value'] == 1) {
                    //quiere registrar el enganche inicial, osea el valor de la propiedad de una vez
                    DB::table('pagos_propiedades')->insert(
                        [
                            'pagos_programados_propiedades_id' => $id_pago_programado_enganche,
                            'subtotal' => $subtotal_enganche,
                            'iva' => $iva_enganche,
                            'descuento' => $descuento_enganche,
                            'total' => $total_enganche,
                            'fecha_pago' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)), //fecha de la venta
                            'fecha_registro' => now(), //fecha de la venta
                            'registro_id' => (int) $request->user()->id,
                            'cobrador_id' => (int) $request->vendedor['value'],
                            'sat_formas_pago_id' => $request->formaPago['value'], //
                            'banco' => ($request->formaPago['value'] != '1') ? $request->banco : null,
                            'num_cheque' => ($request->formaPago['value'] == '2') ? $request->num_cheque : null,
                            'referencia_operacion' => ($request->formaPago['value'] == '3') ? $request->num_operacion : null,
                            'ultimos_cuatro' => ($request->formaPago['value'] == '4' || $request->formaPago['value'] == '5') ? $request->ultimosdigitos : null,
                        ]
                    );
                }

                //a futuro y a meses
                for ($i = 1; $i <= ((int) $request->planVenta['value']); $i++) {
                    //aqui van las seis mensualidades del plan de ventas seleccionado y se crear los registros de pagos programados
                    $sub_total_pago_sin_descuento = ($resto_a_mensualidades / (int) $request->planVenta['value']) + (($descuento * $porcentaje_resto_a_mensualidades) / 100);


                    $subtotal_pago = $sub_total_pago_sin_descuento * .84;
                    $iva_pago = $sub_total_pago_sin_descuento * .16;
                    $descuento_pago = ($descuento * $porcentaje_resto_a_mensualidades) / 100;
                    $total_pago = $subtotal_pago + $iva_pago - $descuento_pago;
                    $numero_pago_para_referencia = '';
                    if ($i < 10) {
                        //se debe asignar un cero (0) para crear la referencia correcta
                        $numero_pago_para_referencia = '0' . ($i + 1);
                    } else {
                        $numero_pago_para_referencia = ($i + 1);
                    }

                    $fecha = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add($i, 'month');
                    DB::table('pagos_programados_propiedades')->insertGetId(
                        [
                            'num_pago' => ($i + 1), //numero 1, pues es enganche
                            'fecha_programada' => $fecha, //fecha de la venta
                            'ventas_propiedades_id' => $id_venta, //id de la venta
                            'tipo_pagos_id' => 2, //3-enganche //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                            'referencia_pago' => '02'
                                /**tipo 02 por ser a meses */
                                . date('Ymd', strtotime($request->fecha_venta)) . $numero_pago_para_referencia . $id_venta, //se crea una referencia para saber a que pago pertenece
                            'subtotal' => $subtotal_pago,
                            'iva' => $iva_pago,
                            'descuento' => $descuento_pago,
                            'total' => $total_pago
                        ]
                    );
                }
                /**aqui corro el ciclo para ver cuantos pagos se van a hacer, diferenciando el enganche  */
            }
        }
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
        return DB::table('ventas_referencias')->where('id', '<', 3)->get();
    }


    //obtiene los tipos de antiguedad de una venta, son 3 registros predefinidos
    public function get_antiguedades_venta()
    {
        //id del conjunto de propieades

        return
            AntiguedadesVenta::get();
    }





    /**obtiene todas las ventas para el paginado de ventas de cementerio */
    public function get_ventas(Request $request)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular = $request->titular;
        $numero_control = $request->numero_control;
        $status = $request->status;


        $resultado = $this->showAllPaginated(
            VentasPropiedades::select(
                'ventas_propiedades.propiedades_area_id',
                'nombre',
                'ventas_propiedades.status',
                'ventas_propiedades.id',
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'ubicacion as ubicacion_raw',
                'tipo_propiedades.tipo',
                'ventas_propiedades.status',
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.ventas_referencias_id = "1" THEN "Inmediato"
                        ELSE "A futuro" 
                        END) AS uso_venta'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_solicitud <> "" THEN ventas_propiedades.numero_solicitud
                        ELSE "N/A" 
                        END) AS numero_solicitud'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_convenio <> "" THEN ventas_propiedades.numero_convenio
                        ELSE "N/A" 
                        END) AS numero_convenio'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_titulo <> "" THEN ventas_propiedades.numero_titulo
                        ELSE "Pendiente" 
                        END) AS numero_titulo'
                ),
                DB::raw(
                    '"" as ubicacion_texto'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_des'
                )
            )
                ->with(
                    ['pagosProgramados.pagosRealizados' => function ($q) {
                        $q->where('status', '=', 1);
                    }]
                )
                ->where(function ($q) use ($status) {
                    if ($status != '') {
                        $q->where('ventas_propiedades.status', $status);
                    }
                })
                ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                    if (trim($numero_control) != '') {
                        if ($filtro_especifico_opcion == 1) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_propiedades.numero_solicitud', '=',  $numero_control);
                        } else if ($filtro_especifico_opcion == 2) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_propiedades.numero_convenio', '=',  $numero_control);
                        } else if ($filtro_especifico_opcion == 3) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_propiedades.numero_titulo', '=',  $numero_control);
                        } else {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_propiedades.id', $numero_control);
                        }
                    }
                })
                ->where(function ($q) use ($titular) {
                    if (trim($titular) != '') {
                        $q->where('ventas_propiedades.nombre', 'like', '%' . $titular . '%');
                    }
                })
                ->join('propiedades', 'ventas_propiedades.propiedades_area_id', '=', 'propiedades.id')
                ->join('tipo_propiedades', 'propiedades.tipo_propiedades_id', '=', 'tipo_propiedades.id')
                ->orderBy('ventas_propiedades.id', 'desc')
                ->get()
        );


        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        $datos_cementerio = $this->get_cementerio();
        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */

        //**se actualiza la propiedad a formato legible para el usuario */
        foreach ($resultado as $valor) {
            $valor->ubicacion_texto = $this->ubicacion_texto($valor->ubicacion_raw, $datos_cementerio);
        }

        //se retorna el resultado
        return $resultado;
    }

    public function ubicacion_texto($dato = '', $datos_cementerio = [])
    {
        //checo si los datos del cemeterio vienen vacios para llenar el arreglo
        if (count($datos_cementerio) == 0) {
            /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
            $datos_cementerio = $this->get_cementerio();
            /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        }

        /**se obtienen los parametros de la ubicacion */
        $id_tipo = explode("-", $dato)[0];
        $id_propiedad = explode("-", $dato)[1];
        $fila = explode("-", $dato)[2];
        $lote = explode("-", $dato)[3];

        /**se necesita crear un arregle con el abecedario para cuadrar las propiedades segun su fila "en alfabeto" */
        $alfabeto = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        /**se procede a crear una ubicacion entendible para el usuario */

        $ubicacion_texto = '';
        foreach ($datos_cementerio as $propiedad) {
            //recorriendo propiedades
            if ($propiedad->id == $id_propiedad) {
                //una vez encontrada el id defino si es terraza que es
                if ($propiedad->tipo_propiedades_id == 1) {
                    //uniplex
                    $ubicacion_texto .= "Uniplex " . $propiedad->propiedad_indicador . " Módulo " . $fila;
                } else if ($propiedad->tipo_propiedades_id == 2) {
                    //duplex
                    $ubicacion_texto .= "Duplex " . $propiedad->propiedad_indicador . " Módulo " . $fila;
                } else if ($propiedad->tipo_propiedades_id == 3) {
                    //nicho
                    $ubicacion_texto .= "Nichos - Columna " . $propiedad->propiedad_indicador . ", Fila " . $fila;
                } else if ($propiedad->tipo_propiedades_id == 4) {
                    //cuadriplex
                    $ubicacion_texto .= "Terraza " . $propiedad->propiedad_indicador . ", Fila " . strtoupper($alfabeto[$fila - 1]) . " Lote " . $lote;
                } else if ($propiedad->tipo_propiedades_id == 5) {
                    //triplex
                    $ubicacion_texto .= "Triplex " . $propiedad->propiedad_indicador . " Módulo " . $fila;
                } else if ($propiedad->tipo_propiedades_id == 6) {
                    //cuadriplex sin terraza
                    $ubicacion_texto .= "cuadriplex " . $propiedad->propiedad_indicador . " Módulo " . $fila;
                }
            }
        }

        return $ubicacion_texto;
    }




    /**obtiene la venta por id */
    public function get_venta_id($venta_id = 0)
    {
        $id_venta = $venta_id;

        $resultado =
            VentasPropiedades::select(
                'ventas_propiedades.status',
                'email',
                'ventas_propiedades.propiedades_area_id',
                'nombre',
                'ciudad',
                'estado',
                'rfc',
                'fecha_registro',
                'mensualidades',
                'enganche_inicial_plan_origen',
                'ventas_propiedades.status',
                'ventas_propiedades.id',
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'numero_solicitud AS numero_solicitud_raw',
                'numero_convenio as numero_convenio_raw',
                'numero_titulo as numero_titulo_raw',
                'ubicacion as ubicacion_raw',
                'tipo_propiedades.tipo',
                'fecha_venta',
                'fecha_nac',
                'total',
                'subtotal',
                'descuento',
                'iva',
                'domicilio',
                'telefono',
                'celular',
                'tel_oficina',
                'email',
                'ventas_propiedades.status',
                'antiguedad_ventas_id',
                'vendedor_id',
                'ventas_referencias_id',
                DB::raw(
                    '(NULL) AS tipo_raw'
                ),
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.ventas_referencias_id = "1" THEN "Inmediato"
                        ELSE "A futuro" 
                        END) AS uso_venta'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_solicitud <> "" THEN ventas_propiedades.numero_solicitud
                        ELSE "N/A" 
                        END) AS numero_solicitud'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_convenio <> "" THEN ventas_propiedades.numero_convenio
                        ELSE "N/A" 
                        END) AS numero_convenio'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.numero_titulo <> "" THEN ventas_propiedades.numero_titulo
                        ELSE "Pendiente" 
                        END) AS numero_titulo'
                ),
                DB::raw(
                    '"" as ubicacion_texto'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_propiedades.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_des'
                )
            )
            ->with(
                ['pagosProgramados.pagosRealizados' => function ($q) {
                    $q->where('status', '=', 1);
                }]
            )
            ->with(
                'pagosProgramados.pagosRealizados.tipoPagoSat'
            )
            ->with(
                'pagosProgramados.tipoPago'
            )
            ->with(
                'propiedad.tipoPropiedad'
            )
            ->with(array('vendedor' => function ($query) {
                $query->select('id', 'nombre');
            }))
            ->with(
                'beneficiarios'
            )
            ->with(
                'antiguedad'
            )
            ->where('ventas_propiedades.id', $id_venta)
            ->join('propiedades', 'ventas_propiedades.propiedades_area_id', '=', 'propiedades.id')
            ->join('tipo_propiedades', 'propiedades.tipo_propiedades_id', '=', 'tipo_propiedades.id')
            ->orderBy('ventas_propiedades.id', 'desc')
            ->get();



        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        $datos_cementerio = $this->get_cementerio();
        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */

        //**se actualiza la propiedad a formato legible para el usuario */
        foreach ($resultado as $valor) {
            $valor->ubicacion_texto = $this->ubicacion_texto($valor->ubicacion_raw, $datos_cementerio);

            /**agregando fila, lote, y tipo, por separado en valor numrico */
            $valor->tipo_raw = explode("-", $valor->ubicacion_raw)[0];
            $valor->fila_raw = explode("-", $valor->ubicacion_raw)[2];
            $valor->lote_raw = explode("-", $valor->ubicacion_raw)[3];
        }



        //se retorna el resultado
        return $resultado;
    }







    public function referencias_de_pago(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /* $id_venta = 89;
        $email = true;
        $email_to = 'hector@gmail.com';
        */
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta)->toArray();

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/pagos/referencias_de_pago', ['datos' => $datos_venta[0], 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "REFERENCIA DE PAGOS TITULAR " . strtoupper($datos_venta[0]['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.pagos.footer'),
        ]);
        if ($datos_venta[0]['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.pagos.header')
            ]);
        }




        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-bottom', 0);
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
                strtoupper($datos_venta[0]['nombre']),
                'REFERENCIAS DE PAGO CEMENTERIO',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }


    /**pdf del convenio plan de cementerio */
    public function documento_convenio(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /* $id_venta = 3;
        $email = false;
        $email_to = 'hector@gmail.com';*/

        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];
        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta)->toArray();

        /**verificando si el documento aplica para esta solictitud */
        if ($datos_venta[0]['numero_convenio_raw'] == null) {
            return 0;
        }


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/convenio/documento_convenio', ['datos' => $datos_venta[0], 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "CONVENIO TITULAR " . strtoupper($datos_venta[0]['nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.convenio.footer'),
        ]);
        if ($datos_venta[0]['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.convenio.header')
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 25.4);
        $pdf->setOption('margin-right', 25.4);
        $pdf->setOption('margin-top', 25.4);
        $pdf->setOption('margin-bottom', 25.4);
        $pdf->setOption('page-size', 'legal');

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
                strtoupper($datos_venta[0]['nombre']),
                'COPIA DEL CONVENIO / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }




    /**pdf de la solicitud de plan de cementerio */
    public function documento_solicitud(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 35;
        $email = false;
        $email_to = 'hector@gmail.com';
        */
        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta)->toArray();

        /**verificando si el documento aplica para esta solictitud */
        if ($datos_venta[0]['numero_solicitud_raw'] == null) {
            return 0;
        }


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/solicitud/documento_solicitud', ['datos' => $datos_venta[0], 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "SOLICITUD TITULAR " . strtoupper($datos_venta[0]['nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.solicitud.footer'),
        ]);
        if ($datos_venta[0]['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.solicitud.header')
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 5.4);
        $pdf->setOption('margin-right', 5.4);
        $pdf->setOption('margin-top', 5.4);
        $pdf->setOption('margin-bottom', 10.4);
        $pdf->setOption('page-size', 'a4');

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
                strtoupper($datos_venta[0]['nombre']),
                'SOLICITUD DE PROPIEDAD / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }



    /**pdf del estado de cuenta del cementerio */
    public function documento_estado_de_cuenta_cementerio(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 38;
        $email = false;
        $email_to = 'hector@gmail.com';
*/
        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta)->toArray();

        /**verificando si el documento aplica para esta solictitud */
        if ($datos_venta[0]['numero_solicitud_raw'] == null) {
            return 0;
        }


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/estado_cuenta/estado_cuenta', ['datos' => $datos_venta[0], 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "ESTADO CUENTA " . strtoupper($datos_venta[0]['nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.estado_cuenta.footer'),
        ]);
        if ($datos_venta[0]['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.estado_cuenta.header')
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 5.4);
        $pdf->setOption('margin-right', 5.4);
        $pdf->setOption('margin-top', 5.4);
        $pdf->setOption('margin-bottom', 10.4);
        $pdf->setOption('page-size', 'a4');

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
                strtoupper($datos_venta[0]['nombre']),
                'ESTADO DE CUENTA / CEMENTERIO AETERNUS',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }
}