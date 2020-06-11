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
use App\AjustesPoliticas;
use App\PagosPropiedades;
use App\AntiguedadesVenta;
use App\EmpresaOperaciones;
use App\Operaciones;
use App\PreciosPropiedades;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\PagosProgramadosTerrenos;
use PhpParser\Node\Stmt\Foreach_;
use App\ProgramacionPagosTerrenos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\PagosProgramadosPropiedades;
use Illuminate\Support\Facades\Mail;

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
                    'cantidad_a_regresar_cancelacion' => (float) $request->cantidad,
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
            ->with('filas_columnas')->with('tipoPropiedad')->with('tipoPropiedad.precios')->with('filas_columnas')->orderBy('id', 'asc')->get()->toArray();

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




            foreach ($dato['tipo_propiedad']['precios'] as $precio_key => &$precio) {

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
                    $precio['descuento_x_pago'] = ($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'];
                    $precio['porcentaje_pronto_pago'] = 100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']);
                } else {
                    $precio['descuento_x_pago'] = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }

            /**agregando fila, lote, y tipo, por separado en valor numrico */

            /*foreach ($dato['ventas'] as $key_venta => &$venta) {
                $venta['fila_raw'] = (intval(explode("-", $venta['ubicacion'])[2]));
                $venta['lote_raw'] = (intval(explode("-", $venta['ubicacion'])[3]));
            }*/
        }


        return $datos;
    }

    //obtiene los usuarios para vendedores
    public function get_vendedores()
    {
        //no super usuarios
        /**puesto de venderor id 2 */
        /**obtiene los usuarios con puesto de vendedor */
        return User::select('id', 'nombre')
            ->join('usuarios_puestos', 'usuarios_puestos.usuarios_id', '=', 'usuarios.id')
            ->where('roles_id', '>', 1)
            ->where('puestos_id', '=', 2)
            ->where('usuarios.status', '>', 0)
            ->get();
    }

    public function get_sat_formas_pago()
    {
        //id del conjunto de propieades

        return
            SatFormasPago::where('clave', '<>', '99')->where('clave', '<>', '25')->get();
    }

    /**GUARDAR LA VENTA */
    public function control_ventas(Request $request, $tipo_servicio = '')
    {


        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        /**procede la peticion */


        /**aqui comienzan a gurdar los datos */
        $subtotal = (float) $request->subtotal; //sin iva
        $iva = (float) $request->impuestos; //solo el iva
        $descuento = (float) $request->descuento;
        $costo_neto = (float) $request->costo_neto;


        /**valdiando que cuadren las cantidades de la venta */
        //validaciones directas sin condicionales
        $validaciones = [
            //datos de la propiedad
            'id_venta' => '',
            /**solo para modificaciones */
            'tipo_propiedades_id' => 'required|min:1',
            'propiedades_id' => 'required|min:1',
            'ubicacion' => 'required',
            //fin de datos de la propiedad
            //datos de la venta
            'fecha_venta' => 'required|date',
            'ventaAntiguedad.value' => 'required',
            'tipo_financiamiento' => 'required',

            'filas.value' => 'required',
            'lotes.value' => '', //modificada segun condiciones

            'vendedor.value' => 'required',

            'solicitud' => '',
            'convenio' => '',
            'titulo' => '',

            /**id del cliente */
            'id_cliente' => 'required',

            //info del plan de venta y pagos
            'planVenta.value' => 'numeric|required',
            'subtotal' => 'numeric|required|min:1',
            'descuento' => 'required|numeric|min:0|max:' . $request->subtotal,
            'impuestos' => 'numeric|required|min:0',
            'costo_neto' => 'numeric|required|min:0',
            'costo_neto_pronto_pago' => 'required|min:1|lte:' . ($subtotal * (1 + config('globales.iva_decimal'))),
            'pago_inicial' => '',


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
            $validaciones['pago_inicial'] = 'numeric|required|min:' . (float) $request->costo_neto . '|max:' . (float) $request->costo_neto;
        } else {
            /**cuando es a credito */
            if ((float) $request->costo_neto > $request->planVenta['pago_inicial']) {
                /**minimo el pago inicial y maximo un 70% del costo neto */
                $validaciones['pago_inicial'] = 'numeric|required|min:' . (float) $request->planVenta['pago_inicial'] . '|max:' . (float) ($request->costo_neto) * .7;
            } else {
                /**si el descuento es menor al pago inicial se forza al usuario a ingresa como pago inicial minmo un 10% del totoa a pagar y un 70% de maximo 
                 * de pago inicial y el resto liquidarlo con los abonos
                 */
                $validaciones['pago_inicial'] = 'numeric|required|min:' . (float) ($request->costo_neto * .1) . '|max:' . (float) ($request->costo_neto * .7);
            }
        }


        /**validando de manera manual si la ubicacion enviada ya esta registrada y esta activa */
        $ubicacion_enviada = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
            ->where('ubicacion', '=', $request->ubicacion)->where('operaciones.status', 1)->first();
        if (!empty($ubicacion_enviada)) {
            if ($tipo_servicio == 'modificar') {
                if ($ubicacion_enviada->id != $request->id_venta)
                    return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
            } else {
                return $this->errorResponse('La ubicación seleccionada ya ha sido vendida.', 409);
            }
        }



        /**VALIDACIONES CONDICIONADAS*/
        //validando que mande el user el lote en caso de ser terraza
        if ($request->tipo_propiedades_id == 4) {
            //checando que tipo de propiedad es, si es terraza
            $validaciones['lotes.value'] = "required";
        }

        //validnado en caso de que sea de uso inmediato y de venta antes del sistema.
        if ($request->ventaAntiguedad['value'] == 3) {
            //venta de uso inmediato
            $validaciones['titulo'] = 'required';
            /**validando de manera manual si el titulo enviado ya esta registrado y esto activa */
            $titulo = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
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
            $solicitud = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
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

            return $this->errorResponse('El número de convenio ingresado ya ha sido registrado.', 409);
            /**venta ya realizada anterior al sistema pero no liquidadada */
            $validaciones['convenio'] = 'required';

            /**validando de manera manual si la solicitud enviado ya esta registrado y esto activa */
            $convenio = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
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
            $convenio = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
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
            $titulo = VentasTerrenos::select('ventas_terrenos.id')->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id')
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

        if ($tipo_servicio == 'modificar') {
            if ($datos_venta['pagos_vigentes'] > 0) {
                /**tiene pagos vigentes, se debe validar lo que no puede modifcar. 
                 * todo aquello que altere los precios y totales de la venta, pues al tener pagos realizados vigentes se alteria
                 * el contrato y la programacion de pagos
                 * solo puede modificar lo que no altere el contrato como cliente,vendedor, cliente
                 * titular sustituto
                 * beneficiarios
                 */
                /**verificando que no hay modificado nada relativo a precios */
                if (
                    $request->fecha_venta != $datos_venta['venta_terreno']['fecha_date_format'] ||
                    ($request->costo_neto != $datos_venta['total'] ||
                        $request->descuento != $datos_venta['descuento'] ||
                        $request->costo_neto_pronto_pago != $datos_venta['costo_neto_pronto_pago'])
                ) {
                    return $this->errorResponse('La venta no puede modificar datos relativos a cantidades, fecha, ubicacion, tipo de venta, tipo de 
                financiamiento, etc. Esto se debe a que existen pagos vigentes relacionados a esta venta y modificar cantidades o precios causaría que se perdiera la integridad de esta información.', 409);
                }
            }
        }




        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                //venta de uso inmediato y de control sistematizado
                //captura de la venta
                $id_venta = DB::table('ventas_terrenos')->insertGetId(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'fecha_registro' => now(),
                        'fecha_venta' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'registro_id' => (int) $request->user()->id,
                        'considerar_mantenimiento_b' => 1,
                        'nota' => $request->nota,
                        'tipo_financiamiento' => $request->tipo_financiamiento
                    ]
                );

                /**a partir de la venta se crea la operaicon */
                $id_operacion = DB::table('operaciones')->insertGetId(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        'ventas_terrenos_id' => $id_venta,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? $request->solicitud : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' => $this->generarNumeroConvenio($request),
                        'numero_titulo' => ($request->ventaAntiguedad['value'] == 3) ? $request->titulo : null,
                        'empresa_operaciones_id' => 1, //venta de terrenos
                        'subtotal' => $subtotal,
                        'descuento' => $descuento,
                        'impuestos' => $iva,
                        'total' => $costo_neto,
                        'descuento_pronto_pago_b' => $request->planVenta['descuento_pronto_pago_b'],
                        'costo_neto_pronto_pago' => (float) $request->costo_neto_pronto_pago,
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->planVenta['value'],
                        'aplica_devolucion_b' => 0,
                        'costo_neto_financiamiento_normal' => (float) $request->planVenta['costo_neto_financiamiento_normal'],
                        'comision_venta_neto' => 0
                    ]
                );
                /**guardando los datos de la tasa para intereses */
                $this->guardarAjustesPoliticas($request, $id_operacion);
                /**programacion de pagos */
                if ($costo_neto > 0) {
                    /**si la cantidad que resta a pagar es mayor a cero se manda llamar la programcion de pagos */
                    $this->programarPagos($request, $id_operacion, $id_venta);
                } else {
                    /**no hay nada que cobrar, por lo cual debemos generar un numero de titulo inmeadiato */
                    $this->generarNumeroTitulo($id_operacion);
                }
                //captura de los beneficiarios
                $this->guardarBeneficiarios($request, $id_operacion);
                /**guardar venta parte final */
                /**captura de pagos */
                /**fin de captura de pagos */
            }
            /**fin if servicio tipo agregar */
            else {
                /**es modificar */
                DB::table('ventas_terrenos')->where('id', '=', $request->id_venta)->update(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'fecha_modificacion' => now(),
                        'fecha_venta' => date('Y-m-d H:i:s', strtotime($request->fecha_venta)),
                        'modifico_id' => (int) $request->user()->id,
                        'nota' => $request->nota,
                        'tipo_financiamiento' => $request->tipo_financiamiento
                    ]
                );
                DB::table('operaciones')->where('id', '=', $datos_venta['operacion_id'])->update(
                    [
                        'clientes_id' => (int) $request->id_cliente,
                        /**venta a futuro solamente */
                        'numero_solicitud' => ($request->tipo_financiamiento == 2) ? $request->solicitud : null,
                        /**venta  liquidada solamente */
                        'numero_convenio' => trim($request->convenio),
                        'numero_titulo' => trim($request->titulo),
                        'subtotal' => $subtotal,
                        'descuento' => $descuento,
                        'impuestos' => $iva,
                        'total' => $costo_neto,
                        'descuento_pronto_pago_b' => $request->planVenta['descuento_pronto_pago_b'],
                        'costo_neto_pronto_pago' => (float) $request->costo_neto_pronto_pago,
                        'antiguedad_operacion_id' => (int) $request->ventaAntiguedad['value'],
                        /** titular_sustituto */
                        'titular_sustituto' => $request->titular_sustituto,
                        'parentesco_titular_sustituto' => $request->parentesco_titular_sustituto,
                        'telefono_titular_sustituto' => $request->telefono_titular_sustituto,
                        'financiamiento' => $request->planVenta['value'],
                        'costo_neto_financiamiento_normal' => (float) $request->planVenta['costo_neto_financiamiento_normal'],
                    ]
                );
                /**pendiente hacer modificacion de progrmacion de pagos */
            }










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

        /**verificando si la venta sigue activa
         * si ya fue dada de baja no se puede proceder
         */
        if ($datos_venta['status'] != 1) {
            return $this->errorResponse('Solicitud denegada, esta venta ya ha sido cancelada y solo queda disponible para efectos de historial y consulta.', 409);
        }


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
            if ($datos_venta['numero_pagos_vigentes'] > 0) {
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
            $this->guardarBeneficiarios($request,  $request->id_venta);



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
                $this->programarPagos($request, $request->id_venta, $programacion_pagos_id, $version_programacion);

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
                $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_convenio NOT LIKE '%[^0-9]%' THEN numero_convenio END) as int)) AS max_numero_convenio  from operaciones"));
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
    public function generarNumeroTitulo($operacion_id = 0)
    {
        if ($operacion_id > 0) {
            /**pasa a generar el numero de titulo*/

            /**se debe revisar si esta operacion es apta para crearle un numero de titulo automatico */
            $operacion_info = Operaciones::with('venta_terreno')
                ->with('pagosProgramados.pagadosActivos')
                ->where('operaciones.status', '<>', 0)
                ->where('operaciones.id', '=', $operacion_id)
                ->first()->toArray();

            if ($operacion_info['antiguedad_operacion_id'] != 3) {
                $generar = false;
                /** la venta no fue de las que ya estan pagadas */
                if ($operacion_info['total'] > 0) {
                    /**el total es mayor a cero 
                     * se debe de checar si todos los pagos que se la han hecho ya cubre el 100 del total para crearle el numero de titulo
                     */

                    /**modifcar para cuando haya pagos */
                } else {
                    /**la venta fue 100 gratis por lo que se genera el numero de titulo automaticamente */
                    $generar = true;
                }

                if ($generar == true) {
                    //se debe generar
                    //500 (quinientos)
                    //determino si ya esta en funcion la asignacion de numeros de titulos automaticos
                    $ajustes = Ajustes::first();
                    $numero_titulo = 0;
                    if ($ajustes->numero_titulos_sistematizados == true) {
                        //quiere decir que ya esta funcionando esto y debo elejir el numero de convenio mayor para crear el siguiente
                        $result = DB::select(DB::raw("select max(cast((CASE WHEN numero_titulo NOT LIKE '%[^0-9]%' THEN numero_titulo END) as int)) AS max_numero_titulo  from operaciones"));
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


                    $operacion = Operaciones::find($operacion_id);
                    //actualizamos la venta con su nuevo numero de titulo
                    $operacion->numero_titulo = $numero_titulo;
                    $operacion->timestamps = false;
                    $operacion->save();
                }
            }
        }
    }



    public function guardarAjustesPoliticas(Request $request, $operacion_id = 0)
    {
        /**aqui obtengo el plan de intereses con que funcionara esta venta */
        $ajustes_politicas = AjustesPoliticas::find(1);
        /**hago un registro con la informacion que afectara el control de intereses para esta venta */
        DB::table('ajustes_politicas_operacion')->insert(
            [
                'tasa_fija_anual' => $ajustes_politicas->tasa_fija_anual,
                'dias_antes_vencimiento' => $ajustes_politicas->dias_antes_vencimiento,
                'maximo_dias_retraso' => $ajustes_politicas->maximo_dias_retraso,
                'porcentaje_pena_convencional_minima' => $ajustes_politicas->porcentaje_pena_convencional_minima,
                'minima_partes_cubiertas' => $ajustes_politicas->minima_partes_cubiertas,
                'maximo_pagos_vencidos' => $ajustes_politicas->maximo_pagos_vencidos,
                'maximo_dias_cancelar_contrato' => $ajustes_politicas->maximo_dias_cancelar_contrato,
                'operaciones_id' => $operacion_id
            ]
        );
    }

    //guarda los beneficiarios de la venta de una propiedad
    public function guardarBeneficiarios(Request $request, $operacion_id = 0)
    {

        /**primero elimino beneficiarios si existen, de esta forma
         * la funcion me sirve perfecto tanto para insertar beneficiarios y actualizarlos
         */
        DB::table('beneficiarios')->where('operaciones_id', $operacion_id)->delete();

        //id del conjunto de propieades
        for ($i = 0; $i < count($request['beneficiarios']); $i++) {
            DB::table('beneficiarios')->insert(
                [
                    'nombre' => $request['beneficiarios'][$i]['nombre'],
                    'parentesco' => $request['beneficiarios'][$i]['parentesco'],
                    'telefono' => $request['beneficiarios'][$i]['telefono'],
                    'operaciones_id' => $operacion_id,
                ]
            );
        }
    }



    //guarda los beneficiarios de la venta de una propiedad
    public function programarPagos(Request $request, $operacion_id = 0, $id_venta = 0)
    {
        /**aqui comienzan a gurdar los datos */
        $subtotal = (float) $request->subtotal; //sin iva
        $iva = (float) $request->impuestos; //solo el iva
        $descuento = (float) $request->descuento;
        $costo_neto = (float) $request->costo_neto;
        $pago_inicial = (float) $request->pago_inicial;





        /**valdiando que cuadren las cantidades de la venta */

        //verificando si la venta viene con algun descuento
        /**como se genera la referencia del pago para realizar pago en bancos */
        /*•	3 dígitos según la tabla de empresa_operaciones
        •	Fecha programada del pago, 8 dígitos(ej., 20200601
        •	Numero de pago 01, 02, 12, 18, 24, 32, máximo son 64 etc. (2 dígitos)
        •	Id de la tabla origen que se incluye en la tabla de operaciones, es decir, si la operación es tipo venta de terrenos, 
        el id se tomara de la tabla ventas_terrenos, y así respectivamente el tipo de operación. De esta manera vamos incrementando 
        las referencias según el tipo de operaciones y la tabla de operaciones solo la utilizamos para darle uso de centralización de tablas.
        Ejemplo de referencia de pago
        Referencia de pago para venta de terrenos de contado realizada el día 01/junio/2020.
        •	00120200601011(3 dígitos del tipo de operación, 8 dígitos 20200601, 2 dígitos del número de pago 01 y el id de la tabla de origen 
        según la operación, en este caso de la tabla venta de terrenos, el id de la venta numero 1 o según el id de la venta).
        */


        //puede que venga con descuento pero no es del 100%
        //determinamos que tipo de ventas
        if ($request->tipo_financiamiento == 1 || (int) $request->planVenta['value'] == 1) {
            //de uso inmediato sin importar si es seleccionado a futuro o inmediato ya que selecciono pagarlo de contado
            /**se crea un solo pago */
            //se agregan 0 dias a los enganches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(1, 'day');
            $id_pago_programado_unico = DB::table('pagos_programados')->insertGetId(
                [
                    'num_pago' => 1, //numero 1, pues es unico
                    'referencia_pago' => '001' . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'fecha_programada' => $fecha_maxima, //fecha de la venta
                    'conceptos_pagos_id' => 3, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                    'monto_programado' => $costo_neto,
                    'operaciones_id' => $operacion_id,
                    'status' => 1
                ]
            );
        } else {
            //registro el enganche
            /**los pagos deben llevar los valores en proporcion al descuento 
             * por decir asi, cuando el precio lleva descuento se debe de repartir el descuento total entre los diferentes pagos
             * segun el porcentaje del pago
             */

            $resto_a_mensualidades = (float) $request->total_pagar - (float) $request->pago_incial;

            //enganche inicial mandado mas lo descontado para sacar impuestos completos



            //se agregan tres dias a los enfanches y a las liquidaciones para ser capturadas
            $fecha_maxima = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add(3, 'day');


            $id_pago_programado_enganche = DB::table('pagos_programados')->insertGetId(
                [
                    'num_pago' => 1, //numero 1, pues es unico
                    'referencia_pago' => '001' . date('Ymd', strtotime($request->fecha_venta)) . '01' . $id_venta, //se crea una referencia para saber a que pago pertenece
                    'fecha_programada' => $fecha_maxima, //fecha de la venta
                    'conceptos_pagos_id' => 1, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                    'monto_programado' => $pago_inicial,
                    'operaciones_id' => $operacion_id,
                    'status' => 1
                ]
            );

            //a futuro y a meses
            for ($i = 1; $i <= ((int) $request->planVenta['value']); $i++) {
                $numero_pago_para_referencia = '';
                if ($i < 10) {
                    //se debe asignar un cero (0) para crear la referencia correcta
                    $numero_pago_para_referencia = '0' . ($i + 1);
                } else {
                    $numero_pago_para_referencia = ($i + 1);
                }
                $fecha = Carbon::createFromformat('Y-m-d', date('Y-m-d', strtotime($request->fecha_venta)))->add($i, 'month');
                $id_pago_programado = DB::table('pagos_programados')->insertGetId(
                    [
                        'num_pago' => ($i + 1), //numero 1, pues es unico
                        'referencia_pago' => '001' . date('Ymd', strtotime($request->fecha_venta)) . $numero_pago_para_referencia . $id_venta, //se crea una referencia para saber a que pago pertenece
                        'fecha_programada' => $fecha, //fecha de la venta
                        'conceptos_pagos_id' => 2, //3-pago unico //que concepto de pago es, segun los conceptos de pago, abono, enganche o liquidacion
                        'monto_programado' => ($costo_neto - $pago_inicial) / $request->planVenta['value'],
                        'operaciones_id' => $operacion_id,
                        'status' => 1
                    ]
                );
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

    public function get_financiamientos()
    {
        $resultado = tipoPropiedades::with('precios')->withCount('precios')->get()->toArray();

        foreach ($resultado as $tipo_key => &$tipo) {
            foreach ($tipo['precios'] as $precio_key => &$precio) {
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
                    $precio['descuento_x_pago'] = ($precio['costo_neto'] - $precio['costo_neto_pronto_pago']) / $precio['financiamiento'];
                    $precio['porcentaje_pronto_pago'] = 100 - (($precio['costo_neto_financiamiento_normal'] * 100) / $precio['costo_neto']);
                } else {
                    $precio['descuento_x_pago'] = ' 0';
                    $precio['porcentaje_pronto_pago'] = ' 0';
                }
            }
        }
        return $resultado;
    }

    /**obtiene todos los tipos de propiedades */
    public function get_tipo_propiedades()
    {
        $resultado = tipoPropiedades::orderBy('id', 'asc')->get();
        return $resultado;
    }


    /**obtiene un precio por id */
    public function get_precio_by_id(Request $request)
    {
        if (!$request->id_precio) {
            return $this->errorResponse('Es necesario un id, para continuar', 409);
        }
        $resultado = PreciosPropiedades::where('id', $request->id_precio)->get()->first();
        return $resultado;
    }

    /**GUARDAR PRECIO DE PROPIEDAD*/
    public function registrar_precio_propiedad(Request $request)
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
            'tipo_propiedades_id.value' => 'required',
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
        $precio = PreciosPropiedades::where('tipo_propiedades_id', $request->tipo_propiedades_id['value'])
            ->where('financiamiento', $request->financiamiento)
            ->get()
            ->first();
        if (!empty($precio)) {
            /**ya existe el precio */
            if ($precio->status == 1) {
                return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento', 409);
            } else {
                return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento, solo debe habilitarlo nuevamente', 409);
            }
        }



        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $id_precio = 0;
            $id_precio = DB::table('precios_propiedades')->insertGetId(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' => $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' => $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b' => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago' => (float) ($request->costo_neto_pronto_pago),
                    'tipo_propiedades_id' => (int) ($request->tipo_propiedades_id['value']),
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


    /**MODIFICAR PRECIO DE PROPIEDAD*/
    public function update_precio_propiedad(Request $request)
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
            'tipo_propiedades_id.value' => 'required',
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
        $precio = PreciosPropiedades::where('tipo_propiedades_id', $request->tipo_propiedades_id['value'])
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
            }
        }



        try {
            $subtotal = (float) (($request->costo_neto / (1 + config('globales.iva_decimal'))));
            $iva = $subtotal * (config('globales.iva_decimal'));
            $costo_neto = $subtotal + $iva;
            DB::beginTransaction();
            $res = DB::table('precios_propiedades')->where('id', $request->id_precio_modificar)->update(
                [
                    'pago_inicial' => (float) $request->pago_inicial,
                    'subtotal' =>  $subtotal,
                    'impuestos' => $iva,
                    'costo_neto' =>  $costo_neto,
                    'costo_neto_financiamiento_normal' => (float) ($request->costo_neto_financiamiento_normal),
                    'descuento_pronto_pago_b' => (int) ($request->descuento_pronto_pago_b['value']),
                    'costo_neto_pronto_pago' => (float) ($request->costo_neto_pronto_pago),
                    'tipo_propiedades_id' => (int) ($request->tipo_propiedades_id['value']),
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



    /**ENABLE DISABLE PRECIO DE PROPIEDAD*/
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
        $precio = PreciosPropiedades::where('id', $request->id_precio)
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
            $res = DB::table('precios_propiedades')->where('id', $request->id_precio)->update(
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



    public function lista_precios_pdf($idioma = 'es', Request $request)
    {

        if (!($idioma == 'en' || $idioma == 'es')) {
            $idioma = 'es';
        }
        App::setLocale($idioma);

        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email =  $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        $email_to = $request->email_address;
        $datos_request = json_decode($request->request_parent[0], true);
        $financiamientos = $this->get_financiamientos();

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 2;
        $email = false;
        $email_to = 'hector@gmail.com';
¨*/
        //obtengo la informacion de esa venta

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/planes_venta/reportes', ['empresa' => $empresa, 'financiamientos' => $financiamientos, 'id_tipo_propiedad' => $datos_request['id_tipo_propiedad'], 'idioma' => $idioma]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = __('cementerio/lista_precios.titulo_reporte')  . '.pdf';
        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.planes_venta.footer'),
        ]);

        $pdf->setOptions([
            'header-html' => view('inventarios.cementerios.planes_venta.header')
        ]);


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
                $request->destinatario,
                __('cementerio/lista_precios.titulo_reporte'),
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
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
        return AntiguedadesVenta::get();
    }



    /**obtiene todas las ventas para el paginado de ventas de cementerio */
    public function get_ventas(Request $request, $id_venta = 'all', $paginated = false)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $titular = $request->titular;
        $numero_control = $request->numero_control;
        $status = $request->status;

        $resultado_query = Operaciones::with('pagosProgramados.pagados')
            ->with('venta_terreno.vendedor')
            ->with('beneficiarios')
            ->with('AjustesPoliticas')
            ->where('empresa_operaciones_id', '=', 1)
            /**solo ventas de cementerio */
            ->select(
                /**venta operacion */
                'operaciones.id as operacion_id',
                'antiguedad_operacion_id',
                'empresa_operaciones_id',
                'subtotal',
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
                'ventas_terrenos_id',
                'financiamiento',
                'aplica_devolucion_b',
                'costo_neto_financiamiento_normal',
                'comision_venta_neto',
                'operaciones.status as operacion_status',
                'clientes.id as cliente_id',
                'clientes.nombre',
                /**fin de datos de  operacion */
                DB::raw(
                    '(0) AS num_pagos_programados'
                ),
                DB::raw(
                    '(0) AS intereses'
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
                    '(CASE 
                        WHEN operaciones.status = 1 THEN "Activa"
                        ELSE "Cancelada" 
                        END) AS status_texto'
                )
            )
            ->where(function ($q) use ($id_venta) {
                if (trim($id_venta) == 'all' || $id_venta > 0) {
                    if (trim($id_venta) == 'all') {
                        $q->where('operaciones.ventas_terrenos_id', '>', $id_venta);
                    } else if ($id_venta > 0) {
                        $q->where('operaciones.ventas_terrenos_id', '=', $id_venta);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_solicitud', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_convenio', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 3) {
                        /**filtro por numero de solicitud */
                        $q->where('operaciones.numero_titulo', '=',  $numero_control);
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
            ->orderBy('operaciones.ventas_terrenos_id', 'desc')
            ->get();
        /**verificando si el usario necesita el resultado paginado, todo o por id */
        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
        }
        /**datos del cmeenterio para actualizar los valores de la ubicacion */
        $datos_cementerio = $this->get_cementerio();

        foreach ($resultado as $index_venta => &$venta) {

            /**aqui se saca el porcentaje para ver cuanto seria el descuento por pago en cada pronto pago */
            $porcentaje_descuento_pronto_pago = 0;
            if (floatval($venta['total']) > 0) {
                $porcentaje_descuento_pronto_pago = (floatval($venta['costo_neto_pronto_pago']) * 100) / floatval($venta['total']);
            }

            $venta['num_pagos_programados'] = count($venta['pagos_programados']);

            if ($venta['num_pagos_programados'] > 0) {
                /**si tiene pagos programados, eso quiere decir que la venta no tuvo 100 de descuento */
                /**recorriendo arreglo de pagos programados */
                $vencidos = 0;
                $pagos_programados_cubiertos = 0;
                $dias_vencido_primer_pago_vencido = '';
                $pagos_vigentes = 0;
                $pagos_cancelados = 0;
                $pagos_realizados = 0;
                /**guardo los dias que lleva vencido el pago vencido mas antiguo */
                foreach ($venta['pagos_programados']  as $index_programado => &$programado) {
                    if ($programado['status'] == 1) {
                        /**haciendo sumatoria de los montos que se han destinado a un pago programado segun el tipo de movimiento */
                        /**montos segun su tipo de movimiento */
                        $abonado_intereses = 0;
                        $abonado_capital = 0;
                        $descontado_pronto_pago = 0;
                        $descontado_capital = 0;
                        $complemento_cancelacion = 0;
                        $total_cubierto = 0;
                        $fecha_ultimo_pago = '';
                        foreach ($programado['pagados']  as $index_pagados => &$pagado) {
                            if ($pagado['status'] == 1) {
                                /**si esta activo el pago se toma en cuenta el monto de cada operacion */
                                /**tomando en cuenta solo pagos que son parent(todos los tipos menos abono a intereses y descuento por pronto pago, estos 2 tipos
                                 * son los que van incluidos dentro de un parent) */
                                if ($pagado['movimientos_pagos_id'] != 2 && $pagado['movimientos_pagos_id'] != 3) { //se excluyen aqui los que son de pronto pago y cobro por interes
                                    /**aqui entrarian en los abonos a capital, descuento al capital y complementos por cancelacion*/
                                    if ($pagado['movimientos_pagos_id'] == 1) {
                                        /**si es de tipo 1, abono a copital, por lo regular podria llevar asociados pagos children
                                         * y se debe de recorrer el foreach para obtener los distintos montos asignados a cada pago programado
                                         */
                                        $abonado_capital += $pagado['pagos_cubiertos']['monto'];
                                        foreach ($programado['pagados']  as $index_children => $children) {
                                            if ($children['parent_pago_id'] == $pagado['id']) {
                                                /**aqui ya encontro algun children, ahora se debe de ver a que concepto va destinado ese monto */
                                                if ($children['movimientos_pagos_id'] == 2) {
                                                    /**es abono de intereses */
                                                    $abonado_intereses += $children['pagos_cubiertos']['monto'];
                                                } else if ($children['movimientos_pagos_id'] == 3) {
                                                    /**es descuento por pronto pago */
                                                    $descontado_pronto_pago += $children['pagos_cubiertos']['monto'];
                                                }
                                            }
                                        } //fin foreach children
                                    } else  if ($pagado['movimientos_pagos_id'] == 4) {
                                        /**fue descuento al capital */
                                        $descontado_capital += $pagado['pagos_cubiertos']['monto'];
                                    } else  if ($pagado['movimientos_pagos_id'] == 5) {
                                        /**fue complemento por cancelacion */
                                        $complemento_cancelacion += $pagado['pagos_cubiertos']['monto'];
                                    }
                                    /**fecha en que se realizo el ultimo pago */
                                    $fecha_ultimo_pago = $pagado['fecha_pago'];
                                }

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
                        $programado['abonado_capital'] =  $abonado_capital;
                        $programado['abonado_intereses'] =  $abonado_intereses;
                        $programado['descontado_pronto_pago'] =  $descontado_pronto_pago;
                        $programado['descontado_capital'] =  $descontado_capital;
                        $programado['complementado_cancelacion'] =  $complemento_cancelacion;
                        $saldo_pago_programado = $programado['monto_programado'] - $abonado_capital - $descontado_pronto_pago - $descontado_capital - $complemento_cancelacion;
                        $programado['saldo_neto'] = $saldo_pago_programado;
                        /**verificando el estado del pago programado*/
                        /**verificando si la fecha sigue vigente o esta vencida */
                        /**variables para controlar el incremento por intereses */
                        $dias_retrasados_del_pago = 0;
                        $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                        $fecha_hoy = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $interes_generado = 0;

                        /**fin varables por intereses */
                        /**verificando que el pago programado tiene un saldo de capital que cobrar para saber si aplica o no intereses */
                        if ($saldo_pago_programado > 0) {
                            /**tiene todavia saldo que pagar, se debe verificar si el pago esta vencido para generarle los intereses correspondientes */
                            if (date('Y-m-d', strtotime($programado['fecha_programada'])) < date('Y-m-d')) {
                                /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                                $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                                if ($dias_vencido_primer_pago_vencido == '') {
                                    $dias_vencido_primer_pago_vencido = $dias_retrasados_del_pago;
                                }
                                $programado['fecha_a_pagar'] = date('Y-m-d');
                                /**
                                 * Los intereses moratorios se calcularán
                                 * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                                 * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                                 * ser hecho y la fecha que el contratante
                                 * liquide el adeudo.
                                 **/
                                $interes_generado = (($saldo_pago_programado * ($venta['ajustes_politicas']['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;
                                /**aqui actualizamos el saldo neto del pago con todo e intereses, quitando los intereses que ya se han pagado previamente */
                                $programado['saldo_neto'] = $saldo_pago_programado + ($interes_generado - $abonado_intereses);
                                /**la fecha qui es mayor que la fecha programada del pago */
                                $programado['status_pago'] = 0;
                                $programado['status_pago_texto'] = 'Vencido';
                                $vencidos++;
                                $programado['dias_vencido'] = $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_generado;
                            } else {
                                /**la fecha aun no vence */
                                $programado['fecha_a_pagar'] = $programado['fecha_programada'];
                                $programado['status_pago'] = 1;
                                $programado['status_pago_texto'] = 'Pendiente';
                            }
                        } else {
                            $pagos_programados_cubiertos++;
                            $programado['fecha_a_pagar'] = $pagado['fecha_pago'];
                            /**el pago programado ya fue cubierto */
                            $programado['status_pago'] = 2;
                            $programado['status_pago_texto'] = 'Pagado';
                        }
                        /**monto con pronto pago de cada abono */
                        $programado['monto_pronto_pago'] = ($porcentaje_descuento_pronto_pago * $programado['monto_programado']) / 100;

                        /**actualizando los totales de montos en la venta */
                        $venta['intereses'] +=  $interes_generado;
                        $venta['abonado_capital'] +=  $abonado_capital;
                        $venta['abonado_intereses'] +=  $abonado_intereses;
                        $venta['descontado_pronto_pago'] +=  $descontado_pronto_pago;
                        $venta['descontado_capital'] +=  $descontado_capital;
                        $venta['complementado_cancelacion'] +=  $complemento_cancelacion;
                        $venta['saldo_neto'] += $saldo_pago_programado + ($interes_generado - $abonado_intereses);
                        /**verificado el monto que seria con pronnto pago  */
                    } //fin foreach if status 1 programado 
                } //fin foreach programados
                $venta['pagos_realizados'] = $pagos_realizados;
                $venta['pagos_vigentes'] = $pagos_vigentes;
                $venta['pagos_cancelados'] = $pagos_cancelados;
                $venta['pagos_programados_cubiertos'] = $pagos_programados_cubiertos;
                $venta['pagos_vencidos'] = $vencidos;
                $venta['dias_vencidos'] = $dias_vencido_primer_pago_vencido;
            } else {
                /**la venta no tiene pagos programados debido a que fue 100% "GRATIS" */
            }

            /**verificando el tipo de venta segun financiamiento*/
            if ($venta['venta_terreno']['tipo_financiamiento'] == 1) {
                $venta['venta_terreno']['tipo_financiamiento_texto'] = 'Uso Inmediato';
            } else {
                $venta['venta_terreno']['tipo_financiamiento_texto'] = 'A Futuro';
            }

            /**actualiznado ubicacion */
            $venta['venta_terreno']['ubicacion_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'];
            $venta['venta_terreno']['area_nombre'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['area_nombre'];
            $venta['venta_terreno']['tipo_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['tipo_texto'];
            $venta['venta_terreno']['fila_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['fila_texto'];
            $venta['venta_terreno']['lote_texto'] = $this->ubicacion_texto($venta['venta_terreno']['ubicacion'], $datos_cementerio)['lote_texto'];

            /**agregando fila, lote, y tipo, por separado en valor numrico */
            $venta['venta_terreno']['fila_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[2]));
            $venta['venta_terreno']['lote_raw'] = (intval(explode("-", $venta['venta_terreno']['ubicacion'])[3]));
        } //fin foreach venta

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_ventas */
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
                'ventas_terrenos.fecha_cancelacion',
                'ventas_terrenos.nota_cancelacion',
                'ventas_terrenos.cantidad_a_regresar_cancelacion',
                'ventas_terrenos.cancelo_id',
                'ventas_terrenos.motivos_cancelacion_id',
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
                    '(0) AS numero_pagos_vigentes'
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
            ->with(array('cancelador' => function ($query) {
                $query->select('id', 'nombre');
            }))
            ->with(array('motivoCancelacion' => function ($query) {
                $query->select('id', 'motivo');
            }))
            ->with(array('programacionPagos.pagosProgramados.conceptoPago'))
            ->with(array('programacionPagos.pagosProgramados.pagosRealizados.RegistroUsuario' => function ($query) {
                $query->select('id', 'nombre');
            }))
            ->with(array('programacionPagos.pagosProgramados.pagosRealizados.Tipo'))
            ->with(array('programacionPagos.pagosProgramados.pagosRealizados.Cobrador' => function ($query) {
                $query->select('id', 'nombre');
            }))
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
                'AjustesPoliticas'
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
        $pagos_vigentes = 0;
        /**calculando el monto de interes que debe la persona */
        $ajustes_intereses = $resultado['ajustes_intereses'];

        /**fecha del primer pago vencido para scar la diferencia */
        $fecha_primer_pago_vencido = '';

        $intereses_generados = 0;
        $intereses_pagados = 0;


        /**fecha a tomar en cuenta 
         * esto se refiere  a que cuando la venta ya fue cancelada, 
         * el sistema debe considerar como ultima fecha para el calculo de intereses
         * la fecha de cancelacion, o si la venta esta activa, se debe tomar el dia en curso (fecha actual del sistema)
         */
        $ultima_fecha_para_intereses = $resultado['status'] == 1 ? date('Y-m-d') : date('Y-m-d', strtotime($resultado['fecha_cancelacion']));

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
                $interes_generado = 0;
                /**veririca si el pago vencio y no se ha pagado ndd*/
                if (count($programado['pagos_vigentes']) == 0) {


                    if (strtotime($ultima_fecha_para_intereses) > strtotime($programado['fecha_programada'])) {
                        $vencidos++;
                        $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                        $fecha_hoy = Carbon::createFromFormat('Y-m-d', $ultima_fecha_para_intereses);
                        /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                        $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                        $programado['fecha_a_pagar'] = $ultima_fecha_para_intereses;
                        /**
                         * Los intereses moratorios se calcularán
                         * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                         * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                         * ser hecho y la fecha que el contratante
                         * liquide el adeudo.
                         **/
                        $interes_generado = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                        /**actualizando los datos de vencimiento, en caso de que hayan vencido */

                        $programado['total_a_pagar'] = doubleVal($programado['total']) + $interes_generado;

                        $programado['intereses_a_pagar'] = $interes_generado;

                        $programado['vencido'] = 1;
                        /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                        $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                        $programado['intereses'] = $interes_generado;

                        if ($fecha_primer_pago_vencido == '') {
                            $fecha_primer_pago_vencido = $programado['fecha_programada'];
                        }
                    }
                } else {
                    /**hay pagos realizados */
                    $monto_pagado_interes = 0;
                    $fecha_ultimo_pago_realizado = '';
                    foreach ($programado['pagos_vigentes'] as &$realizado) {
                        /**creando la fecha del pago a texto */
                        $realizado['fecha_realizado_texto'] = fecha_abr($realizado['fecha_pago']);
                        if ($realizado['status'] == 1) {
                            $pagos_vigentes++;
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
                            $interes_generado = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;
                            if ($interes_generado < $monto_pagado_interes) {
                                if ($fecha_primer_pago_vencido == '') {
                                    $fecha_primer_pago_vencido = $programado['fecha_programada'];
                                }
                                $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_generado - $programado['intereses_pagado'];
                                $programado['intereses_a_pagar'] = $interes_generado - $programado['intereses_pagado'];
                                $vencidos++;
                                $programado['vencido'] = 1;
                                /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                                $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                                $programado['intereses'] = $interes_generado;
                                $programado['fecha_a_pagar'] = $ultima_fecha_para_intereses;
                            } else {
                                $programado['fecha_a_pagar'] = $fecha_pago_realizado;
                                /**pago cubierto en fecha y orden */
                                $programado['pagado'] = 1;
                                /**el monto es igual o mayor */
                                $pagados++;
                            }
                        }
                    } else {
                        if (strtotime($ultima_fecha_para_intereses) > strtotime($programado['fecha_programada'])) {
                            $vencidos++;
                            $programado['fecha_a_pagar'] = $ultima_fecha_para_intereses;
                            $fecha_programada_pago = Carbon::createFromFormat('Y-m-d', $programado['fecha_programada']);
                            $fecha_hoy = Carbon::createFromFormat('Y-m-d', $ultima_fecha_para_intereses);
                            /**esto me dara los dias que se retraso en el el pago la persona, que debe coincidir la suma de los * intereses cobrados */
                            $dias_retrasados_del_pago = $fecha_programada_pago->diffInDays($fecha_hoy);
                            /**
                             * Los intereses moratorios se calcularán
                             * multiplicando el monto de lo que adeude el contratante por la tasa de interés anual,
                             * dividida entre 365, este resultado se multiplica por el número de días transcurridos entre la fecha de pago que debió
                             * ser hecho y la fecha que el contratante
                             * liquide el adeudo.
                             */
                            $interes_generado = ((doubleVal($programado['total']) * ($ajustes_intereses['tasa_fija_anual'] / 12)) / 365) * $dias_retrasados_del_pago;

                            $programado['vencido'] = 1;
                            /**actualizando los datos de vencimiento, en caso de que hayan vencido */
                            $programado['dias_vencido'] =  $dias_retrasados_del_pago;
                            $programado['intereses'] = $interes_generado;

                            $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'] + $interes_generado - $programado['intereses_pagado'];
                            $programado['intereses_a_pagar'] = $interes_generado - $programado['intereses_pagado'];

                            if ($fecha_primer_pago_vencido == '') {
                                $fecha_primer_pago_vencido = $programado['fecha_programada'];
                            }
                        } else {
                            /**al no esta vencido el pago solo se actualiza el total a pagar de ese pago programado */
                            $programado['total_a_pagar'] = $programado['total'] - $programado['total_pagado'];
                        }
                    }
                }
                $intereses_generados += $interes_generado;
                $intereses_pagados += $programado['intereses_pagado'];


                /**veririficando el status del pago programado */
                if ($programado['vencido'] == 1) {
                    $programado['status_texto'] = 'vencido';
                } else {
                    if ($programado['total_a_pagar'] > 0) {
                        $programado['status_texto'] = 'pendiente';
                    } else {
                        $programado['status_texto'] = 'pagado';
                    }
                }
            }
            if ($index == 0) {
                /**solo se toma en cuenta los valores de las sumas para la programacion actual */

                $resultado['fecha_venta_texto'] = fecha_abr($resultado['fecha_venta']);
                $resultado['intereses_generados'] =  $intereses_generados;
                $resultado['intereses_pagados'] =  $intereses_pagados;
                $resultado['numero_pagos_programados'] = count($resultado['programacion_pagos'][0]['pagos_programados']);
                $resultado['numero_pagos_programados_cubiertos'] = $pagados;
                $resultado['numero_pagos_vigentes'] = $pagos_vigentes;
                $resultado['pagos_vencidos'] = $vencidos;
                if ($fecha_primer_pago_vencido != '') {
                    $fecha_hoy = Carbon::createFromFormat('Y-m-d', $ultima_fecha_para_intereses);
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
        $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$id_venta = 9;
        $email = false;
        $email_to = 'hector@gmail.com';
        */


        //obtengo la informacion de esa venta
        $datos_venta = $this->get_venta_id($id_venta);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $pdf = PDF::loadView('inventarios/cementerios/acuse_cancelacion/acuse', ['datos' => $datos_venta, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = "ACUSE DE CANCELACIÓN " . strtoupper($datos_venta['cliente_nombre']) . '.pdf';

        $pdf->setOptions([
            'title' => $name_pdf,
            'footer-html' => view('inventarios.cementerios.acuse_cancelacion.footer'),
        ]);
        if ($datos_venta['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.cementerios.acuse_cancelacion.header')
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



    public function documento_ubicacion_terreno(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        /* $email =  $request->email_send === 'true' ? true : false;
        $email_to = $request->email_address;
        $requestVentasList = json_decode($request->request_parent[0], true);
        $id_venta = $requestVentasList['venta_id'];
*/
        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        $id_venta = 5;
        $email = false;
        $email_to = 'hector@gmail.com';

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