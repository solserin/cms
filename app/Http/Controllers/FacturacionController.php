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
use App\SatUnidades;
use App\SatUsosCfdi;
use App\TipoComprobantes;
use App\TiposRelacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function GenerarXmlCfdi(Request $request)
    {

        $folio_para_asignar = Cfdis::select('folio')->orderBy('folio', 'desc')->first() + 1;
        /**obteniendo claves de los catalogos */
        $forma_pago = SatFormasPago::where('id', '=', $request->forma_pago['value'])->first();
        if (is_null($forma_pago)) {
            return $this->errorResponse('No se encontró la forma de pago que se está utilizando.', 409);
        }

        $tipo_comprobante = TipoComprobantes::where('id', '=', $request->tipo_comprobante['value'])->first();
        if (is_null($tipo_comprobante)) {
            return $this->errorResponse('No se encontró el tipo de comprobanteque se está utilizando.', 409);
        }
        $metodo_pago = MetodosPago::where('id', '=', $request->metodo_pago['value'])->first();
        if (is_null($metodo_pago)) {
            return $this->errorResponse('No se encontró el método de pago que se está utilizando.', 409);
        }
        $uso_cfdi = SatUsosCfdi::where('id', '=', $request->uso_cfdi['value'])->first();
        if (is_null($uso_cfdi)) {
            return $this->errorResponse('No se encontró el uso de cfdi que se está utilizando.', 409);
        }

        $datos_funeraria = Funeraria::first();

        /**checabndo que tipo de comprobante es */
        $subtotal       = 0;
        $descuento      = 0;
        $iva_trasladado = 0;
        $total          = 0;

        $conceptos['cfdi:Concepto'] = [];

        $claves_productos_servicios_sat = $this->get_claves_productos_sat();
        $get_sat_unidades               = $this->get_sat_unidades();
        $tasa_iva                       = $request->tasa_iva / 100;

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
                return $this->errorResponse('No se encontró la clave de producto del Sat.', 409);
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
                return $this->errorResponse('No se encontró la clave de unidad del Sat.', 409);
            }

            $ValorUnitarioPrecioNeto      = $concepto['precio_neto'] / (1 + ($request->tasa_iva / 100));
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
                                    'TasaOCuota' => "0.160000",
                                    'TipoFactor' => "Tasa",
                                ],
                            ],
                        ],
                    ],
                ],

            );
            /**verificando si se debe de quitar el nodo de Descuento */
            if ($concepto['descuento_b']['value'] != 1) {
                /**no hay descuento y debe quitarse */
                unset($conceptos['cfdi:Concepto'][count($conceptos['cfdi:Concepto']) - 1]['_attributes']['Descuento']);
            }
        }

        if ($request->tipo_comprobante['value'] == '1') {
            /**ingreso */
        } else if ($request->tipo_comprobante['value'] == '2') {
            /**egreso */
        } else if ($request->tipo_comprobante['value'] == '5') {
            /**pago */
        } else {
            /**error */
            return $this->errorResponse('No se encontró tipo de comprobante que se está utilizando.', 409);

        }
        $array = [
            'cfdi:Emisor'    => [
                '_attributes' => [
                    'RegimenFiscal' => '601',
                    'Rfc'           => ENV('APP_ENV') == 'local' ? 'LAN7008173R5' : strtoupper($datos_funeraria['rfc']),
                    //'Nombre'        => ENV('APP_ENV') == 'local' ? 'EMISOR DE PRUEBAS SA DE CV' : strtoupper($datos_funeraria['razon_social']),
                ],
            ],
            'cfdi:Receptor'  => [
                '_attributes' => [
                    'Rfc'     => ENV('APP_ENV') == 'local' ? 'XEXX010101000' : strtoupper($request->rfc),
                    //'Nombre'  => ENV('APP_ENV') == 'local' ? 'RECEPTOR DE PRUEBAS SA DE CV' : strtoupper($request->razon_social),
                    'UsoCFDI' => $uso_cfdi['clave'],
                ],
            ],
            'cfdi:Conceptos' => $conceptos,
            'cfdi:Impuestos' => [
                '_attributes'    => [
                    'TotalImpuestosTrasladados' => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                ],
                'cfdi:Traslados' => [
                    'cfdi:Traslado' => [
                        '_attributes' => [
                            'Importe'    => number_format((float) round($iva_trasladado, 2), 2, '.', ''),
                            'Impuesto'   => "002",
                            'TasaOCuota' => "0.160000",
                            'TipoFactor' => "Tasa",
                        ],
                    ],
                ],
            ],
        ];

        $comprobante = [
            'xmlns:cfdi'         => 'http://www.sat.gob.mx/cfd/3',
            'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
            'Certificado'        => '',
            'Fecha'              => str_replace(" ", "T", date("Y-m-d H:i:s")),
            'Folio'              => '1',
            'FormaPago'          => $forma_pago['clave'],
            'LugarExpedicion'    => $datos_funeraria['cp'],
            'MetodoPago'         => $metodo_pago['clave'],
            'Moneda'             => "MXN",
            'NoCertificado'      => '',
            'Sello'              => '',
            'Serie'              => "EX",
            'SubTotal'           => number_format((float) round($subtotal, 2), 2, '.', ''),
            'Descuento'          => number_format((float) round($descuento, 2), 2, '.', ''),
            'TipoCambio'         => '1',
            'TipoDeComprobante'  => $tipo_comprobante['clave'],
            'Total'              => number_format((float) round($total, 2), 2, '.', ''),
            'Version'            => '3.3',
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
        ];
        if ($descuento == 0) {
            /**no hay descuento y debe quitarse */
            unset($comprobante['Descuento']);
        }

        $result = ArrayToXml::convert($array, [
            'rootElementName' => 'cfdi:Comprobante',
            '_attributes'     => $comprobante,
        ], true, 'UTF-8');

        $nombre_temporal = rand(0, 100000) . '.xml';
        //$nombre_temporal = 'ok.xml';

        Storage::disk('cfdis')->put($nombre_temporal, $result);

        return $nombre_temporal;

        //return $request;
    }

    public function timbrar_cfdi(Request $request)
    {
        header('Content-type: text/html; charset=utf-8');
        try {
            set_time_limit(0);
            date_default_timezone_set("America/Mazatlan");

            $cfdi_a_timbrar = $this->GenerarXmlCfdi($request);
            //return $this->errorResponse($cfdi_a_timbrar, 409);
            /* carga archivo xml */
            $certFile = Storage::disk('local')->path('cer/CSD_Pruebas_CFDI_LAN7008173R5.cer');
            $keyFile  = Storage::disk('local')->path('key/CSD_Pruebas_CFDI_LAN7008173R5.key.pem');

            $contents = Storage::disk('cfdis')->path($cfdi_a_timbrar);

            $clienteFD = new ClienteFormasDigitales($contents);
            /* se le pasan los datos de acceso */
            $autentica           = new Autenticar();
            $autentica->usuario  = "pruebasWS";
            $autentica->password = "pruebasWS";

            $parametros = new Parametros();

            $parametros->accesos = $autentica;

            $this->errorResponse($clienteFD->sellarXML($certFile, $keyFile), 409);

            $parametros->comprobante = $clienteFD->sellarXML($certFile, $keyFile);

            /* se manda el xml a timbrar */
            $responseTimbre = $clienteFD->timbrar($parametros);

            if (isset($responseTimbre->acuseCFDI->error)) {
                //return $this->errorResponse(utf8_decode(utf8_encode(($responseTimbre->acuseCFDI->codigoError))), 409);

                return $this->errorResponse(utf8_decode(utf8_encode(($responseTimbre->acuseCFDI->error))), 409);
            }
            if (isset($responseTimbre->acuseCFDI->xmlTimbrado)) {
                $file_guardar = $cfdi_a_timbrar;
                Storage::disk('cfdis')->put($file_guardar, $responseTimbre->acuseCFDI->xmlTimbrado);
                return $this->leer_xml($file_guardar);
            }
        } catch (SoapFault $e) {
            print("Auth Error:::: $e");
        }

    }

    /**leo los datos del xml para guardar en la base de datos */
    public function leer_xml($path_xml = '')
    {
//EMPIEZO A LEER LA INFORMACION DEL CFDI E IMPRIMIRLA
        if (trim($path_xml) == '') {
            $path_xml = Storage::disk('cfdis')->path('file.xml');
        } else {
            $path_xml = Storage::disk('cfdis')->path($path_xml);
        }

        $xml = simplexml_load_file($path_xml);
        $ns  = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('c', $ns['cfdi']);
        $xml->registerXPathNamespace('t', $ns['tfd']);

        foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) {
            // echo $cfdiComprobante['MetodoPago'];
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor) {
            //echo $Emisor['Rfc'];
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal') as $DomicilioFiscal) {
            //echo $DomicilioFiscal['Pais'];
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:ExpedidoEn') as $ExpedidoEn) {
            //echo $ExpedidoEn['Pais'];
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto) {
            /*echo $Concepto['ClaveUnidad'];
        echo $Concepto['Importe'];
        echo $Concepto['Cantidad'];
        echo $Concepto['Descripcion'];
        echo $Concepto['ValorUnitario'];
         */
        }

        foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Traslado) {
            /*echo $Traslado['Tasa'];
        echo $Traslado['Importe'];
        echo $Traslado['Impuesto'];
         */
        }

        $uuid = '';
        //ESTA ULTIMA PARTE ES LA QUE GENERABA EL ERROR
        foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
            //echo $tfd['SelloCFD'];
            //echo $tfd['FechaTimbrado'];
            $uuid = $tfd['UUID'];
            //echo $tfd['NoCertificadoSAT'];
            //echo $tfd['Version'];
            //echo $tfd['SelloSAT'];
        }

        //retorno el uuid del cfdi

        return $uuid;

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