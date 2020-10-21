<?php

namespace App\Http\Controllers;

use App\Cfdis;
use App\cfdi\ClienteFormasDigitales;
use App\Funeraria;
use App\MetodosPago;
use App\Operaciones;
use App\SatFormasPago;
use App\SatPais;
use App\SATProductosServicios;
use App\SATRegimenes;
use App\SatUnidades;
use App\SatUsosCfdi;
use App\TipoComprobantes;
use App\TiposRelacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS2D;
use Spatie\ArrayToXml\ArrayToXml;

class FacturacionController extends ApiController
{
    /**get tipo de comprobantes */
    public function get_tipos_comprobante()
    {
        $datos = TipoComprobantes::whereNotIn('id', [3, 4])->orderBy('id', 'asc')->get();
        foreach ($datos as $key => &$dato) {
            $dato['tipo'] = $dato['tipo'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_metodos_pago()
    {
        $metodos = MetodosPago::orderBy('id', 'asc')->get();

        foreach ($metodos as $key => &$metodo) {
            $metodo['metodo'] = $metodo['metodo'] . ' (' . $metodo['clave'] . ')';
        }

        return $metodos;
    }

    public function get_sat_formas_pago()
    {
        return SatFormasPago::orderBy('id', 'asc')->get();
    }

    public function get_tipos_relacion()
    {
        return TiposRelacion::orderBy('id', 'asc')->get();
    }

    public function get_claves_productos_sat()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SATProductosServicios::whereNotIn('clave', ['84111506', '42262102'])->get();

        foreach ($datos as $key => &$dato) {
            $dato['clave_original'] = $dato['clave'];
            $dato['clave']          = $dato['descripcion'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUnidades::get();

        foreach ($datos as $key => &$dato) {
            $dato['clave_original'] = $dato['clave'];

            $dato['clave'] = $dato['unidad'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_usos_cfdi()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUsosCfdi::where('aplica_b', 1)->get();

        foreach ($datos as $key => &$dato) {
            $dato['uso'] = $dato['uso'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_paises()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatPais::get();

        foreach ($datos as $key => &$dato) {
            $dato['pais'] = $dato['pais'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_empresa_tipo_operaciones()
    {
        /**los diferentes tipos de operaciones que maneja la empresa */
        $datos = [
            [
                'id'    => 1,
                'tipo'  => 'Venta de terrenos',
                'ver_b' => 1,
            ],
            [
                'id'    => 2,
                'tipo'  => 'Mantenimiento en cementerio',
                'ver_b' => 0,
            ],
            [
                'id'    => 3,
                'tipo'  => 'Servicios funerarios',
                'ver_b' => 1,
            ],
            [
                'id'    => 4,
                'tipo'  => 'Venta de plan funerario a futuro',
                'ver_b' => 1,
            ],
            [
                'id'    => 5,
                'tipo'  => 'Servicios con extremidadades',
                'ver_b' => 0,
            ],
            [
                'id'    => 6,
                'tipo'  => 'Ventas en general',
                'ver_b' => 0,
            ],
        ];

        $operaciones = [];
        foreach ($datos as $key => $value) {
            if ($value['ver_b'] == 1) {
                array_push($operaciones, $value);
            }
        }
        return $operaciones;
    }

    /**control de obtencion de operaciones para la facturacion */

    /**
     * id_operacion_local se refiere al id de la venta de propiedad, plan, servicios funerarios, etc, no a la tabla de operaciones
     */
    public function get_operaciones(Request $request, $id_operacion_local = 'all', $paginated = false)
    {
        $cliente           = $request->cliente;
        $numero_control    = $request->numero_control;
        $fecha_inicio      = $request->fecha_inicio;
        $fecha_fin         = $request->fecha_fin;
        $tipo_operacion_id = $request->tipo_operacion_id;

        $resultado_query = Operaciones::
            select(
            'empresa_operaciones_id',
            'subtotal',
            'tasa_iva',
            'descuento',
            'total',
            'operaciones.id as operacion_id',
            'clientes.id as cliente_id',
            'clientes.nombre',
            'fecha_operacion',
            'empresa_operaciones_id',
            'ventas_terrenos_id',
            'servicios_funerarios_id',
            'ventas_planes_id',
            DB::raw(
                '(NULL) AS fecha_operacion_texto'
            ),
            DB::raw(
                '(NULL) AS tipo_operacion_texto'
            ),
            DB::raw(
                '(NULL) AS conceptos'
            )
        )
            ->with('movimiento_operacion_inventario.articulos_operacion:movimientos_inventario_id,cantidad,plan_b,descuento_b,facturable_b,costo_neto_normal,costo_neto_descuento,articulos_id,sat_productos_servicios.id as sat_producto_servicio_id,sat_productos_servicios.clave as sat_producto_servicio_clave,sat_productos_servicios.descripcion as sat_producto_servicio_descripcion,articulos.descripcion as articulo_descripcion,sat_unidades.id as sat_unidad_id,sat_unidades.unidad as sat_unidad,sat_unidades.clave as sat_unidad_clave')
            ->where('operaciones.status', '<>', 0)
            ->where(function ($q) use ($tipo_operacion_id) {
                if ($tipo_operacion_id > 0) {
                    $q->where('operaciones.empresa_operaciones_id', '=', $tipo_operacion_id);
                }
            })
            ->with('venta_terreno:id,ubicacion')
            ->with('venta_plan')
            ->join('clientes', 'clientes.id', '=', 'operaciones.clientes_id')
            ->where('nombre', 'like', '%' . $cliente . '%')
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                if (trim($fecha_inicio) != '' && trim($fecha_fin) != '') {
                    if ($fecha_fin != $fecha_inicio) {
                        $q->whereBetween('fecha_operacion', [$fecha_inicio, $fecha_fin]);
                    } else {
                        $q->whereDate('fecha_operacion', '=', $fecha_inicio);
                    }
                }
            })
            ->where(function ($q) use ($numero_control) {
                if ($numero_control > 0) {
                    $q->where('ventas_terrenos_id', '=', $numero_control);
                    $q->orWhere('ventas_planes_id', '=', $numero_control);
                    $q->orWhere('servicios_funerarios_id', '=', $numero_control);
                }

            })
            ->orderBy('operaciones.fecha_operacion', 'desc')
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

        $tipos_de_operaciones = $this->get_empresa_tipo_operaciones();
/**formanteando datos */

/**obtengo el cementerio */
        $cementerio_controller = new CementerioController();

        $datos_cementerio = $cementerio_controller->get_cementerio();

        foreach ($resultado as $index_operacion => &$operacion) {
            $operacion['fecha_operacion_texto'] = fecha_abr($operacion['fecha_operacion']);

            /**asignando el tipo de operacion */
            foreach ($tipos_de_operaciones as $key => $tipo) {
                # code...
                if ($operacion['empresa_operaciones_id'] == $tipo['id']) {
                    $operacion['tipo_operacion_texto'] = $tipo['tipo'];
                    break;
                }
            }

            $conceptos = [];

            /**creando el arreglo con los conceptos que puede facturar el usuario segun la operacion*/
            if ($operacion['empresa_operaciones_id'] == 3) {
                /**es servicio funerario */
                if (isset($operacion['movimiento_operacion_inventario']['articulos_operacion'])) {
                    foreach ($operacion['movimiento_operacion_inventario']['articulos_operacion'] as $id => $concepto) {
                        if ($concepto['facturable_b'] == 1) {
                            /**solo los que  */
                            array_push($conceptos, [
                                'clave_sat'             => ['value' => $concepto['sat_producto_servicio_id'], 'label' => $concepto['sat_producto_servicio_descripcion'] . ' (' . $concepto['sat_producto_servicio_clave'] . ')'],
                                'unidad_sat'            => ['value' => $concepto['sat_unidad_id'], 'label' => $concepto['sat_unidad'] . ' (' . $concepto['sat_unidad_clave'] . ')'],
                                "cantidad"              => $concepto['cantidad'],
                                "descripcion"           => $concepto['articulo_descripcion'],
                                'descuento_b'           => $concepto['descuento_b'] == 1 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                                'modifica_b'            => 0,
                                'concepto_operacion_id' => $operacion['operacion_id'],
                                'precio_neto'           => $concepto['costo_neto_normal'],
                                'precio_descuento'      => $concepto['costo_neto_descuento'],
                            ]);
                        }

                    }
                }
            } elseif ($operacion['empresa_operaciones_id'] == 1) {
                /**es venta de terrenos */
                array_push($conceptos, [
                    'clave_sat'             => ['value' => 5, 'label' => 'Productos de entierro o tumbas (48131500)'],
                    'unidad_sat'            => ['value' => 2, 'label' => 'Pieza (H87)'],
                    "cantidad"              => 1,
                    "descripcion"           => 'Propiedad en cementerio (Ubicación ' . $cementerio_controller->ubicacion_texto($operacion['venta_terreno']['ubicacion'], $datos_cementerio)['ubicacion_texto'] . ')',
                    'descuento_b'           => $operacion['descuento'] > 0 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                    'modifica_b'            => 1,
                    'concepto_operacion_id' => $operacion['operacion_id'],
                    'precio_neto'           => $operacion['descuento'] > 0 ? round((($operacion['subtotal']) * (1 + ($operacion['tasa_iva'] / 100))), 2, PHP_ROUND_HALF_UP) : $operacion['total'],
                    'precio_descuento'      => $operacion['descuento'] > 0 ? $operacion['total'] : 0,
                ]);

            } elseif ($operacion['empresa_operaciones_id'] == 4) {
                /**es venta de planes funerarios */
                array_push($conceptos, [
                    'clave_sat'             => ['value' => 2, 'label' => 'Servicios funerarios y asociados (85171500)'],
                    'unidad_sat'            => ['value' => 1, 'label' => 'Unidad de servicio (E48)'],
                    "cantidad"              => 1,
                    "descripcion"           => 'Plan Funerario a Futuro / ' . $operacion['venta_plan']['nombre_original'],
                    'descuento_b'           => $operacion['descuento'] > 0 ? ['value' => 1, 'label' => 'SI'] : ['value' => 0, 'label' => 'NO'],
                    'modifica_b'            => 1,
                    'concepto_operacion_id' => $operacion['operacion_id'],
                    'precio_neto'           => $operacion['descuento'] > 0 ? round((($operacion['subtotal']) * (1 + ($operacion['tasa_iva'] / 100))), 2, PHP_ROUND_HALF_UP) : $operacion['total'],
                    'precio_descuento'      => $operacion['descuento'] > 0 ? $operacion['total'] : 0,
                ]);

            }

            /**actualizo los conceptos */
            $operacion['conceptos'] = $conceptos;

/**remuevo el arreglo original de datos de conceptos ya que en la parte de arriba organizo los conceptos a conveniencia */
            unset($operacion['movimiento_operacion_inventario']);
            unset($operacion['venta_plan']);
            unset($operacion['venta_terreno']);

        }

        return $resultado_query;
    }

    public function GenerarXmlCfdi(Request $request, $folio_para_asignar = '')
    {
        if ($folio_para_asignar == '') {
            /**no hay folio y se debe de cancelar el servicio */
            return 'El folio para el cfdi no es válido';
        }
        /**obteniendo claves de los catalogos */
        $forma_pago = SatFormasPago::where('id', '=', $request->forma_pago['value'])->first();
        if (is_null($forma_pago)) {
            return 'No se encontró la forma de pago que se está utilizando.';
        }

        $tipo_comprobante = TipoComprobantes::where('id', '=', $request->tipo_comprobante['value'])->first();
        if (is_null($tipo_comprobante)) {
            return 'No se encontró el tipo de comprobanteque se está utilizando.';
        }
        $metodo_pago = MetodosPago::where('id', '=', $request->metodo_pago['value'])->first();
        if (is_null($metodo_pago)) {
            return 'No se encontró el método de pago que se está utilizando.';
        }
        $uso_cfdi = SatUsosCfdi::where('id', '=', $request->uso_cfdi['value'])->first();
        if (is_null($uso_cfdi)) {
            return 'No se encontró el uso de cfdi que se está utilizando.';
        }

        /**obtengo los datos de la funeraria */
        $datos_funeraria = Funeraria::first();

        /**checabndo que tipo de comprobante es */
        $subtotal                       = 0;
        $descuento                      = 0;
        $iva_trasladado                 = 0;
        $total                          = 0;
        $serie                          = '';
        $claves_productos_servicios_sat = $this->get_claves_productos_sat();
        $get_sat_unidades               = $this->get_sat_unidades();
        $tasa_iva                       = number_format((float) round($request->tasa_iva / 100, 2), 6, '.', '');

        /**NODO INICIAL DE CONCEPTOS */
        $conceptos['cfdi:Concepto'] = [];

        /**VERIFICANDO QUE TIPO DE COMPROBANTE SE VA A REALIZAR */
        if ($request->tipo_comprobante['value'] == '1') {
/**validnado que tenga conceptos si es de tipo ingreso */

            if (count($request->conceptos) == 0) {
                /**no hay conceptos y no se puede seguir con el timbrado */
                return 'Se deben agregar conceptos para el CFDI.';
            }

            $serie = 'I';
            /**ingreso */
            /**CREANDO LOS NODOS DE CONCEPTO */
            foreach ($request->conceptos as $key => $concepto) {
                //cargo la clave del sat
                $esta_clave_sat = false;
                $clave_sat      = '';
                foreach ($claves_productos_servicios_sat as $key => $clave) {
                    if ($clave->id == $concepto['clave_sat']['value']) {
                        $esta_clave_sat = true;
                        $clave_sat      = $clave['clave_original'];
                        break;
                    }
                }
                if (!$esta_clave_sat) {
                    return 'No se encontró la clave de producto del Sat.';
                }
                //cargo la unidad
                $esta_clave_unidad = false;
                $clave_unidad      = '';
                foreach ($get_sat_unidades as $key => $clave) {
                    if ($clave->id == $concepto['unidad_sat']['value']) {
                        $esta_clave_unidad = true;
                        $clave_unidad      = $clave['clave_original'];
                        break;
                    }
                }

                if (!$esta_clave_unidad) {
                    return 'No se encontró la clave de unidad del Sat.';
                }

                $ValorUnitarioPrecioNeto      = (float) ($concepto['precio_neto'] / (1 + ($request->tasa_iva / 100)));
                $ValorUnitarioPrecioDescuento = $concepto['precio_descuento'] / (1 + ($request->tasa_iva / 100));
                $descuento_concepto           = $concepto['descuento_b']['value'] == 1 ? ($ValorUnitarioPrecioNeto - $ValorUnitarioPrecioDescuento) : 0;

                $subtotal += $ValorUnitarioPrecioNeto * $concepto['cantidad'];
                $descuento += $descuento_concepto * $concepto['cantidad'];
                $iva_trasladado += (($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * $tasa_iva;
                $total += (($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * (1 + $tasa_iva);
                array_push($conceptos['cfdi:Concepto'],
                    [
                        '_attributes'    =>
                        [
                            'Cantidad'      => $concepto['cantidad'],
                            'ClaveProdServ' => $clave_sat,
                            'ClaveUnidad'   => $clave_unidad,
                            'Descripcion'   => trim(($concepto['descripcion'])),
                            'Importe'       => number_format((float) round($concepto['cantidad'] * $ValorUnitarioPrecioNeto, 2), 2, '.', ''),
                            'ValorUnitario' => number_format((float) round($ValorUnitarioPrecioNeto, 2), 2, '.', ''),
                            'Descuento'     => number_format((float) round($descuento_concepto * $concepto['cantidad'], 2), 2, '.', ''),
                        ],
                        'cfdi:Impuestos' => [
                            'cfdi:Traslados' => [
                                'cfdi:Traslado' => [
                                    '_attributes' => [
                                        'Base'       => number_format((float) round(($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad'], 2), 2, '.', ''),
                                        'Importe'    => number_format((float) round((($ValorUnitarioPrecioNeto - $descuento_concepto) * $concepto['cantidad']) * $tasa_iva, 2), 2, '.', ''),
                                        'Impuesto'   => "002", //IVA
                                        'TasaOCuota' => $tasa_iva,
                                        'TipoFactor' => "Tasa",
                                    ],
                                ],
                            ],
                        ],
                    ],
                );
                /**verificando si se debe de quitar el nodo de Descuento */
                if ($concepto['descuento_b']['value'] != 1) {
                    /**no hay descuento y debe quitarse*/
                    unset($conceptos['cfdi:Concepto'][count($conceptos['cfdi:Concepto']) - 1]['_attributes']['Descuento']);
                }
            }
        } else if ($request->tipo_comprobante['value'] == '2') {
            /**egreso */
            $serie = 'E';
        } else if ($request->tipo_comprobante['value'] == '5') {
            /**pago */
            $serie = 'P';

            foreach ($request->cfdis_a_pagar as $key => $cfdi) {
                $total += $cfdi['monto_pago'];
            }
            array_push($conceptos['cfdi:Concepto'],
                [
                    '_attributes' =>
                    [
                        'Cantidad'      => 1,
                        'ClaveProdServ' => '84111506',
                        'ClaveUnidad'   => 'ACT',
                        'Descripcion'   => 'Pago',
                        'Importe'       => 0,
                        'ValorUnitario' => 0,
                    ],
                ]
            );
            /**agregago los pagos relacionados */
        } else {
            /**error */
            return 'No se encontró tipo de comprobante que se está utilizando.';
        }

        /**creando nodos de EMISOR Y RECEPTOR Y AGREGANDO LOC CONCEPTOS QUE VAN A APLICAR A ESTE CFDI*/
        $array = [
            'cfdi:Emisor'      => [
                '_attributes' => [
                    'RegimenFiscal' => '601',
                    'Rfc'           => ENV('APP_ENV') == 'local' ? 'LAN7008173R5' : strtoupper($datos_funeraria['rfc']),
                    'Nombre'        => ENV('APP_ENV') == 'local' ? 'EMISOR DE PRUEBAS SA DE CV' : strtoupper($datos_funeraria['razon_social']),
                ],
            ],
            'cfdi:Receptor'    => [
                '_attributes' => [
                    'Rfc'     => ENV('APP_ENV') == 'local' ? 'XEXX010101000' : strtoupper($request->rfc),
                    'Nombre'  => ENV('APP_ENV') == 'local' ? 'RECEPTOR DE PRUEBAS SA DE CV' : strtoupper($request->razon_social),
                    'UsoCFDI' => $uso_cfdi['clave'],
                ],
            ],
            'cfdi:Conceptos'   => $conceptos,
            'cfdi:Impuestos'   => [
                '_attributes'    => [
                    'TotalImpuestosTrasladados' => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                ],
                'cfdi:Traslados' => [
                    'cfdi:Traslado' => [
                        '_attributes' => [
                            'Importe'    => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                            'Impuesto'   => "002",
                            'TasaOCuota' => $tasa_iva,
                            'TipoFactor' => "Tasa",
                        ],
                    ],
                ],
            ],
            'cfdi:Complemento' => [
                'pago10:Pagos' => [
                    '_attributes' => [
                        'Version' => '1.0',
                    ],
                    'pago10:Pago' => [
                        '_attributes'             => [
                            'FechaPago'    => $request->fecha_pago . 'T12:00:00',
                            'FormaDePagoP' => $forma_pago['clave'],
                            'MonedaP'      => 'MXN',
                            'Monto'        => number_format((float) round($total, 2), 2, '.', ''),
                        ],
                        'pago10:DoctoRelacionado' => $request->array_cfdis_a_pagar_xml,
                    ],
                ],
            ],
        ];

        if ($request->tipo_comprobante['value'] == '5') {
            /**pago */
        }

        /**AQUI AGREGO LOS PAGOS QUE SE GENERARON */
        $numero_serie = 0;
        if ($request->tipo_comprobante['value'] == 1) {
            $schema_location = 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
            $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 1)->count();
        } else if ($request->tipo_comprobante['value'] == 2) {
            $schema_location = '';
            $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 2)->count();
        } else {
            if ($request->tipo_comprobante['value'] == 5) {
                $numero_serie    = Cfdis::where('sat_tipo_comprobante_id', '=', 5)->count();
                $schema_location = 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd';
            }
        }
        $comprobante = [
            'xmlns:cfdi'         => 'http://www.sat.gob.mx/cfd/3',
            'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
            'xmlns:pago10'       => 'http://www.sat.gob.mx/Pagos',
            'Certificado'        => '',
            'Fecha'              => str_replace(" ", "T", date("Y-m-d H:i:s")),
            'Folio'              => $folio_para_asignar,
            'FormaPago'          => $forma_pago['clave'],
            'LugarExpedicion'    => $datos_funeraria['cp'],
            'MetodoPago'         => $metodo_pago['clave'],
            'Moneda'             => $request->tipo_comprobante['value'] != '5' ? "MXN" : 'XXX',
            'NoCertificado'      => '',
            'Sello'              => '',
            'Serie'              => $serie . ($numero_serie),
            'SubTotal'           => $request->tipo_comprobante['value'] != '5' ? number_format((float) round($subtotal, 2), 2, '.', '') : '0',
            'Descuento'          => number_format((float) round($descuento, 2), 2, '.', ''),
            'TipoCambio'         => '1',
            'TipoDeComprobante'  => $tipo_comprobante['clave'],
            'Total'              => $request->tipo_comprobante['value'] != '5' ? number_format((float) round($total, 2), 2, '.', '') : '0',
            'Version'            => '3.3',
            'xsi:schemaLocation' => $schema_location,
        ];

        if ($request->tipo_comprobante['value'] != '5') {
            unset($comprobante['xmlns:pago10']);
            unset($array['cfdi:Complemento']);
        } else {
            unset($comprobante['FormaPago']);
            unset($comprobante['TipoCambio']);
            unset($comprobante['MetodoPago']);
            unset($array['cfdi:Impuestos']);
        }

        if ($descuento == 0) {
            /**no hay descuento y debe quitarse */
            unset($comprobante['Descuento']);
        }

        $result = ArrayToXml::convert($array, [
            'rootElementName' => 'cfdi:Comprobante',
            '_attributes'     => $comprobante,
        ], true, 'UTF-8');

        //$nombre_temporal = rand(0, 100000) . '.xml';
        //$nombre_temporal = 'ok.xml';
        $nombre_temporal = $folio_para_asignar . '.xml';
        Storage::disk('cfdis')->put($nombre_temporal, $result);

        $arreglo_de_datos_a_retornar['nombre_xml'] = $nombre_temporal;
        $arreglo_de_datos_a_retornar['subtotal']   = $subtotal;
        $arreglo_de_datos_a_retornar['descuento']  = $descuento;
        $arreglo_de_datos_a_retornar['total']      = $total;

        return $arreglo_de_datos_a_retornar;
    }

    public function timbrar_cfdi(Request $request)
    {
        $validaciones = [
            /**validacion de datos para el cfdi */
            'id_cliente'               => 'required|integer|min:1',
            'cliente'                  => 'required',
            'tipo_rfc.value'           => 'required',
            'rfc'                      => '',
            'razon_social'             => '',
            'direccion_fiscal'         => '',
            'sat_pais.value'           => 'required',
            'tipo_comprobante.value'   => 'required',
            'metodo_pago.value'        => 'required',
            'forma_pago.value'         => 'required',
            'fecha_pago'               => '',
            'uso_cfdi.value'           => 'required',
            'tipo_relacion.value'      => '',
            'cfdis_relacionados'       => '',
            'operaciones_relacionadas' => '',
            'conceptos'                => '',
            'tasa_iva'                 => 'required|numeric|min:16|max:16',
        ];

        /**VALIDACIONES CONDICIONADAS RESPECTO AL TIPO DE RFC*/
        if ($request->tipo_rfc['value'] == 1) {
            /**ocupa rfc que lo escriban, minimo 12 caracteres */
            $validaciones['rfc']          = 'required|min:12';
            $validaciones['razon_social'] = 'required';
            /**asigando pais por defecto mexico id 151 */
            $request->merge([
                'sat_pais.value' => 151,
            ]);

        } elseif ($request->tipo_rfc['value'] == 2) {
            /**es publico en general */
            $request->merge([
                'rfc' => 'XAXX010101000',
            ]);
            $request->merge([
                'razon_social' => 'público en general',
            ]);
            //$request->direccion_fiscal = 'N/A';
            /**asigando pais por defecto mexico id 151 */
            $request->merge([
                'sat_pais.value' => 151,
            ]);

        } else {
            /**es publico gral extranjero */
            $request->merge([
                'rfc' => 'XEX010101000',
            ]);
            $request->merge([
                'razon_social' => 'público en general extranjero',
            ]);
            //$request->direccion_fiscal = 'N/A';
            $validaciones['sat_pais'] = 'required';
        }

        /**VALIDANDO EL TIPO DE COMPROBANTE */
        if ($request->tipo_comprobante['value'] == 1) {
            /**es de tipo ingreso y ocupa conceptos */
        } elseif ($request->tipo_comprobante['value'] == 2) {
            /**es egreso */
        } else {
            /**es pago debe ser id 5 */
            $validaciones['fecha_pago'] = 'required';
            /**metodo pago pue */
            $request->merge([
                'metodo_pago.value' => 1,
            ]);
            /**P01, por definir, por regla del sat */
            $request->merge([
                'uso_cfdi.value' => 22,
            ]);
        }
        /**MENSAJES DE LAS VALIDACIONES*/
        $mensajes = [
            'integer'  => 'Ingrese un número entero',
            'numeric'  => 'Ingrese un valor numérico',
            'required' => 'Dato obligatorio',
            'min'      => "Este valor debe ser mínimo :min",
            'max'      => "Este valor debe ser máximo :max",
        ];

        /**obtenermos el folio para asignar al cfdi */

        /**VALIDANDO LOS DATOS */
        request()->validate(
            $validaciones,
            $mensajes
        );
        /**LAS VALIDACIONES HAN PASADO Y SE DEBEN VALIDAR LOS CAMPOS QUE APLICAN SEGUN EL CASO*/
        if ($request->tipo_comprobante['value'] == '1') {
            /**ES DE TIPO INGRESO */
            if ($request->metodo_pago['value'] == '2') {
                /**es PPD */
                if ($request->forma_pago['value'] != 9) {
                    /**es diferente de POR DEFINIR */
                    return $this->errorResponse('La forma de pago debe ser por definir para este tipo de comprobante.', 409);
                }
            }
        } else if ($request->tipo_comprobante['value'] == '5') {

        }

        /**procede con las validaciones y se hace la insercion de la transanccion en la base de datos */
        $datos_funeraria = Funeraria::first();
/**COMENZANDO A GUARDAR EL CFDI EN LA BASE DE DATOS */
        try {
            DB::beginTransaction();
            /**GUARDAMOS LA INFO INICIAL DEL  */
            $folio_para_asignar = DB::table('cfdis')->insertGetId(
                [
                    'uuid'                        => null,
                    'tasa_iva'                    => $request->tasa_iva,
                    'clientes_id'                 => $request->id_cliente,
                    'serie'                       => null,
                    'fecha'                       => null,
                    'sat_formas_pago_id'          => $request->forma_pago['value'],
                    'subtotal'                    => 0,
                    'descuento'                   => 0,
                    'fecha_pago'                  => $request->tipo_comprobante['value'] == 5 ? $request->fecha_pago : null,
                    'sat_monedas_id'              => 1, //peso mxn
                    'tipo_cambio'                 => 1,
                    'total'                       => 0,
                    'sat_tipo_comprobante_id'     => $request->tipo_comprobante['value'],
                    'sat_metodos_pago_id'         => $request->metodo_pago['value'],
                    'rfc_emisor'                  => $datos_funeraria->rfc,
                    'nombre_emisor'               => $datos_funeraria->nombre_comercial,
                    'sat_regimenes_id'            => 1, //personas morales
                    'sat_pais_id'                 => $request->sat_pais['value'],
                    'rfc_receptor'                => $request->rfc,
                    'nombre_receptor'             => $request->razon_social,
                    'residencia_fiscal_receptor'  => $request->direccion_fiscal,
                    'sat_usos_cfdi_id'            => $request->uso_cfdi['value'],
                    'fecha_timbrado'              => null,
                    'rfc_proveedor_certificado'   => null,
                    'fecha_registro'              => now(),
                    'nota'                        => $request->nota,
                    'timbro_id'                   => (int) $request->user()->id,
                    'num_operacion'               => null,
                    'rfc_emisor_cta_ordenante'    => null,
                    'nombre_banco_ordenante'      => null,
                    'rfc_emisor_cta_beneficiario' => null,
                    'cta_beneficiario'            => null,
                    'tipos_cadena_pago_clave'     => null,
                    'sat_tipo_relacion_id'        => $request->tipo_relacion['value'],
                ]
            );

            if ($request->tipo_comprobante['value'] == 1) {
                /**ingreso */
                /**GUARDANDO OPERACIONES RELACIONADAS */
                if (isset($request->operaciones_relacionadas)) {
                    if (count($request->operaciones_relacionadas) > 0) {
                        /**tiene operaciones relacionadas */
                        foreach ($request->operaciones_relacionadas as $key => $operacion) {
                            //return $this->errorResponse($operacion['operacion_id'], 409);
                            DB::table('cfdis_operaciones')->insert(
                                [
                                    'cfdis_id'       => $folio_para_asignar,
                                    'operaciones_id' => $operacion['operacion_id'],
                                ]
                            );
                        }
                    }
                }
                /**GUARDANDO CONCEPTOS DEL CFDI */
                if (isset($request->conceptos)) {
                    if (count($request->conceptos) > 0) {
                        /**tiene conceptos */
                        foreach ($request->conceptos as $key => $concepto) {
                            //return $this->errorResponse($concepto['clave_sat']['value'], 409);
                            $ValorUnitarioPrecioNeto      = (float) ($concepto['precio_neto'] / (1 + ($request->tasa_iva / 100)));
                            $ValorUnitarioPrecioDescuento = $concepto['precio_descuento'] / (1 + ($request->tasa_iva / 100));
                            $descuento_concepto           = $concepto['descuento_b']['value'] == 1 ? ($ValorUnitarioPrecioNeto - $ValorUnitarioPrecioDescuento) : 0;

                            $valor_unitario_real = $concepto['descuento_b']['value'] == 1 ? $ValorUnitarioPrecioDescuento : $ValorUnitarioPrecioNeto;
                            DB::table('conceptos_cfdi')->insert(
                                [
                                    'sat_productos_servicios_id' => $concepto['clave_sat']['value'],
                                    'cantidad'                   => $concepto['cantidad'],
                                    'descripcion'                => $concepto['descripcion'],
                                    'valor_unitario'             => $valor_unitario_real,
                                    'importe'                    => $valor_unitario_real * $concepto['cantidad'],
                                    'descuento'                  => $descuento_concepto,
                                    'sat_unidades_id'            => $concepto['unidad_sat']['value'],
                                    'concepto_operacion_id'      => $concepto['concepto_operacion_id'],
                                    'cfdis_id'                   => $folio_para_asignar,
                                    'concepto_operacion_ver_b'   => $concepto['concepto_operacion_ver_b'],
                                    'modifica_b'                 => $concepto['modifica_b'],
                                ]
                            );
                        }
                    } else {
                        /**regreso el id de la base de datos que se iba consumir */
                        $this->regresar_bd_folio();
                        return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                    }
                } else {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    return $this->errorResponse('Ingrese los conceptos a facturar.', 409);
                }
            } else if ($request->tipo_comprobante['value'] == '5') {
                /**ES DE TIPO PAGO, REVISANDO QUE LOS CFDIS A PAGAR SEAN DE TIPO PAGO, QUE ESTEN VIGENTES Y EL MONTO SEA VALIDO */
                $cfdis_a_pagar          = [];
                $total_comprobante_pago = 0;
                if (isset($request->cfdis_a_pagar)) {
                    if (count($request->cfdis_a_pagar) > 0) {
                        foreach ($request->cfdis_a_pagar as $key => $cfdi) {
                            if (isset($cfdi['id'])) {
                                array_push($cfdis_a_pagar, $cfdi['id']);
                                $total_comprobante_pago += $cfdi['monto_pago'];
                            } else {
                                /**regreso el id de la base de datos que se iba consumir */
                                $this->regresar_bd_folio();
                                return $this->errorResponse('El folio del cfdi que ingresó no es válido.', 409);
                            }
                        }
                        /**una vez verificados que los cfdis existen, se mandan traer de la bd para verificar que el total pagado permite el monto
                         * del abonos
                         */
                        $cfdis_de_bd = Cfdis::select(
                            'status',
                            'uuid',
                            'id',
                            'total',
                            'sat_tipo_comprobante_id',
                            'sat_metodos_pago_id'
                        )->with('pagos_asociados')
                            ->whereIn('id', $cfdis_a_pagar)->get();

                        if (count($cfdis_de_bd) == 0) {
                            /**regreso el id de la base de datos que se iba consumir */
                            $this->regresar_bd_folio();
                            return $this->errorResponse('No se han encontrado cfdis a pagar', 409);
                        }

                        /**validamos que todo esté segun las validaciones del SAT */
                        foreach ($cfdis_de_bd as $key => $cfdi_bd) {
                            if ($cfdi_bd['sat_tipo_comprobante_id'] == 1 && $cfdi_bd['sat_metodos_pago_id'] == 2) {
                                /**se procede con el pago, al ser un cfdi de tipo ingreso y ppd */
                                $total_pagado       = 0;
                                $numero_parcialidad = 1;
                                foreach ($cfdi_bd['pagos_asociados'] as $key_pago => $pago) {
                                    if ($pago['status'] == 1) {
                                        /**pago activo */
                                        $total_pagado += $pago['monto_relacion'];
                                        if ($pago['tipo_relacion_id'] == 2) {
                                            /**solo para relacion de tipo pago */
                                            $numero_parcialidad++;
                                        }
                                    }
                                }

                                /**creo el array con los pagos relacionados del xml */
                                $array_cfdis_a_pagar_xml = [];

                                foreach ($request->cfdis_a_pagar as $key_pagar => $cfdi_pagar) {
                                    if ($cfdi_pagar['id'] == $cfdi_bd['id']) {
                                        if ($cfdi_pagar['monto_pago'] > 0) {
                                            if (($total_pagado + $cfdi_pagar['monto_pago']) <= $cfdi_bd['total']) {
                                                /**procede la relacion del cfdi para pago */
                                                DB::table('cfdis_tipo_relacion')->insert(
                                                    [
                                                        'numero_parcialidad'   => $numero_parcialidad,
                                                        'sat_metodos_pago_id'  => $cfdi_bd['sat_metodos_pago_id'],
                                                        'monto_relacion'       => $cfdi_pagar['monto_pago'],
                                                        'tipo_relacion_id'     => 2, //pago
                                                        'cfdis_id_relacionado' => $cfdi_bd['id'],
                                                        'cfdis_id'             => $folio_para_asignar,
                                                    ]
                                                );
                                                $metodo_pago = MetodosPago::where('id', '=', $cfdi_bd['sat_metodos_pago_id'])->first();
                                                if (is_null($metodo_pago)) {
                                                    $this->regresar_bd_folio();
                                                    return 'No se encontró el método de pago que se está utilizando.';
                                                }
                                                array_push($array_cfdis_a_pagar_xml, [

                                                    '_attributes' => [
                                                        'Folio'            => $cfdi_bd['id'],
                                                        'IdDocumento'      => $cfdi_bd['uuid'],
                                                        'ImpPagado'        => $cfdi_pagar['monto_pago'],
                                                        'ImpSaldoAnt'      => $cfdi_bd['total'] - $total_pagado,
                                                        'ImpSaldoInsoluto' => $cfdi_bd['total'] - $total_pagado - $cfdi_pagar['monto_pago'],
                                                        'MetodoDePagoDR'   => $metodo_pago['clave'],
                                                        'MonedaDR'         => "MXN",
                                                        'NumParcialidad'   => $numero_parcialidad,
                                                    ],
                                                ]);
                                            } else {
                                                /**No aplica por que le pago es mayor a lo que resta de pagar */
                                                $this->regresar_bd_folio();
                                                return $this->errorResponse('El monto a pagar debe ser menor o igual a $ ' . number_format($cfdi_bd['total'] - $total_pagado, 2), 409);
                                            }
                                        } else {
                                            /**regreso el id de la base de datos que se iba consumir */
                                            $this->regresar_bd_folio();
                                            return $this->errorResponse('El monto a pagar de los comprobantes debe ser mayor a cero.', 409);
                                        }
                                        break;
                                    }
                                }
                                $request->merge([
                                    'array_cfdis_a_pagar_xml' => $array_cfdis_a_pagar_xml,
                                ]);

                            } else {
                                /**regreso el id de la base de datos que se iba consumir */
                                $this->regresar_bd_folio();
                                return $this->errorResponse('Verifique que los CFDIs son de tipo ingreso y PPD', 409);
                            }
                        }
                    } else {
                        /**regreso el id de la base de datos que se iba consumir */
                        $this->regresar_bd_folio();
                        return $this->errorResponse('Ingrese los cfdis a pagar.', 409);
                    }
                } else {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    return $this->errorResponse('Ingrese los cfdis a pagar.', 409);
                }
            }

            /**GUARDANDO CFDIS RELACIONADOS PENDIENTES */

            header('Content-type: text/html; charset=utf-8');
            try {
                set_time_limit(0);
                ini_set('display_errors', true);
                ini_set("soap.wsdl_cache_enabled", "0");
                date_default_timezone_set("America/Mazatlan");
                /**mandamos crear el XML, con el nombre temporal del folio registrado en la parte superior */
                $xml_a_timbrar = $this->GenerarXmlCfdi($request, $folio_para_asignar);
                /**verificando que el xml se haya genrado sin errores */
                if ($xml_a_timbrar['nombre_xml'] != $folio_para_asignar . '.xml') {
                    Storage::disk($storage_disk_xmls)->delete($xml_a_timbrar['nombre_xml']);
                    return $this->errorResponse($responseTimbre->acuseCFDI->error, 409);

                    /**el xml no se creo */
                    return $this->errorResponse('Ocurrió un error al generar el XML, reintente por favor.', 409);
                }

                /* carga archivo xml */
                $storage_disk_credentials = ENV('STORAGE_DISK_CREDENTIALS');
                $storage_disk_xmls        = ENV('STORAGE_DISK_XML');
                if (ENV('APP_ENV') == 'local') {
                    $certificado_path = ENV('CER_PAC');
                    $key_path         = ENV('KEY_PAC');
                    $usuario          = ENV('USER_PAC');
                    $password         = ENV('PASSWORD_PAC');
                    $root_path_cer    = ENV('ROOT_CER_DEV');
                    $root_path_key    = ENV('ROOT_KEY_DEV');
                } else {
                    /**data from DB */
                    $certificado_path = ENV('CER_PAC'); //sistema
                    $key_path         = ENV('KEY_PAC'); //sistema
                    $usuario          = ENV('USER_PAC');
                    $password         = ENV('PASSWORD_PAC');
                    $root_path_cer    = ENV('ROOT_CER_PROD');
                    $root_path_key    = ENV('ROOT_KEY_PROD');
                }
                $certFile                = Storage::disk($storage_disk_credentials)->path($root_path_cer . $certificado_path);
                $keyFile                 = Storage::disk($storage_disk_credentials)->path($root_path_key . $key_path . '.pem');
                $contenido_xml_a_timbrar = Storage::disk($storage_disk_xmls)->path($xml_a_timbrar['nombre_xml']);
                /**mandamos llamar la clase del PAC*/
                $clienteFD = new ClienteFormasDigitales($contenido_xml_a_timbrar);
                /* se le pasan los datos de acceso */
                $autentica           = new Autenticar();
                $autentica->usuario  = $usuario;
                $autentica->password = $password;
                $parametros          = new Parametros();
                $parametros->accesos = $autentica;
                //$this->errorResponse($clienteFD->sellarXML($certFile, $keyFile), 409);
                /**SE MANDA SELLAR EL XML */
                $clienteFD->sellarXML($certFile, $keyFile);
                $retorno_del_sellado     = $clienteFD->sellarXML($certFile, $keyFile);
                $parametros->comprobante = $retorno_del_sellado['xml'];
                $cadena_original_cfdi    = $retorno_del_sellado['cadena_original'];
                /* se manda el xml a TIMBRAR */
                $responseTimbre = $clienteFD->timbrar($parametros);

                if (isset($responseTimbre->acuseCFDI->error)) {
                    /**regreso el id de la base de datos que se iba consumir */
                    $this->regresar_bd_folio();
                    //return $this->errorResponse(($responseTimbre->acuseCFDI->codigoError), 409);
                    /**eliminar el xml ya que no se timbro*/
                    Storage::disk($storage_disk_xmls)->delete($xml_a_timbrar['nombre_xml']);
                    return $this->errorResponse($responseTimbre->acuseCFDI->error, 409);
                }
                /**verificando si esta timbrado el cfdi */
                if (isset($responseTimbre->acuseCFDI->xmlTimbrado)) {
                    /**EL XML SE TIMBRO CORRECTAMENTE */
                    /**se comiezna a guardar el resultado en la base de datos */
                    /**se actualiza el xml */
                    Storage::disk($storage_disk_xmls)->put($xml_a_timbrar['nombre_xml'], $responseTimbre->acuseCFDI->xmlTimbrado);
                    $xml_timbrado_file = Storage::disk($storage_disk_xmls)->get($xml_a_timbrar['nombre_xml']);
                    $xml_timbrado      = $this->leer_xml($folio_para_asignar);

                    $total = 0;
                    if ($request->tipo_comprobante['value'] == '1') {
                        /**pago */
                        $total = $xml_timbrado['Comprobante']['Total'];
                    } else if ($request->tipo_comprobante['value'] == '2') {
                        /**egregso */

                    } else {
                        if ($request->tipo_comprobante['value'] == '5') {
                            /**pago */
                            $total = $total_comprobante_pago;
                        }
                    }
                    DB::table('cfdis')->where('id', '=', $folio_para_asignar)->update(
                        [
                            'uuid'                        => $xml_timbrado['Complemento']['TimbreFiscalDigital']['UUID'],
                            'version'                     => $xml_timbrado['Comprobante']['Version'],
                            'serie'                       => $xml_timbrado['Comprobante']['Serie'],
                            'fecha'                       => $xml_timbrado['Comprobante']['Fecha'],
                            'subtotal'                    => $xml_timbrado['Comprobante']['SubTotal'],
                            'descuento'                   => $xml_timbrado['Comprobante']['Descuento'],
                            'total'                       => $total,
                            'fecha_timbrado'              => $xml_timbrado['Complemento']['TimbreFiscalDigital']['FechaTimbrado'],
                            'rfc_proveedor_certificado'   => $xml_timbrado['Complemento']['TimbreFiscalDigital']['RfcProvCertif'],
                            'num_operacion'               => null,
                            'rfc_emisor_cta_ordenante'    => null,
                            'nombre_banco_ordenante'      => null,
                            'rfc_emisor_cta_beneficiario' => null,
                            'cta_beneficiario'            => null,
                            'tipos_cadena_pago_clave'     => null,
                            'cadena_original'             => $cadena_original_cfdi,
                            'xml_timbrado'                => $xml_timbrado_file,
                        ]
                    );
                    DB::commit();
                    return $folio_para_asignar . 'ff';
                    //$clienteFD = new ClienteFormasDigitales($contents = Storage::disk($storage_disk_xmls)->path($file_guardar));
                    //return $clienteFD->generarCadenaOriginal();
                }
            } catch (SoapFault $e) {
                $this->regresar_bd_folio();
                print("Auth Error:::: $e");
            }
            /**SE MANDA REGISTRAR LA TRANSACCIOEN LA BASE DE DATOS */
        } catch (\Throwable $th) {
            /**regreso el id de la base de datos que se iba consumir */
            $this->regresar_bd_folio();
            return $th;
        }

    }

    public function regresar_bd_folio()
    {
        DB::rollBack();
        $maxId = DB::table('cfdis')->max('id');
        if (trim($maxId) > 0) {
            DB::statement("ALTER TABLE cfdis AUTO_INCREMENT=$maxId");
        }
    }

    /**leo los datos del xml para guardar en la base de datos */
    public function leer_xml($folio_xml = '')
    {

        $id_folio_cfdi = 0;
        //EMPIEZO A LEER LA INFORMACION DEL CFDI
        if (trim($folio_xml) == '') {
            return $this->errorResponse('Debe especificar una ruta del archivo .xml', 409);
        } else {
/* carga archivo xml */
            $storage_disk_xmls = ENV('STORAGE_DISK_XML');
            $id_folio_cfdi     = $folio_xml;
            $folio_xml         = Storage::disk($storage_disk_xmls)->path($folio_xml . '.xml');
        }
        if (File::exists($folio_xml)) {
            $xml = simplexml_load_file($folio_xml);
        } else {
            return $this->errorResponse('El xml que indicó no existe.', 409);
        }

        /**se trae la informacion del cfdi de la bd */

        $cfdi = Cfdis::where('id', '=', $id_folio_cfdi)->first();

        $tasa_iva = number_format((float) round($cfdi->tasa_iva / 100, 2), 6, '.', '');

        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('cfdi', 'http://www.sat.gob.mx/cfd/3');
        $xml->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
        $comprobante = $xml->xpath('//cfdi:Comprobante')[0];

        $emisor   = $xml->xpath('//cfdi:Emisor')[0];
        $receptor = $xml->xpath('//cfdi:Receptor')[0];
        /**determinando el regimen */
        $regimen  = SATRegimenes::select('regimen')->where('clave', (string) $emisor['RegimenFiscal'])->first();
        $uso_cfdi = SatUsosCfdi::select('uso')->where('clave', (string) $receptor['UsoCFDI'])->first();

        /**agregando conceptos */
        $conceptos = [];
        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $concepto) {
            $traslado = [];
            /**agregando traslado */

            $descuento = !empty((string) $concepto['Descuento']) ? (string) $concepto['Descuento'] : 0;

            array_push($traslado, [
                'Base'       => number_format((float) round(((string) $concepto['ValorUnitario'] - ($descuento / $concepto['Cantidad'])) * ((string) $concepto['Cantidad']), 2), 2, '.', ''),
                'Importe'    => number_format((float) round((((string) $concepto['ValorUnitario'] - ($descuento / $concepto['Cantidad'])) * (string) $concepto['Cantidad']) * .16, 2), 2, '.', ''),
                'Impuesto'   => '002',
                'TasaOCuota' => $tasa_iva,
                'TipoFactor' => 'Tasa',
            ]);

            array_push($conceptos, [
                'ClaveProdServ' => (string) $concepto['ClaveProdServ'],
                'ClaveUnidad'   => (string) $concepto['ClaveUnidad'],
                'Importe'       => (string) $concepto['Importe'],
                'Cantidad'      => (string) $concepto['Cantidad'],
                'Descripcion'   => (string) $concepto['Descripcion'],
                'ValorUnitario' => (string) $concepto['ValorUnitario'],
                'Descuento'     => $descuento,
                'Impuestos'     => [
                    'Traslados' => $traslado,
                ],
            ]);
        }

        $ultimo_nodo_de_impuestos = count($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado'));

        /**impuestos trasladados */
        $impuestos_trasladados       = [];
        $total_impuestos_trasladados = 0;

        /**verificando si tiene nodos de Impuestos Traslados */
        if ($ultimo_nodo_de_impuestos > 0) {
            $impuestos                   = $xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado')[$ultimo_nodo_de_impuestos - 1];
            $total_impuestos_trasladados = (string) $impuestos['Importe'];
            array_push($impuestos_trasladados, [
                'Importe'    => (string) $impuestos['Importe'],
                'Impuesto'   => "002",
                'TasaOCuota' => $tasa_iva,
                'TipoFactor' => "Tasa",
            ]);
        }

        $complemento                     = $xml->xpath('//tfd:TimbreFiscalDigital')[0];
        $ultimos_ocho_digitos_sello_cfdi = $newstring = substr((string) $comprobante['Sello'], -8);

        $cadena_codigo_qr = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=" . (string) $complemento['UUID'] . "&tt=" . (string) $comprobante['Total'] . "&re=" . (string) $emisor['Rfc'] . "&rr=" . (string) $emisor['Rfc'] . "&fe=" . $ultimos_ocho_digitos_sello_cfdi;
        $codigo_qr        = DNS2D::getBarcodeHTML($cadena_codigo_qr, 'QRCODE', 10, 10);

        /**se crea el arreglo con la informacion del xml */
        $comprobante_cfdi = [
            'Comprobante'    => [
                'xmlns:cfdi'         => 'http: //www.sat.gob.mx/cfd/3',
                'xmlns:xsi'          => 'http: //www.w3.org/2001/XMLSchema-instance',
                'Certificado'        => (string) $comprobante['Certificado'],
                'Fecha'              => (string) $comprobante['Fecha'],
                'Folio'              => (string) $comprobante['Folio'],
                'FormaPago'          => (string) $comprobante['FormaPago'],
                'LugarExpedicion'    => (string) $comprobante['LugarExpedicion'],
                'MetodoPago'         => (string) $comprobante['MetodoPago'],
                'Moneda'             => (string) $comprobante['Moneda'],
                'NoCertificado'      => (string) $comprobante['NoCertificado'],
                'Sello'              => (string) $comprobante['Sello'],
                'Serie'              => (string) $comprobante['Serie'],
                'SubTotal'           => (string) $comprobante['SubTotal'],
                'Descuento'          => !empty((string) $comprobante['Descuento']) ? (string) $comprobante['Descuento'] : 0,
                'TipoCambio'         => (string) $comprobante['TipoCambio'],
                'TipoDeComprobante'  => (string) $comprobante['TipoDeComprobante'],
                'Total'              => (string) $comprobante['Total'],
                'Version'            => (string) $comprobante['Version'],
                'xsi:schemaLocation' => 'http: //www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
            ],
            'Emisor'         => [
                'Rfc'           => (string) $emisor['Rfc'],
                'Nombre'        => (string) $emisor['Nombre'],
                'RegimenFiscal' => $regimen != null ? (string) $emisor['RegimenFiscal'] . ' (' . $regimen['regimen'] . ')' : '',
            ],
            'Receptor'       => [
                'Rfc'     => (string) $receptor['Rfc'],
                'Nombre'  => (string) $receptor['Nombre'],
                'UsoCFDI' => $uso_cfdi != null ? (string) $receptor['UsoCFDI'] . ' (' . $uso_cfdi['uso'] . ')' : '',
            ],
            'Conceptos'      => $conceptos,
            'Impuestos'      => [
                'TotalImpuestosTrasladados' => $total_impuestos_trasladados,
                'Traslados'                 => [
                    'Traslado' => $impuestos_trasladados,
                ],
            ],
            'Complemento'    => [
                'TimbreFiscalDigital' => [
                    'xmlns:tfd'          => 'http://www.sat.gob.mx/TimbreFiscalDigital',
                    'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
                    'FechaTimbrado'      => (string) $complemento['FechaTimbrado'],
                    'NoCertificadoSAT'   => (string) $complemento['NoCertificadoSAT'],
                    'RfcProvCertif'      => (string) $complemento['RfcProvCertif'],
                    'SelloCFD'           => (string) $complemento['SelloCFD'],
                    'SelloSAT'           => (string) $complemento['SelloSAT'],
                    'UUID'               => (string) $complemento['UUID'],
                    'Version'            => (string) $complemento['Version'],
                    'xsi:schemaLocation' => 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd',
                ],
            ],
            'CadenaOriginal' => $cfdi->cadena_original,
            'CodigoQr'       => $codigo_qr,
        ];

        return $comprobante_cfdi;
    }

    public function get_cfdis_timbrados(Request $request, $folio_id = 'all', $paginated = false, $metodo_pago_id = 'all', $tipo_comprobante_id = 'all')
    {
        $cliente             = $request->cliente;
        $rfc                 = $request->rfc;
        $numero_control      = isset($request->numero_control) ? $request->numero_control : $folio_id;
        $metodo_pago_id      = isset($request->metodo_pago['value']) ? $request->metodo_pago['value'] : $metodo_pago_id;
        $tipo_comprobante_id = isset($request->tipo_comprobante_id) ? $request->tipo_comprobante_id : $tipo_comprobante_id;
        $fecha_inicio        = $request->fecha_inicio;
        $fecha_fin           = $request->fecha_fin;
        //$tipo_operacion_id   = $request->tipo_operacion_id;
        $status = $request->status;

        $resultado_query = Cfdis::
            select(
            'id',
            'uuid',
            'clientes_id',
            DB::raw(
                '(NULL) as cliente_nombre'
            ),
            'serie',
            'fecha',
            'sat_formas_pago_id',
            'subtotal',
            'descuento',
            'total',
            DB::raw(
                '(NULL) as total_pagado'
            ),
            'sat_tipo_comprobante_id',
            'sat_metodos_pago_id',
            'rfc_emisor',
            'nombre_emisor',
            'sat_pais_id',
            'rfc_receptor',
            'nombre_receptor',
            'residencia_fiscal_receptor',
            'sat_usos_cfdi_id',
            'fecha_timbrado',
            'status',
            DB::raw(
                '(NULL) as tipo_comprobante_texto'
            ),
            DB::raw(
                '(NULL) as sat_uso_cfdi_texto'
            ),
            DB::raw(
                '(NULL) as sat_formas_pago_texto'
            ),
            DB::raw(
                '(NULL) as sat_metodos_pago_texto'
            ),
            DB::raw(
                '(NULL) as sat_pais_texto'
            ),
            DB::raw(
                '(NULL) as fecha_timbrado_texto'
            ),
            DB::raw(
                '(NULL) as situacion_pago_texto'
            )
        )

            ->with('pagos_asociados')
            ->with('cfdis_pagados')
            ->with('cfdis_relacionados')
            ->with('cliente:id,nombre')
            ->whereHas('cliente', function ($query) use ($cliente) {
                $query->where('nombre', 'like', '%' . $cliente . '%');
            })
            ->where(function ($q) use ($fecha_inicio, $fecha_fin) {
                if (trim($fecha_inicio) != '' && trim($fecha_fin) != '') {
                    if ($fecha_fin != $fecha_inicio) {
                        $q->whereDate('fecha_timbrado', '>=', $fecha_inicio);
                        $q->whereDate('fecha_timbrado', '<=', $fecha_fin);
                    } else {
                        $q->whereDate('fecha_timbrado', '=', $fecha_inicio);
                    }
                }
            })
        /**en caso de que sea filtrado por operacion */
        //->with('cfdis_operaciones.operaciones.cliente')
        //->with('cfdis_operaciones.operaciones.venta_terreno')
            ->where(function ($q) use ($numero_control) {
                if (((float) (trim($numero_control))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.id', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($numero_control) {
                if (((float) (trim($numero_control))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.id', '=', $numero_control);
                }
            })
            ->where(function ($q) use ($metodo_pago_id) {
                if (((float) (trim($metodo_pago_id))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.sat_metodos_pago_id', '=', $metodo_pago_id);
                }
            })
            ->where(function ($q) use ($tipo_comprobante_id) {
                if (((float) (trim($tipo_comprobante_id))) > 0) {
                    /**filtro por numero de folio */
                    $q->where('cfdis.sat_tipo_comprobante_id', '=', $tipo_comprobante_id);
                }
            })
            ->where(function ($q) use ($rfc) {
                if (((trim($rfc))) != '') {
                    $q->where('cfdis.rfc_receptor', '=', $rfc);
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('cfdis.status', '=', $status);
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

        $formas_pago  = SatFormasPago::select('*')->get();
        $metodos_pago = MetodosPago::select('*')->get();
        $sat_paises   = SatPais::select('*')->get();
        $sat_usos     = SatUsosCfdi::select('*')->get();

        foreach ($resultado as $index_cfdi => &$cfdi) {
            /**tipo de comprobante */
            if ($cfdi['sat_tipo_comprobante_id'] == 1) {
                $cfdi['tipo_comprobante_texto'] = 'Ingreso (I)';
                if ($cfdi['sat_metodos_pago_id'] == 1) {
                    /**pue */
                    $cfdi['total_pagado'] = $cfdi['total'];
                } else {
                    /**ppd */
                    $pagado = 0;
                    foreach ($cfdi['pagos_asociados'] as $key_pago => $pago) {
                        if ($pago['status'] == 1) {
                            $pagado += $pago['monto_relacion'];
                        }
                    }
                    $cfdi['total_pagado'] = $pagado;

                    if ($cfdi['status'] == 1) {
                        if ($pagado >= $cfdi['total']) {
                            $cfdi['situacion_pago_texto'] = 'Pagado';
                        } else {
                            $cfdi['situacion_pago_texto'] = 'Por liquidar';
                        }
                    } else {
                        $cfdi['situacion_pago_texto'] = 'CFDI no válido';
                    }

                }
            } elseif ($cfdi['sat_tipo_comprobante_id'] == 2) {
                $cfdi['tipo_comprobante_texto'] = 'Egreso (E)';
            } elseif ($cfdi['sat_tipo_comprobante_id'] == 5) {
                $cfdi['tipo_comprobante_texto'] = 'Pago (P)';
            } else {
                return $this->errorResponse('Error al cargar los datos del cfdi.', 409);
            }
            /**sat_formas_pago_id */
            foreach ($formas_pago as $key => $forma) {
                if ($cfdi['sat_formas_pago_id'] == $forma->id) {
                    $cfdi['sat_formas_pago_texto'] = $forma->forma . ' (' . $forma->id . ')';
                    break;
                }
            }
            /**sat_formas_pago_id */
            foreach ($metodos_pago as $key => $metodo) {
                if ($cfdi['sat_metodos_pago_id'] == $metodo->id) {
                    $cfdi['sat_metodos_pago_texto'] = $metodo->clave;
                    break;
                }
            }
            /**sat_formas_pago_id */
            foreach ($sat_paises as $key => $pais) {
                if ($cfdi['sat_pais_id'] == $pais->id) {
                    $cfdi['sat_pais_texto'] = $pais->pais . ' (' . $pais->clave . ')';
                    break;
                }
            }
            /**sat_usos_cfdi_id */
            foreach ($sat_usos as $key => $uso) {
                if ($cfdi['sat_usos_cfdi_id'] == $uso->id) {
                    $cfdi['sat_uso_cfdi_texto'] = $uso->uso . ' (' . $uso->clave . ')';
                    break;
                }
            }

            /**cliente */
            if (isset($cfdi['cliente'])) {
                $cfdi['cliente_nombre'] = $cfdi['cliente']['nombre'];
            }

            $cfdi['fecha_timbrado_texto'] = fecha_abr($cfdi['fecha_timbrado']);
            /**limpiando datos innecesarios */
            //unset($cfdi['sat_tipo_comprobante_id']);
            unset($cfdi['sat_formas_pago_id']);
            //unset($cfdi['sat_metodos_pago_id']);
            unset($cfdi['sat_pais_id']);
            unset($cfdi['sat_usos_cfdi_id']);
            unset($cfdi['fecha_timbrado']);
            unset($cfdi['cliente']);
            unset($cfdi['sat_formas_pago_id']);
        }
        return $resultado_query;
    }

}

class Autenticar
{
    public $usuario;
    public $password;
}

class Parametros
{
    public $accesos;
    public $comprobante;
}
