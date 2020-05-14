<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use App\Ajustes;
use App\Clientes;
use Carbon\Carbon;
use App\Propiedades;
use NumerosEnLetras;
use App\PagosTerrenos;
use App\SatFormasPago;
use App\VentasTerrenos;
use App\tipoPropiedades;
use App\AjustesIntereses;
use App\PagosPropiedades;
use App\AntiguedadesVenta;
use App\EmpresaOperaciones;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\PagosProgramadosTerrenos;
use App\ProgramacionPagosTerrenos;
use Illuminate\Support\Facades\DB;
use App\PagosProgramadosPropiedades;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\Foreach_;

class CementerioController extends ApiController
{

    /**CANCELAR LA VENTA */
    public function cancelar_venta(Request $request)
    {
        //return $request->minima_cuota_inicial;
        //validaciones directas sin condicionales
        $datos_venta = $this->get_venta_id($request->venta_id);
        $validaciones = [
            'venta_id' => 'required',
            'motivo.value' => 'required',
            'cantidad' => 'numeric|min:0|' . 'max:' . $datos_venta['total_pagado'],
        ];


        $mensajes = [
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'max' => 'La cantidad a devolver no debe superar a la cantidad abonada hasta la fecha: $ ' . number_format($datos_venta['total_pagado'], 2),
            'min' => 'La cantidad a devolver debe ser mínimo: $ 00.00 Pesos MXN'
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

        if ($datos_venta['status'] != 1) {
            return $this->errorResponse('Esta venta ya habia sido dada de baja.', 409);
        }


        try {
            DB::beginTransaction();

            DB::table('ventas_terrenos')->where('id', $request->venta_id)->update(
                [
                    'motivos_cancelacion_id' => $request['motivo.value'],
                    'fecha_cancelacion' => now(),
                    'cancelo_id' => (int) $request->user()->id,
                    'nota_cancelacion' => $request->comentario,
                    'status' => 0
                ]
            );
            DB::commit();
            return $request->venta_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }




    public function get_cementerio()
    {

        $datos = Propiedades::select(
            '*',
            DB::raw(
                '(NULL) AS nombre_area'
            ),

        )
            ->with('filas_columnas')->with('tipoPropiedad')->with('tipoPropiedad.precios')->with('filas_columnas')->with('ventas.cliente')->orderBy('id', 'asc')->get()->toArray();

        foreach ($datos as $key => &$dato) {
            if ($dato['tipo_propiedades_id'] == 1) {
                /**uniplex */
                $dato['nombre_area'] = 'Sección uniplex ' . $dato['propiedad_indicador'];
            } else  if ($dato['tipo_propiedades_id'] == 2) {
                /**duplex */
                $dato['nombre_area'] = 'Sección duplex ' . $dato['propiedad_indicador'];
            } else  if ($dato['tipo_propiedades_id'] == 3) {
                /**nichos */
                $dato['nombre_area'] = 'nichos columna ' . $dato['propiedad_indicador'];
            } else  if ($dato['tipo_propiedades_id'] == 4) {
                /**terrazas */
                $dato['nombre_area'] = 'Terraza ' . $dato['propiedad_indicador'];
            } else  if ($dato['tipo_propiedades_id'] == 5) {
                /**triplex */
                $dato['nombre_area'] = 'Sección Triplex ' . $dato['propiedad_indicador'];
            } else {
                /**cuadriplez dsin terraza */
                $dato['nombre_area'] = 'Sección de cuadriplex ' . $dato['propiedad_indicador'];
            }

            /**agregando fila, lote, y tipo, por separado en valor numrico */

            foreach ($dato['ventas'] as $key_venta => &$venta) {
                $venta['fila_raw'] = (intval(explode("-", $venta['ubicacion'])[2]));
                $venta['lote_raw'] = (intval(explode("-", $venta['ubicacion'])[3]));
            }
        }


        return $datos;
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
        //return $request->id_cliente;
        //validaciones directas sin condicionales
        $validaciones = [
            //datos de la propiedad
            'tipo_propiedades_id' => 'required|min:1',
            'propiedades_id' => 'required|min:1',
            'ubicacion' => 'required',
            //fin de datos de la propiedad
            //datos de la venta
            'fecha_venta' => 'required|date',
            'ventaAntiguedad.value' => 'required',
            'empresa_operaciones_id' => 'required',
            'filas.value' => 'required',
            'lotes.value' => '', //modificada segun condiciones
            'vendedor.value' => 'required',

            'num_solicitud' => '',
            'convenio' => '',
            'titulo' => '',

            /**id del cliente */
            'id_cliente' => 'required',

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


            //fin de datos de la venta


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
        ];

        /**validando de manera manual si la ubicacion enviada ya esta registrada y esta activa */
        $ubicacion_enviada = VentasTerrenos::where('ubicacion', $request->ubicacion)->where('status', 1)->first();
        if (!empty($ubicacion_enviada)) {
            return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
        }

        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->empresa_operaciones_id == 1 && $request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required';
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasTerrenos::where('numero_titulo', $request->titulo)->where('status', 1)->first();
            if (!empty($titulo)) {
                return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
            }
        }

        //validnado en caso de que sea de uso futuro
        if ($request->empresa_operaciones_id == 2) {
            //venta de uso inmediato
            $validaciones['num_solicitud'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $solicitud = VentasTerrenos::where('numero_solicitud', $request->num_solicitud)->where('status', 1)->first();
            if (!empty($solicitud)) {
                return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
            }


            //valido si es de venta antes del sistema
            if ($request->ventaAntiguedad['value'] == 2) {
                $validaciones['convenio'] = 'required';

                /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
                $convenio = VentasTerrenos::where('numero_convenio', $request->convenio)->where('status', 1)->first();
                if (!empty($convenio)) {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            } else if ($request->ventaAntiguedad['value'] == 3) {
                $validaciones['convenio'] = 'required';
                $validaciones['titulo'] = 'required';

                /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
                $convenio = VentasTerrenos::where('numero_convenio', $request->convenio)->where('status', 1)->first();
                if (!empty($convenio)) {
                    return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
                /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
                $titulo = VentasTerrenos::where('numero_titulo', $request->titulo)->where('status', 1)->first();
                if (!empty($titulo)) {
                    return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                }
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

        /**revisar si el cliente esta vigente para proceder con la venta */
        $cliente = Clientes::where('id', (int) $request->id_cliente)->first();
        if ($cliente->status != 1) {
            /**no esta activo y la venta no puede proceder */
            return $this->errorResponse('Este cliente no se encuentra activo en la base de datos.', 409);
        }

        /**aqui comienzan a gurdar los datos */
        $subtotal = (((float) $request->planVenta['precio_neto'])) * .84; //sin iva
        $iva = (((float) $request->planVenta['precio_neto'])) * .16; //solo el iva
        $descuento = (float) $request->descuento;
        $total_neto = $subtotal + $iva - $descuento;




        //aqui procedemos a guardar segun los datas recibidos


        //if ($request->empresa_operaciones_id == 1 && $request->ventaAntiguedad['value'] == 1) {
        /**********CASO DE VENTA 1 - NUEVA VENTA "SISTEMATIZADA" */
        /**VENTA DE USO INMEDIATO */
        //no se captura ningun numero de referencia, pues el numero de titulo se genera al cubrir la totalidad de la venta
        //return $request->vendedor['value'];
        try {
            DB::beginTransaction();
            $id_venta = 0;
            //venta de uso inmediato y de control sistematizado
            //captura de la venta
            $id_venta = DB::table('ventas_terrenos')->insertGetId(
                [
                    /**venta a futuro solamente */
                    'numero_solicitud' => ($request->empresa_operaciones_id == 2) ? $request->num_solicitud : null,
                    /**venta  liquidada solamente */
                    'numero_convenio' => $this->generarNumeroConvenio($request),
                    'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                    'antiguedad_ventas_id' => (int) $request->ventaAntiguedad['value'],
                    /**la ubicacion consiste de 4 valores id_tipo_propiedad-id_propiedad-fila-lote */
                    /**ejem 4-29-1-3 */
                    'ubicacion' => $request->ubicacion,
                    'propiedades_id' => $request->propiedades_id,
                    'fecha_registro' => now(),
                    'fecha_venta' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                    'registro_id' => (int) $request->user()->id,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'iva' => $iva,
                    'total' => $total_neto,
                    'vendedor_id' => (int) $request->vendedor['value'],
                    'clientes_id' => (int) $request->id_cliente,


                    /** titular_sustituto */
                    'titular_sustituto' => $request->titular_sustituto,
                    'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                    'telefono_titular_sustituto' => $request->telefono_titular_sustituto,

                    //'mensualidades' => $total_neto > 0 ? (int) $request->planVenta['value'] : 0,
                    //'enganche_inicial_plan_origen' => $request->planVenta['enganche_inicial'],
                    'empresa_operaciones_id' => (int) $request->empresa_operaciones_id,
                ]
            );

            /**creamos la primera  progrmacion de pagos (original del contrato)*/
            $programacion_pagos_id = DB::table('programacion_pagos_terrenos')->insertGetId(
                [
                    'num_version' => 1,
                    'fecha_registro' => now(),
                    'mensualidades' => $total_neto > 0 ? (int) $request->planVenta['value'] : 0,
                    'enganche_inicial' => $request->enganche_inicial,
                    'ventas_terrenos_id' => $id_venta,
                ]
            );
            //captura de los beneficiarios
            $this->guardarBeneficiariosVenta($request, $id_venta);

            /**guardando los datos de la tasa para intereses */
            $this->guardarAjustesInteresesVentaTerreno($request, $id_venta);



            /**guardar venta parte final */
            /**captura de pagos */
            $this->programarPagosVenta($request, $id_venta, $programacion_pagos_id, '01');

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






    /**MODIFICAR LA VENTA */
    public function modificar_venta(Request $request)
    {
        //return $request->minima_cuota_inicial;
        //validaciones directas sin condicionales
        $validaciones = [
            'id_venta' => 'required',
            'id_cliente' => 'required',
            //datos de la propiedad

            'tipo_propiedades_id' => 'required|min:1',
            'propiedades_id' => 'required|min:1',
            'ubicacion' => [
                'required'
            ],
            //fin de datos de la propiedad
            //datos de la venta
            'fecha_venta' => 'required|date',
            'ventaAntiguedad.value' => 'required',
            'empresa_operaciones_id' => 'required',
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
        ];

        /**validando de manera manual si la ubicacion enviada ya esta registrada y esta activa */
        $ubicacion_enviada = VentasTerrenos::where('ubicacion', $request->ubicacion)->where('status', 1)->first();
        if (!empty($ubicacion_enviada)) {
            if ($ubicacion_enviada->id != $request->id_venta)
                return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
        }

        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->empresa_operaciones_id == 1 && $request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = [
                'required'
            ];
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasTerrenos::where('numero_titulo', $request->titulo)->where('status', 1)->first();
            if (!empty($titulo)) {
                if ($titulo->id != $request->id_venta)
                    return $this->errorResponse('El número de título seleccionado ya ha sido registrado.', 409);
            }
        }

        //validnado en caso de que sea de uso futuro
        if ($request->empresa_operaciones_id == 2) {
            //venta de uso inmediato
            $validaciones['num_solicitud'] = [
                'required'
            ];

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $solicitud = VentasTerrenos::where('numero_solicitud', $request->num_solicitud)->where('status', 1)->first();
            if (!empty($solicitud)) {
                if ($solicitud->id != $request->id_venta)
                    return $this->errorResponse('El número de solicitud ingresado ya ha sido registrado.', 409);
            }

            //valido si es de venta antes del sistema
            if ($request->ventaAntiguedad['value'] == 2) {
                $validaciones['convenio'] = [
                    'required',
                ];
                $convenio = VentasTerrenos::where('numero_convenio', $request->convenio)->where('status', 1)->first();
                if (!empty($convenio)) {
                    return $this->errorResponse('no.', 409);
                    if ($convenio->id != $request->id_venta)
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
            } else if ($request->ventaAntiguedad['value'] == 3) {
                $validaciones['convenio'] =  [
                    'required'
                ];
                $validaciones['titulo'] =  [
                    'required'
                ];

                /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
                $convenio = VentasTerrenos::where('numero_convenio', $request->convenio)->where('status', 1)->first();
                if (!empty($convenio)) {
                    if ($convenio->id != $request->id_venta)
                        return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
                }
                /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
                $titulo = VentasTerrenos::where('numero_titulo', $request->titulo)->where('status', 1)->first();
                if (!empty($titulo)) {
                    if ($titulo->id != $request->id_venta)
                        return $this->errorResponse('El número de título ingresado ya ha sido registrado.', 409);
                }
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
        /**obtengo los datos originales de la venta para manejar los numeros de solicitud, conveio y titulo segun
         * convenga en cada caso
         */
        $datos_venta = $this->get_venta_id($request->id_venta);

        /*revisando si hubo cambios drasticos para la programacion de los pagos
         * estos cambios son referente a si la fecha de la venta cambio
         * el precio de la propiedad cambio
         * el descuento cambio
         * el plan de venta cambio
         * el tipo de propiedad cambio
         */
        /**veririficando si hubo cambio de fecha de la venta
         * el cambio de venta de fecha solo es posible cuando no hay pagos registrados en la venta
         * lo cual quiere decir que la venta se reajusta nuevamente
         */



        /**esta variable define si al final de la modificacion debe aplicar la reprogrmacion de pagos */
        /**crear reprogrmacion de pagos */
        $correr_reiniciar_programacion_completa = false;
        $generar_nueva_version_programacion_pagos = false;

        /**tratando de modificar la fecha de la venta */
        if (date('Y-m-d', strtotime($request->fecha_venta)) != $datos_venta['fecha_venta']) {
            if ($datos_venta['numero_pagos_realizados'] > 0) {
                //ya tiene varios pagos realizados vigentes, por lo cual no procede la modificacion de la fecha y 
                //la nueva generacion de pagos programados
                return $this->errorResponse('El cambio de fecha no puede proceder, 
            esto se debe a que la venta ya cuenta con uno o más pagos realizados a cuenta de capital. 
            Para poder modificar la fecha de la venta debe cancelar los pagos realizados, y de esta manera poder volver a crear la 
            programación de referencias de pago.', 409);
            } else {
                $correr_reiniciar_programacion_completa = true;
            }
        }



        /**tratando de modificar la ubicacion */


        if ($datos_venta['ubicacion_raw'] != $request->ubicacion) {
            if ($datos_venta['restante_pagar_subtotal'] <= 0) {
                return $this->errorResponse('Esta venta ya fue liquidada en su totalidad, 
                    no se puede hacer cambio de propiedad en este caso según las 
                    políticas de la empresa.', 409);
            } else
            if ($datos_venta['restante_pagar_subtotal'] <= 0) {
                return $this->errorResponse('Esta venta ya fue liquidada en su totalidad, 
                    no se puede hacer cambio de propiedad en este caso según las 
                    políticas de la empresa.', 409);
            } else if ($datos_venta['pagos_vencidos'] > 0) {
                return $this->errorResponse('Solicitud rechazada, al parecer esta venta no está al corriente con sus pagos. Para cualquier aclaración, consulte el estado de cuenta de esta venta.', 409);
            } else {
                $correr_reiniciar_programacion_completa = true;
            }
        }


        /**revisnado si el plan de ventas es diferente */
        if ($request->planVenta['value'] != $datos_venta['programacion_pagos'][0]['mensualidades']) {
            /**aqui la venta fue modificada a otro tipo de plan */
            /**verificando que la venta no tenga pagos vencidos */
            if ($datos_venta['pagos_vencidos'] > 0) {
                return $this->errorResponse('Solicitud rechazada, no se puede hacer cambio de plan de venta porque al parecer esta venta no está al corriente con sus pagos. Para cualquier aclaración, consulte el estado de cuenta de esta venta.', 409);
            } else if ($datos_venta['restante_pagar'] <= 0) {
                return $this->errorResponse('Esta venta ya fue liquidada en su totalidad, 
                    no se puede hacer cambio de propiedad en este caso según las 
                    políticas de la empresa.', 409);
            } else {
                /**se genera una nueva version de la progrmacion de pagos y se rellena con los pagos que
                 * ya se han hecho hasta la fecha
                 */
                $generar_nueva_version_programacion_pagos = true;
            }
        }


        /**checando (cambio de precio de la propiedad) si lo ya pagado hasta la fecha no supera el nuevo precio de la venta
         * pues al ser mayor lo que ya se pagó, al llenar los nuevos pagos generados nos quedaria dinero de mas
         * y nos causaria dinero de mas
         */

        if ($total_neto != $datos_venta['total'] || $descuento != $datos_venta['descuento'] ||  (float) $request->enganche_inicial != $datos_venta['programacion_pagos'][0]['enganche_inicial']) {

            /**esta validacion queda pendiente ya que lamgerente quiere poder tener la libertad de cambiar los precios
             * segun los meses a que se haya pagala venta al final
             * deshabilite la vliadacion de pagosVencidos en l frontend de esta funcion
             */
            /*if ($datos_venta['pagos_vencidos'] > 0) {
                return $this->errorResponse('Solicitud rechazada, no se puede hacer cambio de plan de venta porque al parecer esta venta no está al corriente con sus pagos. Para cualquier aclaración, consulte el estado de cuenta de esta venta.', 409);
            } else*/
            if ($datos_venta['restante_pagar_subtotal'] <= 0) {
                return $this->errorResponse('Esta venta ya fue liquidada en su totalidad, 
                    no se puede hacer modificaciones en las cantidades relativas al precio.ok', 409);
            } else if ($datos_venta['total_pagado'] >  $total_neto) {
                /**el nuevo precio (subtotal a pagar es mayor y no debe proceder la venta) */
                return $this->errorResponse('Solicitud rechazada, no se puede hacer cambio de plan de venta porque 
                    el total que ya se ha cubierto de esta venta (' . number_format($datos_venta['total_pagado'], 2) . ' Pesos MXN) supera al nuevo total a pagar (' . number_format($total_neto, 2) . ' Pesos MXN). 
                    Para cualquier aclaración, consulte el estado de cuenta de esta venta.', 409);
            } else {
                $generar_nueva_version_programacion_pagos = true;
            }
        }


        $numero_titulo_original = $datos_venta['numero_titulo_raw'];
        if ($request->ventaAntiguedad['value'] == 3) {
            $numero_titulo_original =   $request->titulo;
        }
        /**fin de validacion de fecha de venta */
        $numero_convenio_original = $datos_venta['numero_convenio_raw'];


        try {
            DB::beginTransaction();
            //venta de uso inmediato y de control sistematizado
            //captura de la venta
            DB::table('ventas_terrenos')->where('id', $request->id_venta)->update(
                [
                    'clientes_id' => (int) $request->id_cliente,
                    /**venta a futuro solamente */
                    'numero_solicitud' => ($request->empresa_operaciones_id == 2) ? $request->num_solicitud : null,
                    'numero_convenio' => $request->ventaAntiguedad['value'] > 1 ? $request->convenio : $numero_convenio_original,
                    /**venta  liquidada solamente */
                    'numero_titulo' => $numero_titulo_original,
                    /**la ubicacion consiste de 4 valores id_tipo_propiedad-id_propiedad-fila-lote */
                    /**ejem 4-29-1-3 */
                    'ubicacion' => $request->ubicacion,
                    'propiedades_id' => $request->propiedades_id,
                    'fecha_modificacion' => now(),
                    'fecha_venta' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                    'modifico_id' => (int) $request->user()->id,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'iva' => $iva,
                    'total' => $total_neto,
                    'vendedor_id' => (int) $request->vendedor['value'],
                    //agregar'tel_oficina' => $request->ubicacion,
                    'titular_sustituto' => $request->titular_sustituto,
                    'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                    'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                    //'mensualidades' => (int) $request->planVenta['value'],
                    //'enganche_inicial_plan_origen' => $request->planVenta['enganche_inicial'],
                    'empresa_operaciones_id' => (int) $request->empresa_operaciones_id,
                ]
            );

            //captura de los beneficiarios
            $this->guardarBeneficiariosVenta($request,  $request->id_venta);



            if ($generar_nueva_version_programacion_pagos == true || $correr_reiniciar_programacion_completa == true) {
                /**creamos la nueva  progrmacion de pagos (original del contrato)*/
                $version_anterior = intval($datos_venta['programacion_pagos'][0]['num_version']);
                $programacion_pagos_id = DB::table('programacion_pagos_terrenos')->insertGetId(
                    [
                        'num_version' => ($version_anterior + 1),
                        'fecha_registro' => now(),
                        'mensualidades' => $total_neto > 0 ? (int) $request->planVenta['value'] : 0,
                        'enganche_inicial' => $request->enganche_inicial,
                        'ventas_terrenos_id' =>  $request->id_venta,
                    ]
                );
                /**verificando que no pase de las programaciones permitidas (10 diez) */
                if (($version_anterior + 1) > 3) {
                    return $this->errorResponse('Solicitud rechazada, esta venta ha cambiado la programación de pagos más de 3 (tres veces).', 409);
                }



                $version_programacion = ('0' . ($version_anterior + 1));
                $this->programarPagosVenta($request, $request->id_venta, $programacion_pagos_id, $version_programacion);

                /**rellenar con los pagos actuales realizados */
                $datos_nueva_programacion = $this->get_venta_id($request->id_venta);
                $total_a_repagar = $datos_venta['total_pagado'];
                foreach ($datos_nueva_programacion['programacion_pagos'][0]['pagos_programados'] as $programado) {
                    if ($total_a_repagar > 0) {
                        if ($total_a_repagar >= $programado['total']) {
                            $subtotal = $programado['subtotal']; //sin iva
                            $iva = $programado['iva']; //solo el iva
                            $descuento =  $programado['descuento'];
                            $total_neto = $subtotal + $iva - $descuento;
                            /**restamos lo que se repago */
                            $total_a_repagar -= ($programado['total']);
                        } else {
                            /**el nuevo precio (subtotal a pagar es mayor y no debe proceder la venta) */

                            /**obtener el porcentaje del descuento */
                            $porcentaje_a_cubrir = (($total_a_repagar * 100) / $programado['total']);


                            /**se debe tomar porcentaje del total para los siguientes valores */
                            $subtotal = ($porcentaje_a_cubrir * $programado['subtotal']) / 100; //sin iva
                            $iva = ($porcentaje_a_cubrir * $programado['iva']) / 100; //solo el iva
                            $descuento =  ($porcentaje_a_cubrir * $programado['descuento']) / 100;
                            $total_neto = $subtotal + $iva - $descuento;
                            /**se acaba lo que se va repagar */
                            $total_a_repagar = 0;
                        }
                        DB::table('pagos_terrenos')->insert(
                            [
                                'pagos_programados_terrenos_id' => $programado['id'],
                                'subtotal' => $subtotal,
                                'iva' => $iva,
                                'descuento' => $descuento,
                                'total' => $total_neto,
                                'fecha_pago' => now(), //fecha de la venta
                                'fecha_registro' =>  now(), //fecha de la venta
                                'registro_id' => (int) $request->user()->id,
                                'cobrador_id' => (int) $request->user()->id,
                                'tipo_pagos_id' => 3, //abono a capital
                                'sat_formas_pago_id' => 6, //remision de deuda la forma pago del sat
                            ]
                        );
                    } else {

                        //ya se ha repagado todo nuevamente
                        break;
                    }
                }
                /**fin modificar */
            }

            /**fin de captura de pagos */
            DB::commit();
            return $request->id_venta;
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
                $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_convenio NOT LIKE '%[^0-9]%' THEN numero_convenio END) as int)) AS max_numero_convenio  from ventas_terrenos where antiguedad_ventas_id=1"));
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
            $pagos_venta = VentasTerrenos::with(['programacionPagos.pagosProgramados.pagosRealizados' => function ($q) {
                $q->where('status', '=', 1);
            }])->find($id_venta)->toArray();

            //debemos verificar que la venta no sea liquidada antes del sistema ps deberia llevar el titulo asigando por el usuario y no el calculado
            if ($pagos_venta['antiguedad_ventas_id'] != 3) {
                //la venta no fue hecha y liquidada antes del sistema
                //return $pagos_venta['pagos_programados'];
                $total_pagado = 0;
                foreach ($pagos_venta['programacion_pagos'][0]['pagos_programados'] as $pago_programado) {
                    foreach ($pago_programado['pagos_realizados'] as $pago_realizado) {
                        if ($pago_realizado['status']) {
                            $total_pagado += $pago_realizado['subtotal'];
                            /**el subtotal no se ve afectado por descuento y se puede deducir en caso de que se apliquien deswcuento */
                        }
                    }
                }

                $numero_titulo = NULL;


                //checando si la suma de pagos es igual al total de la venta para generale un numero de titulo por haber cubierto la deuda
                if ($total_pagado == $pagos_venta['subtotal']) {
                    //venta cubierta el 100%
                    //500 (quinientos)
                    //determino si ya esta en funcion la asignacion de numeros de titulos automaticos
                    $ajustes = Ajustes::first();
                    if ($ajustes->numero_titulos_sistematizados == true) {
                        //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                        $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_titulo NOT LIKE '%[^0-9]%' THEN numero_titulo END) as int)) AS max_numero_titulo  from ventas_terrenos"));
                        $ultimo_titulo = json_decode(json_encode($result), true)[0]['max_numero_titulo'];
                        if (intval($ultimo_titulo) > 0) {
                            $numero_titulo = $ultimo_titulo + 1;
                        } else {
                            $numero_titulo = 500;
                        }
                    } else {
                        //comenzamos en numero 500 (quinientos) y marcamos numero_titulos_sistematizados como true en la base de datos
                        $ajustes->numero_titulos_sistematizados = true;
                        $ajustes->timestamps = false;
                        $ajustes->save();
                        $numero_titulo = 500;
                    }
                } else {
                    /**se remueve el numero de titulo sis existe ya que no esta 100 pagado */
                    //$numero_titulo = null;
                }
                $venta = VentasTerrenos::find($id_venta);
                //actualizamos la venta con su nuevo numero de titulo
                $venta->numero_titulo = $venta->numero_titulo != NULL ? $venta->numero_titulo : $numero_titulo;
                $venta->timestamps = false;
                $venta->save();
            }
        }
    }

    public function guardarAjustesInteresesVentaTerreno(Request $request, $id_venta = 0)
    {
        /**aqui obtengo el plan de intereses con que funcionara esta venta */
        $ajustes_intereses = AjustesIntereses::find(1);
        /**hago un registro con la informacion que afectara el control de intereses para esta venta */
        DB::table('venta_terrenos_ajustes_intereses')->insert(
            [
                'tasa_fija_anual' => $ajustes_intereses->tasa_fija_anual,
                'dias_antes_vencimiento' => $ajustes_intereses->dias_antes_vencimiento,
                'maximo_dias_retraso' => $ajustes_intereses->maximo_dias_retraso,
                'porcentaje_pena_convencional_minima' => $ajustes_intereses->porcentaje_pena_convencional_minima,
                'minima_partes_cubiertas' => $ajustes_intereses->minima_partes_cubiertas,
                'maximo_pagos_vencidos' => $ajustes_intereses->maximo_pagos_vencidos,
                'ventas_terrenos_id' => $id_venta
            ]
        );
    }

    //guarda los beneficiarios de la venta de una propiedad
    public function guardarBeneficiariosVenta(Request $request, $id_venta = 0)
    {

        /**primero elimino beneficiarios si existen, de esta forma
         * la funcion me sirve perfecto tanto para insertar beneficiarios y actualizarlos
         */
        DB::table('beneficiarios_terrenos')->where('ventas_terrenos_id', $id_venta)->delete();

        //id del conjunto de propieades
        for ($i = 0; $i < count($request['beneficiarios']); $i++) {
            DB::table('beneficiarios_terrenos')->insert(
                [
                    'nombre' => $request['beneficiarios'][$i]['nombre'],
                    'parentesco' => $request['beneficiarios'][$i]['parentesco'],
                    'telefono' => $request['beneficiarios'][$i]['telefono'],
                    'ventas_terrenos_id' => $id_venta,
                ]
            );
        }
    }



    //guarda los beneficiarios de la venta de una propiedad
    public function programarPagosVenta(Request $request, $id_venta = 0, $programacion_pagos_id = 0, $num_version_programacion = '')
    {
        /**aqui comienzan a gurdar los datos*/
        $subtotal = (((float) $request->planVenta['precio_neto'])) * .84; //sin iva
        $iva = (((float) $request->planVenta['precio_neto'])) * .16; //solo el iva
        $descuento = (float) $request->descuento;
        $total_neto = $subtotal + $iva - $descuento;
        //verificando si la venta viene con algun descuento
        /**como se genera la referencia del pago para realizar pago en bancos */
        //se compone de la referencia de la venta segun el tipo de venta que es, la fecha
        //empresa_operaciones_id
        /**asi se compone una referencia para un pago */
        /**
         * se compone de la clave de referencia del tipo de venta segun la tabla ventas_referencias (3 digitos)
         * 2(dos) digitos de la version de la programacion de pagos(01,02,03,04, etc.)
         * fecha programada del pago(8 digitos)
         * numero de pago 01,02,12,18,24,32,maximo son 64 etc. (2 digitos)
         * id de la venta, puede ir desde los 4 hasta los 5 digitos
         * ejemplo de una referencia
         * 0010120200411011  // venta de propiedad de uso inmediato, fecha 11 de abril 2020, pago 01 y venta id 1
         */

        /**se obtiene la referencia segun la operacion de la empresa */
        $empresa_operacion = EmpresaOperaciones::where('id', $request->empresa_operaciones_id)->first();

        //puede que venga con descuento pero no es del 100%
        //determinamos que tipo de ventas
        if ($request->empresa_operaciones_id == 1 || (int) $request->planVenta['value'] == 0) {
            //de uso inmediato sin importar si es seleccionado a futuro o inmediato ya que selecciono pagarlo de contado
            /**se crea un solo pago */
            //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(10, 'day');
            $id_pago_programado_unico = DB::table('pagos_programados_terrenos')->insertGetId(
                [
                    'num_pago' => 1, //numero 1, pues es unico
                    'fecha_programada' => $fecha_maxima, //fecha de la venta
                    'conceptos_pagos_id' => 3, //3-liquidacion //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                    'referencia_pago' => $empresa_operacion->referencia_pago . $num_version_programacion . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'descuento' => $descuento,
                    'total' => $total_neto,
                    'programacion_id' => $programacion_pagos_id
                ]
            );
            //viendo si quiere registrar el abono inicial desde la venta
            if ($request->opcionPagar['value'] == 1 || $total_neto == 0) {
                //quiere registrar el enganche inicial, osea el valor de la propiedad de una vez
                DB::table('pagos_terrenos')->insert(
                    [
                        'pagos_programados_terrenos_id' => $id_pago_programado_unico,
                        'subtotal' => $subtotal,
                        'iva' => $iva,
                        'descuento' => $descuento,
                        'total' => $total_neto,
                        'fecha_pago' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)), //fecha de la venta
                        'fecha_registro' => now(), //fecha de la venta
                        'registro_id' => (int) $request->user()->id,
                        'cobrador_id' => (int) $request->vendedor['value'],
                        'tipo_pagos_id' => 1, //abono a capital
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

            $porcentaje_enganche_inicial = (float) $request->precio_neto > 0 ? (($enganche_incial * 100) / (float) $request->precio_neto) : 0;
            /**obtengo el porcentaje que le corresponde a esos pagos segun el plan de venta */
            $porcentaje_resto_a_mensualidades = (100 - $porcentaje_enganche_inicial) / (int) $request->planVenta['value'];

            $sub_total_pago_enganche_sin_descuento = ((float) $request->enganche_inicial) + (($descuento * $porcentaje_enganche_inicial) / 100);

            //enganche inicial mandado mas lo descontado para sacar impuestos completos
            $subtotal_enganche = $sub_total_pago_enganche_sin_descuento * .84;
            $iva_enganche = $sub_total_pago_enganche_sin_descuento * .16;
            $descuento_enganche = ($descuento * $porcentaje_enganche_inicial) / 100;

            $total_enganche = $subtotal_enganche + $iva_enganche - $descuento_enganche;
            //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(10, 'day');

            if ($total_neto > 0) {
                /**si la venta no fue con 100% de descuento,
                 * se registra el enganche 
                 * en caso contrario que el enganche es 0(cero), no lo registro
                 * solo los abonos
                 */
                $id_pago_programado_enganche = DB::table('pagos_programados_terrenos')->insertGetId(
                    [
                        'num_pago' => 1, //numero 1, pues es enganche
                        'fecha_programada' => $fecha_maxima, //fecha de la venta
                        'conceptos_pagos_id' => 1, //1-enganche //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                        'referencia_pago' =>  $empresa_operacion->referencia_pago . $num_version_programacion
                            /**tipo 02 por ser a meses */
                            . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'subtotal' => $subtotal_enganche,
                        'iva' => $iva_enganche,
                        'descuento' => $descuento_enganche,
                        'total' => $total_enganche,
                        'programacion_id' => $programacion_pagos_id
                    ]
                );



                /**verifico si pago el enganchde desde la venta */
                if ($request->opcionPagar['value'] == 1) {
                    //quiere registrar el enganche inicial, osea el valor de la propiedad de una vez
                    DB::table('pagos_terrenos')->insert(
                        [
                            'pagos_programados_terrenos_id' => $id_pago_programado_enganche,
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
                            'tipo_pagos_id' => 1, //abono a capital
                        ]
                    );
                }
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
                $id_pago_programado = DB::table('pagos_programados_terrenos')->insertGetId(
                    [
                        'num_pago' => ($i + 1), //numero 1, pues es enganche
                        'fecha_programada' => $fecha, //fecha de la venta
                        'conceptos_pagos_id' => 2, //2-enganche //que tipo de pago es, segun los tipos de pago, abono, enganche o liquidacion
                        'referencia_pago' =>  $empresa_operacion->referencia_pago . $num_version_programacion
                            /**tipo 02 por ser a meses */
                            . date('Ymd', strtotime($request->fecha_venta)) . $numero_pago_para_referencia . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'subtotal' => $subtotal_pago,
                        'iva' => $iva_pago,
                        'descuento' => $descuento_pago,
                        'total' => $total_pago,
                        'programacion_id' => $programacion_pagos_id
                    ]
                );

                /**en caso de ser pago 100% gratis hacemos un foreach para hacer los rellenar los pagos en automatico y crear el titulo */

                if ($total_neto == 0) {
                    //este es un pago especial
                    //la venta tiene 100% de descuento
                    //sin importar el plan de venta solo se programara un solo pago y se registrará el pago automaticmante con el la forma de pago del sat
                    //clave 25, remision de deuda

                    //se paga automaticamente este tipo de ventas

                    DB::table('pagos_terrenos')->insertGetId(
                        [
                            'pagos_programados_terrenos_id' => $id_pago_programado,
                            'subtotal' => $subtotal_pago,
                            'iva' => $iva_pago,
                            'descuento' => $descuento_pago,
                            'total' => $total_pago,
                            'fecha_pago' => $fecha, //fecha de la venta
                            'fecha_registro' =>  now(), //fecha de la venta
                            'registro_id' => (int) $request->user()->id,
                            'cobrador_id' => (int) $request->user()->id,
                            'tipo_pagos_id' => 3, //abono a capital
                            'sat_formas_pago_id' => 6, //remision de deuda la forma pago del sat
                        ]
                    );
                }
            }
            if ($total_neto == 0) {
                //se corre el proceso para ver si ya esta liquidada la venta y generar el numero de titulo
                $this->generarNumeroTitulo($id_venta);
            }
        }
    }





    public function propiedadesById(Request $request)
    {
        //id del conjunto de propieades
        $id_propiedad = $request->id_propiedad;

        $datos = Propiedades::select(
            '*',
            DB::raw(
                '(NULL) AS nombre_area'
            )
        )
            ->with('filas_columnas')->with('tipoPropiedad')->orderBy('tipo_propiedades_id', 'asc')->where('propiedades.id', '=', $id_propiedad)->get()->toArray();

        if ($datos[0]['tipo_propiedades_id'] == 1) {
            /**uniplex */
            $datos[0]['nombre_area'] = 'Sección uniplex ' . $datos[0]['propiedad_indicador'];
        } else  if ($datos[0]['tipo_propiedades_id'] == 2) {
            /**duplex */
            $datos[0]['nombre_area'] = 'Sección duplex ' . $datos[0]['propiedad_indicador'];
        } else  if ($datos[0]['tipo_propiedades_id'] == 3) {
            /**nichos */
            $datos[0]['nombre_area'] = 'nichos columna ' . $datos[0]['propiedad_indicador'];
        } else  if ($datos[0]['tipo_propiedades_id'] == 4) {
            /**terrazas */
            $datos[0]['nombre_area'] = 'Terraza ' . $datos[0]['propiedad_indicador'];
        } else  if ($datos[0]['tipo_propiedades_id'] == 5) {
            /**triplex */
            $datos[0]['nombre_area'] = 'Sección Triplex ' . $datos[0]['propiedad_indicador'];
        } else {
            /**cuadriplez dsin terraza */
            $datos[0]['nombre_area'] = 'Sección de cuadriplex ' . $datos[0]['propiedad_indicador'];
        }

        return $datos;
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




    /**obtiene todas las ventas en bruto */
    public function get_ventas_todas(Request $request)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular = $request->titular;
        $numero_control = $request->numero_control;
        $status = $request->status;

        $datos =  VentasTerrenos::select(
            'clientes_id',
            'clientes.nombre as cliente_nombre',
            'clientes.email as cliente_email',
            'clientes.direccion as cliente_direccion',
            'clientes.ciudad as cliente_ciudad',
            'clientes.estado as cliente_estado',

            'clientes.telefono as cliente_telefono',
            'clientes.celular as cliente_celular',
            'clientes.telefono_extra as cliente_telefono_extra',
            'clientes.rfc as cliente_rfc',
            'clientes.fecha_nac as cliente_fecha_nac',
            'titular_sustituto',
            'parentesco_titular_sustituto',
            'telefono_titular_sustituto',
            'ventas_terrenos.fecha_registro',
            'ventas_terrenos.status',
            'ventas_terrenos.id',
            'numero_solicitud',
            'numero_convenio',
            'numero_titulo',
            'numero_solicitud AS numero_solicitud_raw',
            'numero_convenio as numero_convenio_raw',
            'numero_titulo as numero_titulo_raw',
            'ubicacion as ubicacion_raw',
            'fecha_venta',
            'ventas_terrenos.fecha_registro',
            'total',
            'subtotal',
            'descuento',
            'iva',
            'ventas_terrenos.status',
            'antiguedad_ventas_id',
            'vendedor_id',
            'empresa_operaciones_id',
            DB::raw(
                '(NULL) AS tipo_propiedad_des'
            ),
            DB::raw(
                '(NULL) AS tipo_propiedad_capacidad'
            ),
            DB::raw(
                '(NULL) AS intereses_generados'
            ),
            DB::raw(
                '(NULL) AS intereses_pagados'
            ),
            DB::raw(
                '(NULL) AS sub_total_pagado'
            ),
            DB::raw(
                '(NULL) AS iva_pagado'
            ),
            DB::raw(
                '(NULL) AS descuento_pagado'
            ),
            DB::raw(
                '(NULL) AS total_pagado'
            ),
            DB::raw(
                '(NULL) AS restante_pagar_subtotal'
            ),
            DB::raw(
                '(NULL) AS saldo_pagar_neto_con_intereses'
            ),
            DB::raw(
                '(NULL) AS total_pagar_neto_con_intereses'
            ),
            DB::raw(
                '(0) AS pagos_vencidos'
            ),
            DB::raw(
                '(0) AS dias_vencidos'
            ),
            DB::raw(
                '(0) AS numero_pagos_programados'
            ),
            DB::raw(
                '(0) AS numero_pagos_programados_cubiertos'
            ),
            DB::raw(
                '(0) AS numero_pagos_realizados'
            ),

            DB::raw(
                '(NULL) AS tipo_raw'
            ),
            DB::raw(
                '(NULL) AS id_propiedad_raw'
            ),
            DB::raw(
                '(NULL) AS fila_raw'
            ),
            DB::raw(
                '(NULL) AS lote_raw'
            ),
            DB::raw(
                '(CASE 
                        WHEN ventas_terrenos.empresa_operaciones_id = "1" THEN "Inmediato"
                        ELSE "A futuro" 
                        END) AS uso_venta'
            ),
            DB::raw(
                '(CASE 
                        WHEN ventas_terrenos.numero_solicitud <> "" THEN ventas_terrenos.numero_solicitud
                        ELSE "N/A" 
                        END) AS numero_solicitud'
            ),
            DB::raw(
                '(CASE 
                        WHEN ventas_terrenos.numero_convenio <> "" THEN ventas_terrenos.numero_convenio
                        ELSE "N/A" 
                        END) AS numero_convenio'
            ),
            DB::raw(
                '(CASE 
                        WHEN ventas_terrenos.numero_titulo <> "" THEN ventas_terrenos.numero_titulo
                        ELSE "Pendiente" 
                        END) AS numero_titulo'
            ),
            DB::raw(
                '"" as ubicacion_texto'
            ),
            DB::raw(
                '"" as tipo_texto'
            ),
            DB::raw(
                '"" as fila_texto'
            ),
            DB::raw(
                '"" as area_nombre'
            ),
            DB::raw(
                '"" as lote_texto'
            ),
            DB::raw(
                '(CASE 
                        WHEN ventas_terrenos.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_des'
            )
        )

            ->with(array('programacionPagosActual.pagosProgramados.conceptoPago'))
            ->with(array('programacionPagosActual.pagosProgramados.pagosRealizados'))
            ->with(array('vendedor' => function ($query) {
                $query->select('id', 'nombre');
            }))
            ->with(
                'beneficiarios'
            )
            ->with(
                'antiguedad'
            )
            ->with(
                'ajustesIntereses'
            )

            ->where(function ($q) use ($status) {
                if ($status != '') {
                    $q->where('ventas_terrenos.status', $status);
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('ventas_terrenos.numero_solicitud', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        /**filtro por numero de solicitud */
                        $q->where('ventas_terrenos.numero_convenio', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 3) {
                        /**filtro por numero de solicitud */
                        $q->where('ventas_terrenos.numero_titulo', '=',  $numero_control);
                    } else {
                        /**filtro por numero de solicitud */
                        $q->where('ventas_terrenos.id', $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($titular) {
                if (trim($titular) != '') {
                    $q->where('clientes.nombre', 'like', '%' . $titular . '%');
                }
            })

            ->join('clientes', 'ventas_terrenos.clientes_id', '=', 'clientes.id')
            ->orderBy('ventas_terrenos.id', 'desc')
            ->get();

        $resultado = $datos->toArray();




        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        $datos_cementerio = $this->get_cementerio();
        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */

        //**se actualiza la propiedad a formato legible para el usuario */

        foreach ($resultado as $key_data => &$venta) {
            $venta['ubicacion_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['ubicacion_texto'];
            $venta['area_nombre'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['area_nombre'];
            $venta['tipo_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['tipo_texto'];
            $venta['fila_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['fila_texto'];
            $venta['lote_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['lote_texto'];

            /**agregando fila, lote, y tipo, por separado en valor numrico */
            $venta['tipo_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[0]));
            $venta['id_propiedad_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[1]));
            $venta['fila_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[2]));
            $venta['lote_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[3]));

            /**seleccionado el tipo de propiedad */
            $tipo_propiedad = tipoPropiedades::where('id', $venta['tipo_raw'])->first();
            $venta['tipo_propiedad_des'] = $tipo_propiedad['tipo'];
            $venta['tipo_propiedad_capacidad'] = $tipo_propiedad['capacidad'];

            /**obteniendo el num de pagos realizados y vigentes */

            $sub_total_pagado = 0;
            $iva_pagado = 0;
            $descuento_pagado = 0;
            $total_pagado = 0;


            $pagados = 0;
            $vencidos = 0;
            $pagos_realizados = 0;
            /**calculando el monto de interes que debe la persona */
            $ajustes_intereses = $venta['ajustes_intereses'];

            /**fecha del primer pago vencido para scar la diferencia */
            $fecha_primer_pago_vencido = '';

            $intereses_generados = 0;
            $intereses_pagados = 0;


            if (isset($venta['programacion_pagos'][0]['pagos_programados'])) {
                foreach ($venta['programacion_pagos'][0]['pagos_programados'] as &$programado) {
                    /**definiendo el conceptop del pago */
                    if ($programado['concepto_pago']['id'] == 1) {
                        /**enganche */
                        $programado['concepto'] = "Enganche inicial.";
                    } else if ($programado['concepto_pago']['id'] == 2) {
                        /**abono */
                        $programado['concepto'] = "Abono Núm." . ($programado['num_pago'] - 1) . " correspondiente al mes de " . mes_year_from_fecha($programado['fecha_programada']) . ".";
                    } else {
                        /**liquidacion */
                        $programado['concepto'] = "Pago único de liquidación.";
                    }

                    /**valores por default */
                    $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                    $programado['total_a_pagar'] = $programado['total'];
                    $cubierto = 0;
                    $vencido = 0;

                    /**manejo de intereses */
                    $dias_retrasados_del_pago = 0;
                    $interes_a_pagar = 0;
                    /**veririca si el pago vencio y no se ha pagado ndd*/
                    if (count($programado['pagos_realizados']) == 0) {
                        if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                            $vencidos++;
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                            $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                            $programado['fecha_a_pagar'] = date('Y-m-d');
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             **/
                            $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */

                            $programado['total_a_pagar'] = doubleVal($programado['total']) + $interes_a_pagar;

                            $programado['intereses_a_pagar'] = $interes_a_pagar;

                            $programado['vencido'] = 1;
                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                            $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                            $programado['intereses'] = $interes_a_pagar;

                            if ($fecha_primer_pago_vencido == '') {
                                $fecha_primer_pago_vencido = $programado['fecha_programada'];
                            }
                        }
                    } else {
                        /**hay pagos realizados */
                        $monto_pagado_interes = 0;
                        $fecha_ultimo_pago_realizado = '';
                        foreach ($programado['pagos_realizados'] as $realizado) {
                            if ($realizado['status'] == 1) {
                                $pagos_realizados++;
                                /***sacando monto pagado a cuenta de intereses */
                                if ($realizado['tipo_pagos_id'] == 2) {
                                    /**es abono a intereses */
                                    /**me baso en subtotal */
                                    $monto_pagado_interes += (doubleVal($realizado['total']));
                                    $programado['intereses_pagado'] += (doubleVal($realizado['total']));
                                } else {
                                    /**total cuvierto del pago */
                                    $programado['subtotal_pagado'] += (doubleVal($realizado['subtotal']));
                                    $programado['iva_pagado'] += (doubleVal($realizado['iva']));
                                    $programado['descuento_pagado'] += (doubleVal($realizado['descuento']));
                                    $programado['total_pagado'] += (doubleVal($realizado['total']));


                                    /**es abono a capital */
                                    $sub_total_pagado += (doubleVal($realizado['subtotal']));
                                    $iva_pagado +=  (doubleVal($realizado['iva']));
                                    $descuento_pagado += (doubleVal($realizado['descuento']));
                                    $total_pagado += (doubleVal($realizado['total']));
                                    $cubierto += (doubleVal($realizado['subtotal']));
                                }

                                $next = next($realizado);
                                if (false !== $next) {
                                    $fecha_ultimo_pago_realizado = $realizado['fecha_pago'];
                                    //do something with $current
                                }
                            }
                        }
                        if ($cubierto >= $programado['subtotal']) {
                            /**checar si se pago el abono dentro de la fecha limite */
                            if (strtotime($fecha_ultimo_pago_realizado) <= strtotime($programado['fecha_programada'])) {
                                /**pago cubierto en fecha y orden */
                                $programado['intereses_a_pagar'] = 0;
                                $programado['total_a_pagar'] = 0;
                                $programado['pagado'] = 1;
                                $pagados++;
                                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                            } else {
                                /**checando si el monto de los intereses pagados corresponde a los dias vencidos */
                                $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                                $fecha_pago_realizado = Carbon::createFromFormat('Y-m-d', $fecha_ultimo_pago_realizado);
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_pago_realizado);
                                /**
                                 * Los intereses moratorios se calcularán
                                 * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                                 * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                                 * ser hecho y la fecha que el contratante
                                 * liquide el adeudo.
                                 */
                                $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;
                                if ($interes_a_pagar < $monto_pagado_interes) {
                                    if ($fecha_primer_pago_vencido == '') {
                                        $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                    }
                                    $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                                    $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];
                                    $vencidos++;
                                    $programado['vencido'] = 1;
                                    /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                    $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                    $programado['intereses'] = $interes_a_pagar;
                                    $programado['fecha_a_pagar'] = date('Y-m-d');
                                } else {
                                    $programado['fecha_a_pagar'] = $fecha_pago_realizado;
                                    /**pago cubierto en fecha y orden */
                                    $programado['pagado'] = 1;
                                    /**el monto es igual o mayor */
                                    $pagados++;
                                }
                            }
                        } else {
                            if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                                $vencidos++;
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                                $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                                $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                /**
                                 * Los intereses moratorios se calcularán
                                 * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                                 * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                                 * ser hecho y la fecha que el contratante
                                 * liquide el adeudo.
                                 */
                                $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                                $programado['vencido'] = 1;
                                /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_a_pagar;

                                $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                                $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];

                                if ($fecha_primer_pago_vencido == '') {
                                    $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                }
                            }
                        }
                    }

                    $intereses_generados += $interes_a_pagar;
                    $intereses_pagados += $programado['intereses_pagado'];
                }

                $venta['intereses_generados'] =  $intereses_generados;
                $venta['intereses_pagados'] =  $intereses_pagados;


                $venta['numero_pagos_programados'] = count($venta['programacion_pagos'][0]['pagos_programados']);
                $venta['numero_pagos_programados_cubiertos'] = $pagados;

                $venta['numero_pagos_realizados'] = $pagos_realizados;


                $venta['pagos_vencidos'] = $vencidos;


                if ($fecha_primer_pago_vencido != '') {
                    $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $fecha_del_primer_pago_vencido = Carbon::createFromFormat('Y-m-d', $fecha_primer_pago_vencido);
                    $diferencia_en_dias = $fecha_hoy->diffInDays($fecha_del_primer_pago_vencido);
                    $venta['dias_vencidos'] = $diferencia_en_dias;
                }


                $venta['sub_total_pagado'] = $sub_total_pagado;
                $venta['iva_pagado'] = $iva_pagado;
                $venta['descuento_pagado'] = $descuento_pagado;
                $venta['total_pagado'] = $total_pagado;

                $venta['restante_pagar_subtotal'] = $venta['subtotal'] - $sub_total_pagado;
                //se retorna el resultado


                $venta['total_pagar_neto_con_intereses'] =  $venta['total']  + $venta['intereses_generados'];
                $venta['saldo_pagar_neto_con_intereses'] =  $venta['total_pagar_neto_con_intereses'] - $venta['total_pagado'] - $venta['intereses_pagados'];
            }
        }
        return $resultado;
    }



    /**obtiene todas las ventas para el paginado de ventas de cementerio */
    public function get_ventas(Request $request)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular = $request->titular;
        $numero_control = $request->numero_control;
        $status = $request->status;

        $datos = $this->showAllPaginated(
            VentasTerrenos::select(
                'clientes_id',
                'clientes.nombre as cliente_nombre',
                'clientes.email as cliente_email',
                'clientes.direccion as cliente_direccion',
                'clientes.ciudad as cliente_ciudad',
                'clientes.estado as cliente_estado',

                'clientes.telefono as cliente_telefono',
                'clientes.celular as cliente_celular',
                'clientes.telefono_extra as cliente_telefono_extra',
                'clientes.rfc as cliente_rfc',
                'clientes.fecha_nac as cliente_fecha_nac',
                'titular_sustituto',
                'parentesco_titular_sustituto',
                'telefono_titular_sustituto',
                'ventas_terrenos.fecha_registro',
                'ventas_terrenos.status',
                'ventas_terrenos.id',
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'numero_solicitud AS numero_solicitud_raw',
                'numero_convenio as numero_convenio_raw',
                'numero_titulo as numero_titulo_raw',
                'ubicacion as ubicacion_raw',
                'fecha_venta',
                'ventas_terrenos.fecha_registro',
                'total',
                'subtotal',
                'descuento',
                'iva',
                'ventas_terrenos.status',
                'antiguedad_ventas_id',
                'vendedor_id',
                'empresa_operaciones_id',
                DB::raw(
                    '(NULL) AS tipo_propiedad_des'
                ),
                DB::raw(
                    '(NULL) AS tipo_propiedad_capacidad'
                ),
                DB::raw(
                    '(NULL) AS intereses_generados'
                ),
                DB::raw(
                    '(NULL) AS intereses_pagados'
                ),
                DB::raw(
                    '(NULL) AS sub_total_pagado'
                ),
                DB::raw(
                    '(NULL) AS iva_pagado'
                ),
                DB::raw(
                    '(NULL) AS descuento_pagado'
                ),
                DB::raw(
                    '(NULL) AS total_pagado'
                ),
                DB::raw(
                    '(NULL) AS restante_pagar_subtotal'
                ),
                DB::raw(
                    '(NULL) AS saldo_pagar_neto_con_intereses'
                ),
                DB::raw(
                    '(NULL) AS total_pagar_neto_con_intereses'
                ),
                DB::raw(
                    '(0) AS pagos_vencidos'
                ),
                DB::raw(
                    '(0) AS dias_vencidos'
                ),
                DB::raw(
                    '(0) AS numero_pagos_programados'
                ),
                DB::raw(
                    '(0) AS numero_pagos_programados_cubiertos'
                ),
                DB::raw(
                    '(0) AS numero_pagos_realizados'
                ),

                DB::raw(
                    '(NULL) AS tipo_raw'
                ),
                DB::raw(
                    '(NULL) AS id_propiedad_raw'
                ),
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.empresa_operaciones_id = "1" THEN "Inmediato"
                        ELSE "A futuro" 
                        END) AS uso_venta'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_solicitud <> "" THEN ventas_terrenos.numero_solicitud
                        ELSE "N/A" 
                        END) AS numero_solicitud'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_convenio <> "" THEN ventas_terrenos.numero_convenio
                        ELSE "N/A" 
                        END) AS numero_convenio'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_titulo <> "" THEN ventas_terrenos.numero_titulo
                        ELSE "Pendiente" 
                        END) AS numero_titulo'
                ),
                DB::raw(
                    '"" as ubicacion_texto'
                ),
                DB::raw(
                    '"" as tipo_texto'
                ),
                DB::raw(
                    '"" as fila_texto'
                ),
                DB::raw(
                    '"" as area_nombre'
                ),
                DB::raw(
                    '"" as lote_texto'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_des'
                )
            )

                ->with(array('programacionPagos.pagosProgramados.conceptoPago'))
                ->with(array('programacionPagos.pagosProgramados.pagosRealizados'))
                ->with(array('vendedor' => function ($query) {
                    $query->select('id', 'nombre');
                }))
                ->with(
                    'beneficiarios'
                )
                ->with(
                    'antiguedad'
                )
                ->with(
                    'ajustesIntereses'
                )

                ->where(function ($q) use ($status) {
                    if ($status != '') {
                        $q->where('ventas_terrenos.status', $status);
                    }
                })
                ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                    if (trim($numero_control) != '') {
                        if ($filtro_especifico_opcion == 1) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_terrenos.numero_solicitud', '=',  $numero_control);
                        } else if ($filtro_especifico_opcion == 2) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_terrenos.numero_convenio', '=',  $numero_control);
                        } else if ($filtro_especifico_opcion == 3) {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_terrenos.numero_titulo', '=',  $numero_control);
                        } else {
                            /**filtro por numero de solicitud */
                            $q->where('ventas_terrenos.id', $numero_control);
                        }
                    }
                })
                ->where(function ($q) use ($titular) {
                    if (trim($titular) != '') {
                        $q->where('clientes.nombre', 'like', '%' . $titular . '%');
                    }
                })

                ->join('clientes', 'ventas_terrenos.clientes_id', '=', 'clientes.id')
                ->orderBy('ventas_terrenos.id', 'desc')
                ->get()
        );

        $resultado = $datos->toArray();




        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        $datos_cementerio = $this->get_cementerio();
        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */

        //**se actualiza la propiedad a formato legible para el usuario */

        foreach ($resultado['data'] as $key_data => &$venta) {
            $venta['ubicacion_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['ubicacion_texto'];
            $venta['area_nombre'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['area_nombre'];
            $venta['tipo_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['tipo_texto'];
            $venta['fila_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['fila_texto'];
            $venta['lote_texto'] = $this->ubicacion_texto($venta['ubicacion_raw'], $datos_cementerio)['lote_texto'];

            /**agregando fila, lote, y tipo, por separado en valor numrico */
            $venta['tipo_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[0]));
            $venta['id_propiedad_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[1]));
            $venta['fila_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[2]));
            $venta['lote_raw'] = (intval(explode("-", $venta['ubicacion_raw'])[3]));

            /**seleccionado el tipo de propiedad */
            $tipo_propiedad = tipoPropiedades::where('id', $venta['tipo_raw'])->first();
            $venta['tipo_propiedad_des'] = $tipo_propiedad['tipo'];
            $venta['tipo_propiedad_capacidad'] = $tipo_propiedad['capacidad'];

            /**obteniendo el num de pagos realizados y vigentes */

            $sub_total_pagado = 0;
            $iva_pagado = 0;
            $descuento_pagado = 0;
            $total_pagado = 0;


            $pagados = 0;
            $vencidos = 0;
            $pagos_realizados = 0;
            /**calculando el monto de interes que debe la persona */
            $ajustes_intereses = $venta['ajustes_intereses'];

            /**fecha del primer pago vencido para scar la diferencia */
            $fecha_primer_pago_vencido = '';

            $intereses_generados = 0;
            $intereses_pagados = 0;


            if (isset($venta['programacion_pagos'][0]['pagos_programados'])) {
                foreach ($venta['programacion_pagos'][0]['pagos_programados'] as &$programado) {
                    /**definiendo el conceptop del pago */
                    if ($programado['concepto_pago']['id'] == 1) {
                        /**enganche */
                        $programado['concepto'] = "Enganche inicial.";
                    } else if ($programado['concepto_pago']['id'] == 2) {
                        /**abono */
                        $programado['concepto'] = "Abono Núm." . ($programado['num_pago'] - 1) . " correspondiente al mes de " . mes_year_from_fecha($programado['fecha_programada']) . ".";
                    } else {
                        /**liquidacion */
                        $programado['concepto'] = "Pago único de liquidación.";
                    }

                    /**valores por default */
                    $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                    $programado['total_a_pagar'] = $programado['total'];
                    $cubierto = 0;
                    $vencido = 0;

                    /**manejo de intereses */
                    $dias_retrasados_del_pago = 0;
                    $interes_a_pagar = 0;
                    /**veririca si el pago vencio y no se ha pagado ndd*/
                    if (count($programado['pagos_realizados']) == 0) {
                        if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                            $vencidos++;
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                            $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                            $programado['fecha_a_pagar'] = date('Y-m-d');
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             **/
                            $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */

                            $programado['total_a_pagar'] = doubleVal($programado['total']) + $interes_a_pagar;

                            $programado['intereses_a_pagar'] = $interes_a_pagar;

                            $programado['vencido'] = 1;
                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                            $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                            $programado['intereses'] = $interes_a_pagar;

                            if ($fecha_primer_pago_vencido == '') {
                                $fecha_primer_pago_vencido = $programado['fecha_programada'];
                            }
                        }
                    } else {
                        /**hay pagos realizados */
                        $monto_pagado_interes = 0;
                        $fecha_ultimo_pago_realizado = '';
                        foreach ($programado['pagos_realizados'] as $realizado) {
                            if ($realizado['status'] == 1) {
                                $pagos_realizados++;
                                /***sacando monto pagado a cuenta de intereses */
                                if ($realizado['tipo_pagos_id'] == 2) {
                                    /**es abono a intereses */
                                    /**me baso en subtotal */
                                    $monto_pagado_interes += (doubleVal($realizado['total']));
                                    $programado['intereses_pagado'] += (doubleVal($realizado['total']));
                                } else {
                                    /**total cuvierto del pago */
                                    $programado['subtotal_pagado'] += (doubleVal($realizado['subtotal']));
                                    $programado['iva_pagado'] += (doubleVal($realizado['iva']));
                                    $programado['descuento_pagado'] += (doubleVal($realizado['descuento']));
                                    $programado['total_pagado'] += (doubleVal($realizado['total']));


                                    /**es abono a capital */
                                    $sub_total_pagado += (doubleVal($realizado['subtotal']));
                                    $iva_pagado +=  (doubleVal($realizado['iva']));
                                    $descuento_pagado += (doubleVal($realizado['descuento']));
                                    $total_pagado += (doubleVal($realizado['total']));
                                    $cubierto += (doubleVal($realizado['subtotal']));
                                }

                                $next = next($realizado);
                                if (false !== $next) {
                                    $fecha_ultimo_pago_realizado = $realizado['fecha_pago'];
                                    //do something with $current
                                }
                            }
                        }
                        if ($cubierto >= $programado['subtotal']) {
                            /**checar si se pago el abono dentro de la fecha limite */
                            if (strtotime($fecha_ultimo_pago_realizado) <= strtotime($programado['fecha_programada'])) {
                                /**pago cubierto en fecha y orden */
                                $programado['intereses_a_pagar'] = 0;
                                $programado['total_a_pagar'] = 0;
                                $programado['pagado'] = 1;
                                $pagados++;
                                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                            } else {
                                /**checando si el monto de los intereses pagados corresponde a los dias vencidos */
                                $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                                $fecha_pago_realizado = Carbon::createFromFormat('Y-m-d', $fecha_ultimo_pago_realizado);
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_pago_realizado);
                                /**
                                 * Los intereses moratorios se calcularán
                                 * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                                 * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                                 * ser hecho y la fecha que el contratante
                                 * liquide el adeudo.
                                 */
                                $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;
                                if ($interes_a_pagar < $monto_pagado_interes) {
                                    if ($fecha_primer_pago_vencido == '') {
                                        $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                    }
                                    $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                                    $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];
                                    $vencidos++;
                                    $programado['vencido'] = 1;
                                    /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                    $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                    $programado['intereses'] = $interes_a_pagar;
                                    $programado['fecha_a_pagar'] = date('Y-m-d');
                                } else {
                                    $programado['fecha_a_pagar'] = $fecha_pago_realizado;
                                    /**pago cubierto en fecha y orden */
                                    $programado['pagado'] = 1;
                                    /**el monto es igual o mayor */
                                    $pagados++;
                                }
                            }
                        } else {
                            if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                                $vencidos++;
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                                $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                                $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                /**
                                 * Los intereses moratorios se calcularán
                                 * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                                 * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                                 * ser hecho y la fecha que el contratante
                                 * liquide el adeudo.
                                 */
                                $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                                $programado['vencido'] = 1;
                                /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_a_pagar;

                                $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                                $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];

                                if ($fecha_primer_pago_vencido == '') {
                                    $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                }
                            }
                        }
                    }

                    $intereses_generados += $interes_a_pagar;
                    $intereses_pagados += $programado['intereses_pagado'];
                }

                $venta['intereses_generados'] =  $intereses_generados;
                $venta['intereses_pagados'] =  $intereses_pagados;


                $venta['numero_pagos_programados'] = count($venta['programacion_pagos'][0]['pagos_programados']);
                $venta['numero_pagos_programados_cubiertos'] = $pagados;

                $venta['numero_pagos_realizados'] = $pagos_realizados;


                $venta['pagos_vencidos'] = $vencidos;


                if ($fecha_primer_pago_vencido != '') {
                    $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $fecha_del_primer_pago_vencido = Carbon::createFromFormat('Y-m-d', $fecha_primer_pago_vencido);
                    $diferencia_en_dias = $fecha_hoy->diffInDays($fecha_del_primer_pago_vencido);
                    $venta['dias_vencidos'] = $diferencia_en_dias;
                }


                $venta['sub_total_pagado'] = $sub_total_pagado;
                $venta['iva_pagado'] = $iva_pagado;
                $venta['descuento_pagado'] = $descuento_pagado;
                $venta['total_pagado'] = $total_pagado;

                $venta['restante_pagar_subtotal'] = $venta['subtotal'] - $sub_total_pagado;
                //se retorna el resultado


                $venta['total_pagar_neto_con_intereses'] =  $venta['total']  + $venta['intereses_generados'];
                $venta['saldo_pagar_neto_con_intereses'] =  $venta['total_pagar_neto_con_intereses'] - $venta['total_pagado'] - $venta['intereses_pagados'];
            }
        }
        return $resultado;
    }

    public function ubicacion_texto($dato = '', $datos_cementerio = [])
    {
        /**se hace un arreglo para regresar la ubicacion completa y por separado (fila, pripieda, lote tipo) */

        /**para decidir el nombre del area, en caso de ser teraza, seria 1,2,3,4,5 en tipo 
         * de unplex seria a,b,c
         */
        $areas_nombres = [
            /**uniplex */
            1 => 'a', 2 => 'b', 3 => 'd', 4 => 'e', 5 => 'm', 6 => 'n', 7 => 'ñ', 8 => 'o',
            /**duplex */
            9 => 'c', 10 => 'f', 11 => 'g', 12 => 'h', 13 => 'i', 14 => 'j', 15 => 'k', 16 => 'l',
            /**nichos */
            17 => '1', 18 => '2', 19 => '3', 20 => '4', 21 => '5', 22 => '6', 23 => '7', 24 => '8', 25 => '9', 26 => '10', 27 => '11', 28 => '12',
            /**terrazas */
            29 => '1', 30 => '2', 31 => '3', 32 => '4', 33 => '5', 34 => '6', 35 => '7', 36 => '8', 37 => '9', 38 => '10', 39 => '11', 40 => '12', 41 => '13', 42 => '14', 43 => '15', 44 => '16', 45 => '16', 46 => '18',
            /**duplex */
            47 => 's',
            /**triplex */
            48 => 'p', 49 => 'r',
            /**cuadriplex sin terraza */
            50 => 'q'
        ];

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
            if ($propiedad['id'] == $id_propiedad) {
                //una vez encontrada el id defino si es terraza que es
                if ($propiedad['tipo_propiedades_id'] == 1) {
                    //uniplex
                    $ubicacion_texto .= "Uniplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                    $datos['tipo_texto'] = "uniplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['lote_texto'] = "n/a";
                } else if ($propiedad['tipo_propiedades_id'] == 2) {
                    //duplex
                    $ubicacion_texto .= "Duplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                    $datos['tipo_texto'] = "duplex";
                    $datos['fila_texto'] = " Módulo " . $fila;
                    $datos['lote_texto'] = "n/a";
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                } else if ($propiedad['tipo_propiedades_id'] == 3) {
                    //nicho
                    $ubicacion_texto .= "Nichos - Columna " . $propiedad['propiedad_indicador'] . ", Fila " . $fila;
                    $datos['tipo_texto'] = "nicho";
                    $datos['fila_texto'] =  $fila;
                    $datos['area_nombre'] = "Columna " . $areas_nombres[($id_propiedad)];
                    $datos['lote_texto'] = "n/a";
                } else if ($propiedad['tipo_propiedades_id'] == 4) {
                    $datos['lote_texto'] = $lote;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "terraza";
                    $datos['fila_texto'] = strtoupper($alfabeto[$fila - 1]);
                    //cuadriplex
                    $ubicacion_texto .= "Terraza " . $propiedad['propiedad_indicador'] . ", Fila " . strtoupper($alfabeto[$fila - 1]) . " Lote " . $lote;
                } else if ($propiedad['tipo_propiedades_id'] == 5) {
                    $datos['lote_texto'] = "n/a";
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "triplex";
                    $datos['fila_texto'] =  " Módulo " . $fila;
                    //triplex
                    $ubicacion_texto .= "Triplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                } else if ($propiedad['tipo_propiedades_id'] == 6) {
                    $datos['lote_texto'] = $lote;
                    $datos['area_nombre'] = $areas_nombres[($id_propiedad)];
                    $datos['tipo_texto'] = "cuadriplex";
                    $datos['fila_texto'] =  " Módulo " . $fila;
                    //cuadriplex sin terraza
                    $ubicacion_texto .= "cuadriplex " . $propiedad['propiedad_indicador'] . " Módulo " . $fila;
                }
            }
        }
        $datos['ubicacion_texto'] = $ubicacion_texto;


        return $datos;
    }




    /**obtiene la venta por id */
    public function get_venta_id($venta_id = 0)
    {
        $id_venta = $venta_id;
        $datos =
            VentasTerrenos::select(
                'clientes_id',
                'clientes.nombre as cliente_nombre',
                'clientes.email as cliente_email',
                'clientes.direccion as cliente_direccion',
                'clientes.ciudad as cliente_ciudad',
                'clientes.estado as cliente_estado',

                'clientes.telefono as cliente_telefono',
                'clientes.celular as cliente_celular',
                'clientes.telefono_extra as cliente_telefono_extra',
                'clientes.rfc as cliente_rfc',
                'clientes.fecha_nac as cliente_fecha_nac',
                'titular_sustituto',
                'parentesco_titular_sustituto',
                'telefono_titular_sustituto',
                'ventas_terrenos.fecha_registro',
                'ventas_terrenos.status',
                'ventas_terrenos.id',
                'numero_solicitud',
                'numero_convenio',
                'numero_titulo',
                'numero_solicitud AS numero_solicitud_raw',
                'numero_convenio as numero_convenio_raw',
                'numero_titulo as numero_titulo_raw',
                'ubicacion as ubicacion_raw',
                'fecha_venta',
                'ventas_terrenos.fecha_registro',
                'total',
                'subtotal',
                'descuento',
                'iva',
                'ventas_terrenos.status',
                'antiguedad_ventas_id',
                'vendedor_id',
                'empresa_operaciones_id',
                DB::raw(
                    '(NULL) AS tipo_propiedad_des'
                ),
                DB::raw(
                    '(NULL) AS tipo_propiedad_capacidad'
                ),
                DB::raw(
                    '(NULL) AS intereses_generados'
                ),
                DB::raw(
                    '(NULL) AS intereses_pagados'
                ),
                DB::raw(
                    '(NULL) AS sub_total_pagado'
                ),
                DB::raw(
                    '(NULL) AS iva_pagado'
                ),
                DB::raw(
                    '(NULL) AS descuento_pagado'
                ),
                DB::raw(
                    '(NULL) AS total_pagado'
                ),
                DB::raw(
                    '(NULL) AS restante_pagar_subtotal'
                ),
                DB::raw(
                    '(NULL) AS saldo_pagar_neto_con_intereses'
                ),
                DB::raw(
                    '(NULL) AS total_pagar_neto_con_intereses'
                ),
                DB::raw(
                    '(0) AS pagos_vencidos'
                ),
                DB::raw(
                    '(0) AS dias_vencidos'
                ),
                DB::raw(
                    '(0) AS numero_pagos_programados'
                ),
                DB::raw(
                    '(0) AS numero_pagos_programados_cubiertos'
                ),
                DB::raw(
                    '(0) AS numero_pagos_realizados'
                ),

                DB::raw(
                    '(NULL) AS tipo_raw'
                ),
                DB::raw(
                    '(NULL) AS id_propiedad_raw'
                ),
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.empresa_operaciones_id = "1" THEN "Inmediato"
                        ELSE "A futuro" 
                        END) AS uso_venta'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_solicitud <> "" THEN ventas_terrenos.numero_solicitud
                        ELSE "N/A" 
                        END) AS numero_solicitud'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_convenio <> "" THEN ventas_terrenos.numero_convenio
                        ELSE "N/A" 
                        END) AS numero_convenio'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.numero_titulo <> "" THEN ventas_terrenos.numero_titulo
                        ELSE "Pendiente" 
                        END) AS numero_titulo'
                ),
                DB::raw(
                    '"" as ubicacion_texto'
                ),
                DB::raw(
                    '"" as tipo_texto'
                ),
                DB::raw(
                    '"" as fila_texto'
                ),
                /**por decir unoplex "a",b , terraza "1", duplex "b" */
                DB::raw(
                    '"" as area_nombre'
                ),
                DB::raw(
                    '"" as lote_texto'
                ),
                DB::raw(
                    '(CASE 
                        WHEN ventas_terrenos.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_des'
                ),
                DB::raw(
                    '"" as fecha_venta_texto'
                ),
            )

            ->with(array('programacionPagos.pagosProgramados.conceptoPago'))
            ->with(array('programacionPagos.pagosProgramados.pagosRealizados'))
            ->with(array('vendedor' => function ($query) {
                $query->select('id', 'nombre');
            }))
            ->with(
                'beneficiarios'
            )
            ->with(
                'antiguedad'
            )
            ->with(
                'ajustesIntereses'
            )
            ->join('clientes', 'ventas_terrenos.clientes_id', '=', 'clientes.id')
            ->where('ventas_terrenos.id', $id_venta)
            ->orderBy('ventas_terrenos.id', 'desc')
            ->get();


        $resultado = $datos[0]->toArray();


        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        $datos_cementerio = $this->get_cementerio();
        /**obtiene la estructura del cementerio para poder crear la ubicacion a cadena */
        //**se actualiza la propiedad a formato legible para el usuario */
        $resultado['area_nombre'] = $this->ubicacion_texto($resultado['ubicacion_raw'], $datos_cementerio)['area_nombre'];
        $resultado['ubicacion_texto'] = $this->ubicacion_texto($resultado['ubicacion_raw'], $datos_cementerio)['ubicacion_texto'];
        $resultado['lote_texto'] = $this->ubicacion_texto($resultado['ubicacion_raw'], $datos_cementerio)['lote_texto'];
        $resultado['tipo_texto'] = $this->ubicacion_texto($resultado['ubicacion_raw'], $datos_cementerio)['tipo_texto'];
        $resultado['fila_texto'] = $this->ubicacion_texto($resultado['ubicacion_raw'], $datos_cementerio)['fila_texto'];

        /**agregando fila, lote, y tipo, por separado en valor numrico */
        $resultado['tipo_raw'] = (intval(explode("-", $resultado['ubicacion_raw'])[0]));
        $resultado['id_propiedad_raw'] = (intval(explode("-", $resultado['ubicacion_raw'])[1]));
        $resultado['fila_raw'] = (intval(explode("-", $resultado['ubicacion_raw'])[2]));
        $resultado['lote_raw'] = (intval(explode("-", $resultado['ubicacion_raw'])[3]));


        /**seleccionado el tipo de propiedad */
        $tipo_propiedad = tipoPropiedades::where('id', $resultado['tipo_raw'])->first();
        $resultado['tipo_propiedad_des'] = $tipo_propiedad['tipo'];
        $resultado['tipo_propiedad_capacidad'] = $tipo_propiedad['capacidad'];

        /**obteniendo el num de pagos realizados y vigentes */

        $sub_total_pagado = 0;
        $iva_pagado = 0;
        $descuento_pagado = 0;
        $total_pagado = 0;


        $pagados = 0;
        $vencidos = 0;
        $pagos_realizados = 0;
        /**calculando el monto de interes que debe la persona */
        $ajustes_intereses = $resultado['ajustes_intereses'];

        /**fecha del primer pago vencido para scar la diferencia */
        $fecha_primer_pago_vencido = '';

        $intereses_generados = 0;
        $intereses_pagados = 0;

        foreach ($resultado['programacion_pagos'] as $index => &$programacion) {

            foreach ($programacion['pagos_programados'] as $key => &$programado) {
                /**fecha_programada con texto */
                $programado['fecha_programada_texto'] = fecha_abr($programado['fecha_programada']);

                /**definiendo el conceptop del pago */
                if ($programado['concepto_pago']['id'] == 1) {
                    /**enganche */
                    $programado['concepto'] = "Enganche inicial.";
                } else if ($programado['concepto_pago']['id'] == 2) {
                    /**abono */
                    $programado['concepto'] = "Abono Núm." . ($programado['num_pago'] - 1) . " correspondiente al mes de " . mes_year_from_fecha($programado['fecha_programada']) . ".";
                } else {
                    /**liquidacion */
                    $programado['concepto'] = "Pago único de liquidación.";
                }

                /**valores por default */
                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                $programado['total_a_pagar'] = $programado['total'];
                $cubierto = 0;
                $vencido = 0;

                /**manejo de intereses */
                $dias_retrasados_del_pago = 0;
                $interes_a_pagar = 0;
                /**veririca si el pago vencio y no se ha pagado ndd*/
                if (count($programado['pagos_realizados']) == 0) {
                    if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                        $vencidos++;
                        $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                        $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                        $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                        $programado['fecha_a_pagar'] = date('Y-m-d');
                        /**
                         * Los intereses moratorios se calcularán
                         * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                         * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                         * ser hecho y la fecha que el contratante
                         * liquide el adeudo.
                         **/
                        $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                        /**actualizando los datos de vencimiento, en caso de que hayan vencido */

                        $programado['total_a_pagar'] = doubleVal($programado['total']) + $interes_a_pagar;

                        $programado['intereses_a_pagar'] = $interes_a_pagar;

                        $programado['vencido'] = 1;
                        /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                        $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                        $programado['intereses'] = $interes_a_pagar;

                        if ($fecha_primer_pago_vencido == '') {
                            $fecha_primer_pago_vencido = $programado['fecha_programada'];
                        }
                    }
                } else {
                    /**hay pagos realizados */
                    $monto_pagado_interes = 0;
                    $fecha_ultimo_pago_realizado = '';
                    foreach ($programado['pagos_realizados'] as &$realizado) {
                        if ($realizado['status'] == 1) {
                            $pagos_realizados++;
                            /***sacando monto pagado a cuenta de intereses */
                            if ($realizado['tipo_pagos_id'] == 2) {
                                /**es abono a intereses */
                                /**me baso en subtotal */
                                $monto_pagado_interes += (doubleVal($realizado['total']));
                                $programado['intereses_pagado'] += (doubleVal($realizado['total']));
                            } else {
                                /**total cuvierto del pago */
                                $programado['subtotal_pagado'] += (doubleVal($realizado['subtotal']));
                                $programado['iva_pagado'] += (doubleVal($realizado['iva']));
                                $programado['descuento_pagado'] += (doubleVal($realizado['descuento']));
                                $programado['total_pagado'] += (doubleVal($realizado['total']));


                                /**es abono a capital */
                                $sub_total_pagado += (doubleVal($realizado['subtotal']));
                                $iva_pagado +=  (doubleVal($realizado['iva']));
                                $descuento_pagado += (doubleVal($realizado['descuento']));
                                $total_pagado += (doubleVal($realizado['total']));
                                $cubierto += (doubleVal($realizado['subtotal']));
                            }

                            $next = next($realizado);
                            if (false !== $next) {
                                $fecha_ultimo_pago_realizado = $realizado['fecha_pago'];
                                //do something with $current
                            }
                        }
                    }
                    if ($cubierto >= $programado['subtotal']) {
                        /**checar si se pago el abono dentro de la fecha limite */
                        if (strtotime($fecha_ultimo_pago_realizado) <= strtotime($programado['fecha_programada'])) {
                            /**pago cubierto en fecha y orden */
                            $programado['intereses_a_pagar'] = 0;
                            $programado['total_a_pagar'] = 0;
                            $programado['pagado'] = 1;
                            $pagados++;
                            $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                        } else {
                            /**checando si el monto de los intereses pagados corresponde a los dias vencidos */
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                            $fecha_pago_realizado = Carbon::createFromFormat('Y-m-d', $fecha_ultimo_pago_realizado);
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_pago_realizado);
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             */
                            $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;
                            if ($interes_a_pagar < $monto_pagado_interes) {
                                if ($fecha_primer_pago_vencido == '') {
                                    $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                }
                                $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                                $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];
                                $vencidos++;
                                $programado['vencido'] = 1;
                                /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_a_pagar;
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                            } else {
                                $programado['fecha_a_pagar'] = $fecha_pago_realizado;
                                /**pago cubierto en fecha y orden */
                                $programado['pagado'] = 1;
                                /**el monto es igual o mayor */
                                $pagados++;
                            }
                        }
                    } else {
                        if (strtotime(date('Y-m-d')) > strtotime($programado['fecha_programada'])) {
                            $vencidos++;
                            $programado['fecha_a_pagar'] = date('Y-m-d');
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                            $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             */
                            $interes_a_pagar = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                            $programado['vencido'] = 1;
                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                            $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                            $programado['intereses'] = $interes_a_pagar;

                            $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_a_pagar - $programado['intereses_pagado'];
                            $programado['intereses_a_pagar'] = $interes_a_pagar - $programado['intereses_pagado'];

                            if ($fecha_primer_pago_vencido == '') {
                                $fecha_primer_pago_vencido = $programado['fecha_programada'];
                            }
                        } else {
                            /**al no esta vencido el pago solo se actualiza el total a pagar de ese pago programado */
                            $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'];
                        }
                    }
                }
                $intereses_generados += $interes_a_pagar;
                $intereses_pagados += $programado['intereses_pagado'];
            }
            if ($index == 0) {
                /**solo se toma en cuenta los valores de las sumas para la programacion actual */

                $resultado['fecha_venta_texto'] = fecha_abr($resultado['fecha_venta']);
                $resultado['intereses_generados'] =  $intereses_generados;
                $resultado['intereses_pagados'] =  $intereses_pagados;
                $resultado['numero_pagos_programados'] = count($resultado['programacion_pagos'][0]['pagos_programados']);
                $resultado['numero_pagos_programados_cubiertos'] = $pagados;
                $resultado['numero_pagos_realizados'] = $pagos_realizados;
                $resultado['pagos_vencidos'] = $vencidos;
                if ($fecha_primer_pago_vencido != '') {
                    $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $fecha_del_primer_pago_vencido = Carbon::createFromFormat('Y-m-d', $fecha_primer_pago_vencido);
                    $diferencia_en_dias = $fecha_hoy->diffInDays($fecha_del_primer_pago_vencido);
                    $resultado['dias_vencidos'] = $diferencia_en_dias;
                }
                $resultado['sub_total_pagado'] = $sub_total_pagado;
                $resultado['iva_pagado'] = $iva_pagado;
                $resultado['descuento_pagado'] = $descuento_pagado;
                $resultado['total_pagado'] = $total_pagado;
                $resultado['restante_pagar_subtotal'] = $resultado['subtotal'] - $sub_total_pagado;
                //se retorna el resultado
                $resultado['total_pagar_neto_con_intereses'] =  $resultado['total']  + $resultado['intereses_generados'];
                $resultado['saldo_pagar_neto_con_intereses'] =  $resultado['total_pagar_neto_con_intereses'] - $resultado['total_pagado'] - $resultado['intereses_pagados'];
            }
        }
        return $resultado;
    }







    public function acuse_cancelacion(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /*  $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];
*/
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $id_venta = 9;
        $email = false;
        $email_to = 'hector@gmail.com';



        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/titulo/titulo', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "ACUSE DE CANCELACIÓN " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.titulo.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.titulo.header')
            ]);
        }




        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 24.4);
        $pdf->setOption('margin-right', 24.4);
        $pdf->setOption('margin-top', 24.4);
        $pdf->setOption('margin-bottom', 24.4);
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
                strtoupper($datos_venta['cliente_nombre']),
                'ACUSE DE CANCELACIÓN',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }



    public function documento_titulo(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 8;
        $email = false;
        $email_to = 'hector@gmail.com';
*/


        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/titulo/titulo', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "FORMATO DE TITULO " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.titulo.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.titulo.header')
            ]);
        }




        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 24.4);
        $pdf->setOption('margin-right', 24.4);
        $pdf->setOption('margin-top', 24.4);
        $pdf->setOption('margin-bottom', 24.4);
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
                strtoupper($datos_venta['cliente_nombre']),
                'FORMATO DE TITULO',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }


    public function referencias_de_pago(Request $request, $id_pago = '', $id_programacion = '')
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 4;
        $email = false;
        $email_to = 'hector@gmail.com';
        */


        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/pagos/referencias_de_pago', ['id_programacion' => $id_programacion, 'id_pago' => $id_pago, 'datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "REFERENCIA DE PAGOS TITULAR " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.pagos.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.pagos.header')
            ]);
        }




        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 14.4);
        $pdf->setOption('margin-right', 14.4);
        $pdf->setOption('margin-top', 13.4);
        $pdf->setOption('margin-bottom', 13.4);
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
                strtoupper($datos_venta['cliente_nombre']),
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
        /*$id_venta = 2;
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
        $id_venta = $requestVentasList['venta_id'];

        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
            return 0;
        }*/


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/convenio/documento_convenio', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "CONVENIO TITULAR " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.convenio.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.convenio.header')
            ]);
        }
        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('margin-left', 23.4);
        $pdf->setOption('margin-right', 23.4);
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
                strtoupper($datos_venta['cliente_nombre']),
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
        /* $id_venta = 2;
        $email = false;
        $email_to = 'hector@gmail.com';
*/
        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
        }*/


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/solicitud/documento_solicitud', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "SOLICITUD TITULAR " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.solicitud.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
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
                strtoupper($datos_venta['cliente_nombre']),
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
        /*$id_venta = 5;
        $email = false;
        $email_to = 'hector@gmail.com';
*/
        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_solicitud_raw'] == null) {
            return 0;
        }*/


        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/estado_cuenta/estado_cuenta', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "ESTADO CUENTA " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.estado_cuenta.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.estado_cuenta.header')
            ]);
        }

        //$pdf->setOption('grayscale', true);
        //$pdf->setOption('header-right', 'dddd');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-left', 12.4);
        $pdf->setOption('margin-right', 12.4);
        $pdf->setOption('margin-top', 12.4);
        $pdf->setOption('margin-bottom', 12.4);
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
                strtoupper($datos_venta['cliente_nombre']),
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