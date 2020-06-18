<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\SATMonedas;
use App\Operaciones;
use App\SatFormasPago;
use App\PagosProgramados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Pagos;
use App\VentasTerrenos;
use PhpParser\Node\Stmt\TryCatch;

class PagosController extends ApiController
{

    public function get_cobradores()
    {
        //no super usuarios
        /**puesto de venderor id 5 */
        /**obtiene los usuarios con puesto de cobrador */
        return $this->showAll(User::select('id', 'nombre')
            ->join('usuarios_puestos', 'usuarios_puestos.usuarios_id', '=', 'usuarios.id')
            ->where('roles_id', '>', 1)
            ->where('puestos_id', '=', 5)
            ->where('usuarios.status', '>', 0)
            ->get());
    }

    public function get_formas_pago_sat()
    {
        return $this->showAll(SatFormasPago::get());
    }

    public function get_monedas_sat()
    {
        return $this->showAll(SATMonedas::get());
    }


    public function calcular_adeudo($referencia, $fecha = '', $multipago = 'false')
    {

        $request = new \Illuminate\Http\Request();

        $request->replace([
            'referencia' => $referencia,
            'multipago' => $multipago,
            'fecha_pago' => $fecha
        ]);


        $validaciones = [
            'referencia' => 'required',
            'multipago' => 'required',
            'fecha_pago' => 'required|date_format:Y-m-d H:i',
        ];


        $mensajes = [
            'referencia.required' => 'Es necesario ingresar un número de referencia.',
            'multipago.required' => 'Indique la modalidad de pago.',
            'fecha_pago.required' => 'Debe ingresar una fecha de pago.',
            'fecha_pago.date_format' => 'El formato de la fecha no es correcto(Y-m-d H:i).',
        ];
        $request->validate(
            $validaciones,
            $mensajes
        );
        /**esta funcion recibe como parametros un numero de referencia
         * una fecha de pago
         * y un true o false para considerar o no un pago multiple(todas las referencias de dicha operacion que aun tienen adeudo)
         */

        /**con la referencia enviada, tenemos la forma de obtener todos los pagos asocaidos a esa operacion */
        $pago_programado = PagosProgramados::where('referencia_pago', '=', $request->referencia)->orderBy('id', 'asc')->where('status', '=', 1)->get();
        if (count($pago_programado)) {
            /**se ha encontrado la referencia y se procede a hacer los respectivos calculos */
            $resultado = Operaciones::select(
                'operaciones.id as operacion_id',
                'operaciones.status as operacion_status',
                'total',
                'costo_neto_pronto_pago',
                'empresa_operaciones_id',
                'clientes.nombre',
                DB::raw(
                    'DATE(fecha_operacion) as fecha_operacion',
                ),
                DB::raw(
                    '(NULL) as fecha_operacion_texto',
                ),
                DB::raw(
                    '(NULL) AS operacion_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_texto'
                ),
            )
                ->with([
                    'pagosProgramados' => function ($query) use ($request) {
                        if ($request->multipago == 'false') {
                            /**como no es multipago, solo se filtra la referencia solicitada */
                            return $query->where('pagos_programados.referencia_pago', '=', $request->referencia)->where('status', '=', 1)->with('pagados');
                        } else {
                            return $query->with('pagados');
                        }
                    },
                ])
                ->with('AjustesPoliticas')
                ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
                ->where('operaciones.id', '=', $pago_programado[0]->operaciones_id)
                ->get()->toArray();



            foreach ($resultado as $index_dato => &$dato) {
                $dato['fecha_a_pagar'] = date('Y-m-d', strtotime($request->fecha_pago));
                $dato['fecha_a_pagar_texto'] = fecha_abr(date('Y-m-d', strtotime($request->fecha_pago)));
                $dato['fecha_operacion_texto'] = fecha_abr($dato['fecha_operacion']);
                /**DEFINIENDO EL STATUS DE LA dato*/
                if ($dato['operacion_status'] == 0) {
                    $dato['status_texto'] = 'Cancelada';
                } elseif ($dato['operacion_status'] == 1) {
                    $dato['status_texto'] = 'Por Pagar';
                } elseif ($dato['operacion_status'] == 2) {
                    $dato['status_texto'] = 'Pagada';
                }

                if ($dato['empresa_operaciones_id'] == 1) {
                    /**es venta de propiedad */
                    $dato['operacion_texto'] = 'Abono por Venta de Propiedad';
                } elseif ($dato['empresa_operaciones_id'] == 2) {
                    /**SERVICIO DE MANTENIMIENTO ANUAL EN CEMENTERIO. */
                    $dato['operacion_texto'] = 'Pago de Cuota de Mantenimiento';
                } elseif ($dato['empresa_operaciones_id'] == 3) {
                    /**SERVICIOS FUNERARIOS. */
                    $dato['operacion_texto'] = 'Pago por Servicios Funerarios';
                } elseif ($dato['empresa_operaciones_id'] == 4) {
                    /**VENTA DE PLANES FUNERARIOS A FUTURO */
                    $dato['operacion_texto'] = 'Abono a Plan Funerario de uso a Futuro';
                } elseif ($dato['empresa_operaciones_id'] == 5) {
                    /**ERVICIOS ESPECIALES CON EXTREMIDADES */
                    $dato['operacion_texto'] = 'Pago por Servicios Especiales con Extremidades';
                } elseif ($dato['empresa_operaciones_id'] == 6) {
                    /**VENTAS EN GRAL. */
                    $dato['operacion_texto'] = 'Venta en Gral.';
                }


                /**aqui se saca el porcentaje para ver cuanto seria el descuento por pago en cada pronto pago */
                $porcentaje_descuento_pronto_pago = 0;
                if (floatval($dato['total']) > 0) {
                    $porcentaje_descuento_pronto_pago = (floatval($dato['costo_neto_pronto_pago']) * 100) / floatval($dato['total']);
                }


                /**recorriendo arreglo de pagos programados */
                foreach ($dato['pagos_programados']  as $index_programado => &$programado) {
                    //if ($programado['status_pago'] != 2) {
                    /**actualizando el concepto del pago */
                    if ($programado['conceptos_pagos_id'] == 1) {
                        $programado['concepto_texto'] = 'Enganche';
                        /**verificando que el pago de enganche no se trate de hacer con pronto pago*/
                        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                            return $this->errorResponse('El pago de tipo (Enganche) no aplica para fecha antes de la fecha programada.', 409);
                        }
                    } elseif ($programado['conceptos_pagos_id'] == 2) {
                        $programado['concepto_texto'] = 'Abono';
                    } else {
                        $programado['concepto_texto'] = 'Pago Único';
                        /**verificando que el pago de enganche no se trate de hacer con pronto pago*/
                        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                            return $this->errorResponse('El pago de tipo (Pago Único) no aplica para fecha antes de la fecha programada.', 409);
                        }
                    }
                    /**actualizando fecha de pago abre con helper de fechas */
                    $programado['fecha_programada_abr'] = fecha_abr($programado['fecha_programada']);


                    /**aumento el pago programado vigente */
                    /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                    /**montos segun su tipo de movimiento */
                    $abonado_intereses = 0;
                    $abonado_capital = 0;
                    $descontado_pronto_pago = 0;
                    $descontado_capital = 0;
                    $complemento_cancelacion = 0;
                    $fecha_ultimo_pago = '';

                    if (!empty($programado['pagados'])) {
                        $pago_total = 0;
                        foreach ($programado['pagados']  as $index_pagados => &$pagado) {
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
                                    $pago_total += $pagado['monto'];
                                    $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else  if ($pagado['movimientos_pagos_id'] == 4) {
                                    /**fue descuento al capital */
                                    $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                } else  if ($pagado['movimientos_pagos_id'] == 5) {
                                    /**fue complemento por cancelacion */
                                    $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                } elseif ($pagado['movimientos_pagos_id'] == 2) {
                                    /**es tipo interes */
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es abono de intereses */
                                        $abonado_intereses += $pagado['pagos_cubiertos']['monto'];
                                        $pago_total += $pagado['monto'];
                                    }
                                } elseif ($pagado['movimientos_pagos_id'] == 3) {
                                    if ($pagado['pagos_cubiertos']['pagos_programados_id'] == $programado['id']) {
                                        /**es descuento por pronto pago */
                                        $descontado_pronto_pago += $pagado['pagos_cubiertos']['monto'];
                                        $pago_total += $pagado['monto'];
                                    }
                                }
                                /**fecha en que se realizo el ultimo pago */
                                $fecha_ultimo_pago = $pagado['fecha_pago'];
                                // }
                            } //fin if pago status=1
                        } //fin foreach pagado
                        /**actualizando el total del pago, cuando tiene pagos hijos */
                        $pagado['pago_total'] = $pago_total;
                    }

                    /** al final del ciclo se actualizan los valores en el pago programado*/
                    $programado['abonado_capital'] = round($abonado_capital, 2);
                    $programado['abonado_intereses'] =   round($abonado_intereses, 2);
                    $programado['descontado_pronto_pago'] =   round($descontado_pronto_pago, 2);
                    $programado['descontado_capital'] =   round($descontado_capital, 2);
                    $programado['complementado_cancelacion'] =   round($complemento_cancelacion, 2);


                    $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;

                    $programado['saldo_neto'] = round($saldo_pago_programado, 2);

                    /**asignando la fecha del pago que liquidado el pago programado */
                    if ($programado['saldo_neto'] <= 0) {
                        $programado['fecha_ultimo_pago'] = $fecha_ultimo_pago;
                    }

                    /**verificando el estado del pago programado*/
                    /**verificando si la fecha sigue vigente o esta vencida */
                    /**variables para controlar el incremento por intereses */
                    $dias_retrasados_del_pago = 0;
                    $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);

                    $fecha_a_pagar = Carbon::createFromFormat('Y-m-d',  substr($request->fecha_pago, 0, 10));
                    $interes_generado = 0;
                    $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_programada']);
                    /**fin varables por intereses */
                    /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                    if ($saldo_pago_programado > 0) {
                        $programado['fecha_a_pagar'] = date('Y-m-d', strtotime($request->fecha_pago));
                        $programado['fecha_a_pagar_abr'] = fecha_abr($programado['fecha_a_pagar']);
                        /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                        if (date('Y-m-d', strtotime($programado['fecha_programada'])) < $programado['fecha_a_pagar']) {
                            /**no aplica pronto pago, porque la fecha ya vencio */
                            $programado['aplica_pronto_pago_b'] = 0;
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_a_pagar);
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
                                $interes_generado = round(((($programado['monto_programado'] * ($dato['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago), 2);
                                if ($interes_generado > 0) {
                                    if ($interes_generado >= $programado['abonado_intereses']) {
                                        /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo abono a intereses */
                                        $interes_generado -= $programado['abonado_intereses'];
                                    } else {
                                        /**fechas anteriores a la de los intereses, por lo cual salta el error de que cambio dfechas */
                                        return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
                                    }
                                }
                            }

                            /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                            $programado['saldo_neto'] = $saldo_pago_programado + ($interes_generado);
                            /**la fecha qui es mayor que la fecha programada del pago */
                            $programado['status_pago'] = 0;
                            $programado['status_pago_texto'] = 'Vencido';

                            $programado['dias_vencido'] = $dias_retrasados_del_pago;
                            $programado['intereses'] =  $interes_generado;
                        } else {
                            /**la fecha aun no vence */
                            $programado['status_pago'] = 1;
                            $programado['status_pago_texto'] = 'Pendiente';
                            /**verifianco si la fecha es antes del pagro progrmaado para veri si tiene derecho a descuento */
                            if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($programado['fecha_programada']))) {
                                /**aplicando pronto pago solo a abonos */
                                if ($programado['conceptos_pagos_id'] == 2) {
                                    $programado['aplica_pronto_pago_b'] = 1;
                                    /**calculando monto a a descontar */
                                    //return $programado['descontado_pronto_pago'];
                                    $programado['descuento_pronto_pago'] = round(((($programado['monto_programado']) - (($porcentaje_descuento_pronto_pago * ($programado['monto_programado'])) / 100))), 2);
                                    if ($programado['descuento_pronto_pago'] > 0) {
                                        if ($programado['descuento_pronto_pago'] >= $programado['descontado_pronto_pago']) {
                                            /**esto siginifica que la fecha de pago seria mayor o igual a la fecha en que se hizo el ultimo descuento a capital */
                                            $programado['descuento_pronto_pago'] -= $programado['descontado_pronto_pago'];
                                        } else {
                                            /**fechas anteriores a la del descuento de capital, por lo cual salta el error de que cambio dfechas */
                                            return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                        /**el pago programado ya fue cubierto */
                        $programado['status_pago'] = 2;
                        $programado['status_pago_texto'] = 'Pagado';
                    }
                    /**monto con pronto pago de cada abono */

                    $programado['monto_pronto_pago'] = round((($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100), 2);
                    $programado['total_cubierto'] = $programado['abonado_capital'] +  $programado['descontado_pronto_pago'] + $programado['descontado_capital'] + $programado['complementado_cancelacion'];

                    /**verificando el estado de la referencia que se desa consultar */
                    if ($programado['referencia_pago'] == $referencia) {
                        if ($programado['saldo_neto'] <= 0) {
                            /**el pago que se mando como refencia ya fue pagado */
                            return $this->errorResponse('La referencia que desea abonar (' . $referencia . ') ya fue liqudiada.', 409);
                        }
                    }
                    // } //fin if status_pago!=2 // pagado
                } //fin foreach programados

            } //fin foreach dato
            return $resultado;
        } else {
            /**vacio, no encontrado */
            return $this->errorResponse('No se ha encontrado el número de referencia solicitado.', 409);
        }
    }



    public function guardar_pago(Request $request)
    {
        //return $request;
        $validaciones = [
            'multipago' => 'required',
            'fecha_pago' => 'required|date_format:Y-m-d H:i',
            'pagos_a_cubrir' => 'required',
            'pagos_a_cubrir.*.referencia_pago' => 'required',
            'pagos_a_cubrir.*.fecha_a_pagar' => 'required|date_format:Y-m-d',
            'abono' => 'numeric|required|min:0|gt:descuento_pronto_pago',
            'intereses' => 'numeric|required|min:0',
            'descuento_pronto_pago' => 'numeric|required|min:0|lt:abono',
            'total' => 'numeric|required|min:0',
            'formaPago.value' => 'required',
            'cobrador.value' => 'required',
            'moneda.value' => 'required',
            'pago_con_cantidad' => '',
            'cambio_pago' => '',
        ];


        if (isset($request->formaPago['value'])) {
            if ($request->formaPago['value'] == 1) {
                $validaciones['pago_con_cantidad'] = 'numeric|required|min:' . (float) $request->total;
                $validaciones['cambio_pago'] = 'numeric|required|min:0';
            } else {
                $request->pago_con_cantidad = (float) $request->total;
                $request->cambio_pago = 0;
            }
        } else {
            return $this->errorResponse('No se ha ingresado una forma de pago.', 409);
        }

        $mensajes = [

            'pago_con_cantidad.required' => 'Debe ingresar la $ Cantidad con que se realizó el pago.',
            'pago_con_cantidad.min' => 'La $ Cantidad con que se realizó el pago debe ser al menos $ ' . number_format((float) $request->total, 2),
            'cambio_pago.required' => 'Debe ingresar la $ Cantidad que se devolvió como cambio.',
            'cambio_pago.min' => 'La $ Cantidad con que se realizó el pago debe ser al menos $ ' . number_format(0, 2),
            'formaPago.value.required' => 'Debe ingresar la clave de la forma de pago.',
            'moneda.value.required' => 'Debe ingresar la clave del tipo de moneda de cambio.',
            'cobrador.value.required' => 'Debe ingresar la clave del cobrador que realizó el cobro.',
            '*.fecha_a_pagar.required' => 'Debe ingresar la fecha de pago en cada una de las referencias.',
            '*.fecha_a_pagar.date_format' => 'El formato de la fecha no es correcto(Y-m-d).',
            '*.referencia_pago.required' => 'Debe ingresar las referencias de pago.',
            'pagos_a_cubrir.required' => 'Debe ingresar el conjunto de pagos a cubrir.',
            'multipago.required' => 'Indique la modalidad de pago.',
            'fecha_pago.required' => 'Debe ingresar una fecha de pago.',
            'fecha_pago.date_format' => 'El formato de la fecha no es correcto(Y-m-d H:i).',
            /**cantidades del pago */
            'abono.required' => 'Ingrese la $ cantidad del Abono.',
            'abono.numeric' => 'La $ cantidad del Abono debe ser un número valido.',
            'abono.min' => 'La $ cantidad del Abono debe ser mayor o igual a cero.',
            'abono.gt' => 'La $ cantidad del Abono debe ser mayor al descuento por pronto pago.',
            'intereses.required' => 'Ingrese la $ cantidad de Intereses.',
            'intereses.numeric' => 'La $ cantidad de Intereses debe ser un número valido.',
            'intereses.min' => 'La $ cantidad de Intereses debe ser mayor o igual a cero.',
            'descuento_pronto_pago.required' => 'Ingrese la $ cantidad de Descuento por Pronto Pago.',
            'descuento_pronto_pago.lt' => 'El descuento por pronto pago dede ser menor al abono a capital.',
            'descuento_pronto_pago.numeric' => 'La $ cantidad de Descuento por Pronto Pago debe ser un número valido.',
            'descuento_pronto_pago.min' => 'La $ cantidad de Descuento por Pronto Pago debe ser mayor o igual a cero.',
            'total.required' => 'Ingrese la $ cantidad Total del Pago.',
            'total.numeric' => 'La $ cantidad Total del Pago debe ser un número valido.',
            'total.min' => 'La $ cantidad Total del Pago debe ser mayor o igual a cero.',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );


        foreach ($request->pagos_a_cubrir as $index_referencia => $referencia) {
            /**verificando que las fechas enviadas en los pagoa a cubrir y la seleccionada al calcular el adeudo coincidan */
            if (date('Y-m-d', strtotime($referencia['fecha_a_pagar'])) != date('Y-m-d', strtotime($request->fecha_pago))) {
                /**las fechas coinciden */
                return $this->errorResponse('Hemos detectado que la fecha de pago ha cambiado, por favor vuelva a calcular el adeudo.', 409);
            }
        }
        /**las validaciones han pasado y se procede a guardar los datos */
        //calcular_adeudo($referencia, $fecha = '', $multipago = 'false')
        /**verificando si el pago es multipago */
        $referencias_adeudos = array();
        if (count($request->pagos_a_cubrir) > 1) {
            /**es multipago */
            $referencias_adeudos = $this->calcular_adeudo($request->referencia, $request->fecha_pago, 'true');
        } else if (count($request->pagos_a_cubrir) == 1) {
            /**es unipago */
            $referencias_adeudos = $this->calcular_adeudo($request->referencia, $request->fecha_pago, 'false');
        } else {
            /**no hay pagoa a cubrir */
            return $this->errorResponse('No se ha encontrado ninguna referencia de pago a cubrir.', 409);
        }


        /**checar si el pago no esta siendo hecho antes de la fecha de la venta */
        if (date('Y-m-d', strtotime(substr($request->fecha_pago, 0, 10))) < date('Y-m-d', strtotime($referencias_adeudos[0]['fecha_operacion']))) {
            return $this->errorResponse('No se pueden registrar pagos con fecha anterior a la fecha de la venta/operación (' . $referencias_adeudos[0]['fecha_operacion_texto'] . ').', 409);
        }







        //operacion_id

        /**se obtienen los valores actualizados con la fecha y cantidades a cubrir */
        /**cantidades enviadas por el usuario para procesar el pago */
        $abono =  $request->abono;
        $intereses = $request->intereses;
        $descuento_pronto_pago =  $request->descuento_pronto_pago;
        $total =  $request->total;
        $pago_con_cantidad =  $request->pago_con_cantidad;
        $cambio_pago =  $request->cambio_pago;
        $monto_pago_parent = $abono - $descuento_pronto_pago; //el pago parent ha registrar, el abono menos el descuento
        /**validando los totales */

        if (($abono - $descuento_pronto_pago + $intereses) != $total) {
            return $this->errorResponse('Hemos encontrado errores en la captura de las cantidades a pagar, por favor verifique y vuelva a intentar.', 409);
        }

        /**verificando que los pagos a distribuir en las referencias sean correctos para poder hacer las insercciones de pagos y distribucion en referencias */
        $abono_a_cubrir = $monto_pago_parent;
        $intereses_a_cubrir = $intereses;
        $descuento_pronto_pago_a_cubrir = $descuento_pronto_pago;
        $array_abonos_cubrir = array();
        $array_intereses_cubrir = array();
        $array_descuento_a_cubrir = array();
        $array_pagos_programados_id = array();

        try {
            DB::beginTransaction();

            try {
                foreach ($request->pagos_a_cubrir as $index_referencia => $referencia) {
                    $encontrada = false;
                    foreach ($referencias_adeudos[0]['pagos_programados'] as $index_programado => $programado) {
                        /**el paog programada se recorre aqui y se compara con las referencias enviadas para pago */
                        if ($programado['referencia_pago'] == $referencia['referencia_pago']) {
                            /**agregando los ids para ver si ya fueron afectadoes en un pago anterior con fechas anteriores y mostrar el error */
                            array_push($array_pagos_programados_id, $programado['id']);
                            /**referencia encontrada */
                            $encontrada = true;
                            /**checando el estatus del pago programada */
                            if ($programado['status_pago'] == 2) {
                                /**el pago ya habia sido saldado, no es habil que venta en la solicitud de pago*/
                                return $this->errorResponse('Ocurrió un error al guardar el pago, uno de los pagos que desea pagar (' . $referencia['referencia_pago'] . ') ya ha sido cubierto previamente. Por favor cierre su aplicación y vuelva a intentar.', 409);
                            } else {
                                /**haciendo los arrays de pagos a distriburir */
                                /**se puede verificar cantidad a pagar del abono */
                                if ($abono_a_cubrir > 0) {
                                    $monto_con_descuento = $programado['monto_programado'];
                                    if ($descuento_pronto_pago > 0) {
                                        $monto_con_descuento = $programado['monto_programado'] - $programado['descuento_pronto_pago'];
                                    }

                                    if ($monto_con_descuento >= $abono_a_cubrir) {
                                        array_push($array_abonos_cubrir, [
                                            'referencia_pago' => $programado['referencia_pago'],
                                            'pagos_programados_id' => $programado['id'],
                                            'monto' => $abono_a_cubrir, 2,
                                            'movimientos_pagos_d' => 1 //abono a capital
                                        ]);
                                        /**se acaba el abono_a_cubrir */
                                        $abono_a_cubrir = 0;
                                    } else {
                                        /**se puede asignar el 100 del pago programado */
                                        array_push($array_abonos_cubrir, [
                                            'referencia_pago' => $programado['referencia_pago'],
                                            'pagos_programados_id' => $programado['id'],
                                            'monto' =>  round($monto_con_descuento, 2),
                                            'movimientos_pagos_d' => 1 //abono a capital
                                        ]);
                                        $abono_a_cubrir -= round($monto_con_descuento, 2);
                                    }
                                }
                                /**verificando la cantidad de intereses moratorios */
                                if ($intereses_a_cubrir > 0) {
                                    if ($programado['intereses'] > 0) {
                                        /**solo si tiene intereses */
                                        if ($programado['intereses'] >= $intereses_a_cubrir) {
                                            array_push($array_intereses_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => $intereses_a_cubrir,
                                                'movimientos_pagos_d' => 2 //abono a intereses
                                            ]);
                                            /**se acaba el intereses_a_cubrir */
                                            $intereses_a_cubrir = 0;
                                        } else {
                                            array_push($array_intereses_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => $programado['intereses'],
                                                'movimientos_pagos_d' => 2 //abono a intereses
                                            ]);
                                            /**se puede asignar el 100 de intereses programados */
                                            $intereses_a_cubrir -= $programado['intereses'];
                                        }
                                    }
                                }

                                /**verificando la cantidad de descuento por pronto pago */
                                if ($descuento_pronto_pago_a_cubrir > 0) {
                                    if ($programado['descuento_pronto_pago'] > 0) {
                                        /**solo si tiene descuento_pronto_pago */
                                        if ($programado['descuento_pronto_pago'] >= $descuento_pronto_pago_a_cubrir) {
                                            array_push($array_descuento_a_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => $descuento_pronto_pago_a_cubrir,
                                                'movimientos_pagos_d' => 3 //descuento por pronto pago
                                            ]);
                                            /**se acaba el des$descuento_pronto_pago_a_cubrir */
                                            $descuento_pronto_pago_a_cubrir = 0;
                                        } else {
                                            array_push($array_descuento_a_cubrir, [
                                                'referencia_pago' => $programado['referencia_pago'],
                                                'pagos_programados_id' => $programado['id'],
                                                'monto' => ($programado['descuento_pronto_pago']),
                                                'movimientos_pagos_d' => 3 //descuento por pronto pago
                                            ]);
                                            /**se puede asignar el 100 de descuento_pronto_pago */
                                            $descuento_pronto_pago_a_cubrir -= $programado['descuento_pronto_pago'];
                                        }
                                    }
                                }
                                break;
                            }
                        } //fin if referencia de pago
                    } //fin foreach de pagros programados


                    if ($encontrada == false) {
                        return $this->errorResponse('Ocurrió un error al guardar el pago, uno de los pagos que desea pagar (' . $referencia['referencia_pago'] . ') no fue econtontrado en la lista de posibles pagos a cubrir según la venta.', 409);
                    }
                } //fin de foreach de pagos a cubrir
            } catch (\Throwable $th) {
                //throw $th;
                return $referencias_adeudos;
            }
            /**al final de distribuir las cantidades es sus respectivos pagos, sedebe verificar que no quedaron montos remanenetes que no se hayan
             * distribuido en sus respectivas referencias
             */
            if ((round($abono_a_cubrir) != 0 || round($intereses_a_cubrir) != 0 || round($descuento_pronto_pago_a_cubrir) != 0)) {
                return $this->errorResponse('Hemos encontrado errores en el registro de este pago debido a que las cantidades ingresadas sobrepasan los adeudos actuales de pagos programados. Por favor vuelva a intentar la operación.', 409);
            }

            //return $array_intereses_cubrir;

            /**antes de insertar el pago se debe de verificar que no existan pagos con fecha superior al pago que se va hacer,
             * puesp eso haria que se perdiera el control sobre el manejo de intereses y descuentos
             */

            $pago_despues_fecha_pago = DB::table('pagos')->select('pagos.id AS pagos_antes_de_fecha')
                ->join('pagos_pagos_programados', 'pagos_pagos_programados.pagos_id', '=', 'pagos.id')
                ->where('pagos.status', '=', 1)
                ->where('pagos.fecha_pago', '>', $request->fecha_pago)
                ->whereIn('pagos_pagos_programados.pagos_programados_id', $array_pagos_programados_id)->get();


            if (count($pago_despues_fecha_pago) > 0) {
                return $this->errorResponse('Hemos detectado que hay pagos con fecha superior a la fecha de pago que desea registrar. No se puede registrar el pago con esta fecha debido a que ya fueron aplicadas las políticas de descuento e intereses en pagos con fechas anteriores.', 409);
            }
            $array_pagos_programados_id;

            $id_abono_capital = DB::table('pagos')->insertGetId(
                [
                    'monto_pago' => $monto_pago_parent,
                    /**solo la cantidad primero que va destinada al pago parent*/
                    'pago_con_cantidad' => $pago_con_cantidad,
                    'cambio_pago' => $cambio_pago,
                    'fecha_registro' => now(),
                    'banco' => $request->banco,
                    'referencia' => $request->referencia_sobre_pago,
                    'fecha_pago' => $request->fecha_pago, //fecha de la venta
                    'registro_id' => (int) $request->user()->id,
                    'cobrador_id' => $request->cobrador['value'],
                    'sat_formas_pago_id' => $request->formaPago['value'],
                    'movimientos_pagos_id' => 1, //abono a capital
                    'nota' => $request->nota,
                    'sat_monedas_id' => $request->moneda['value'],
                    'tipo_cambio' => 1, //1 pesos,
                ]
            );

            /**guardando los de tipo abono a capital */
            foreach ($array_abonos_cubrir as $key => $pago) {
                DB::table('pagos_pagos_programados')->insert(
                    [
                        'pagos_id' => $id_abono_capital,
                        'monto' => $pago['monto'],
                        'pagos_programados_id' => $pago['pagos_programados_id'],
                        'movimientos_pagos_id' => 1, //abono a capital
                    ]
                );
            }
            /**fin de captura de pagos */


            /**guardando children de descuento e intereses */
            $id_descuento_pronto_pago = '';
            if ($descuento_pronto_pago > 0) {
                $id_descuento_pronto_pago = DB::table('pagos')->insertGetId(
                    [
                        'monto_pago' => $descuento_pronto_pago,
                        /**solo la cantidad primero que va destinada al pago parent*/
                        'pago_con_cantidad' => 0,
                        'cambio_pago' => 0,
                        'fecha_registro' => now(),
                        'fecha_pago' => $request->fecha_pago, //fecha de la venta
                        'registro_id' => (int) $request->user()->id,
                        'cobrador_id' => $request->cobrador['value'],
                        'sat_formas_pago_id' => $request->formaPago['value'],
                        'movimientos_pagos_id' => 3, //descuento pronto pago
                        'sat_monedas_id' => $request->moneda['value'],
                        'tipo_cambio' => 1, //1 pesos,
                        'parent_pago_id' => $id_abono_capital
                    ]
                );

                /**guardando los de tipo descuento */
                foreach ($array_descuento_a_cubrir as $key => $descuento) {
                    DB::table('pagos_pagos_programados')->insert(
                        [
                            'pagos_id' => $id_descuento_pronto_pago,
                            'monto' => $descuento['monto'],
                            'pagos_programados_id' => round($descuento['pagos_programados_id'], 2),
                            'movimientos_pagos_id' => 3,  //descuento pronto pago
                        ]
                    );
                }
                //return $array_descuento_a_cubrir;
                /**fin de los de tipo descuento */
            }



            $id_intereses = '';
            if ($intereses > 0) {
                $id_intereses = DB::table('pagos')->insertGetId(
                    [
                        'monto_pago' => $intereses,
                        /**solo la cantidad primero que va destinada al pago parent*/
                        'pago_con_cantidad' => 0,
                        'cambio_pago' => 0,
                        'fecha_registro' => now(),
                        'fecha_pago' => $request->fecha_pago, //fecha de la venta
                        'registro_id' => (int) $request->user()->id,
                        'cobrador_id' => $request->cobrador['value'],
                        'sat_formas_pago_id' => $request->formaPago['value'],
                        'movimientos_pagos_id' => 2, //abono a intereses
                        'sat_monedas_id' => $request->moneda['value'],
                        'tipo_cambio' => 1, //1 pesos,
                        'parent_pago_id' => $id_abono_capital
                    ]
                );
                /**guardando los de tipo intereses */
                foreach ($array_intereses_cubrir as $key => $interes) {
                    DB::table('pagos_pagos_programados')->insert(
                        [
                            'pagos_id' => $id_intereses,
                            'monto' => $interes['monto'],
                            'pagos_programados_id' => $interes['pagos_programados_id'],
                            'movimientos_pagos_id' => 2, //abono a intereses
                        ]
                    );
                }
                /**fin de los de tipo intereses */
            }


            //return $this->errorResponse('todo bien pero sin commit', 409);
            DB::commit();
            return $id_abono_capital;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}