<?php

namespace App\Http\Controllers;

use App\AjusteInventarioDetalle;
use App\Articulos;
use App\Departamentos;
use App\Inventario;
use App\MovimientosInventario;
use App\SATProductosServicios;
use App\SatUnidades;
use App\TipoArticulos;
use App\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class InventarioController extends ApiController
{
    public function get_tipo_articulo()
    {
        return TipoArticulos::get();
    }

    public function get_categorias()
    {
        return $datos = Departamentos::with('categorias')->get();
    }

    public function get_unidades()
    {
        $unidades = SatUnidades::get();
        foreach ($unidades as $key => &$unidad) {
            $unidad['unidad']
            = $unidad['unidad'] . '(' . $unidad['clave'] . ')';
        }
        return $unidades;
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        return SATProductosServicios::whereNotIn('clave', ['84111506', '42262102'])->get();
    }

    public function ajustar_inventario(Request $request)
    {
        if (trim($request->tipoAjuste['value']) == '') {
            return $this->errorResponse('Error, debe especificar que tipo de ajuste que está solicitando.', 409);
        }
        //validaciones
        $validaciones = [
            'tipoAjuste.value'            => 'required',
            'ajuste.*.id'                 => [
                'required',
            ],
            'ajuste.*.caduca_b'           => [
                'required',
            ],
            'ajuste.*.fecha_caducidad'    => [
                '',
            ],
            'ajuste.*.lote'               => [
                '',
            ],
            'ajuste.*.existencia_fisica'  => [
                'required',
            ],
            'ajuste.*.existencia_sistema' => [
                'required',
            ],
        ];

        //if ($request->tipoAjuste['value'] == 1) {
        /**es un ajuste de no inventariados */
        foreach ($request->ajuste as $key => $articulo) {
            if ($articulo['caduca_b'] == 1) {
                $validaciones['ajuste.' . $key . '.fecha_caducidad'] = 'required|date_format:Y-m-d';
            }
        }
        //}

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'required'                           => 'Ingrese la clave del ajuste',
            'ajuste.id.required'                 => 'ingrese el id del artículo',
            'ajuste.existencia_fisica.required'  => 'ingrese la existencia física',
            'ajuste.existencia_sistema.required' => 'ingrese la existencia en el sistema',
            'ajuste.caduca_b.required'           => 'indique si al artículo caduca',
            'ajuste.fecha_caducidad.required'    => 'indique la fecha de caducidad',
            'ajuste.fecha_caducidad.date_format' => 'indique la fecha de caducidad(Y-m-d)',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );
        try {
            DB::beginTransaction();
            $id_movimiento = 0;

            if ($request->tipoAjuste['value'] == 1) {
                /**creamos el lote nuevo, el lote este es solo para mostrar al usuario, el lote real es lotes_id */
                $num_lote_inventario = (int) Inventario::max('num_lote_inventario');
                $num_lote_inventario++;

                /**se crea un lote y despues se agregan al inventario */
                $id_movimiento = DB::table('movimientos_inventario')->insertGetId(
                    [
                        'nota'                => $request->nota,
                        'fecha_registro'      => now(),
                        'fecha_movimiento'      => now(),
                        'registro_id'         => (int) $request->user()->id,
                        'tipo_movimientos_id' => 2,
                        //'subtotal' => 0,
                        //'descuento' => 0,
                        //'impuestos' => 0,
                        //'total' => 0
                        /**entrada de lostes por ajuste */
                    ]
                );
                /**crea el detalle del ajuste */
                foreach ($request->ajuste as $key => $articulo) {
                    if ($articulo['existencia_fisica'] < 1) {
                        return $this->errorResponse('Ingrese todos los valores a inventariar.', 409);
                    }
                    DB::table('ajuste_detalle')->insert(
                        [
                            'fecha_caducidad'           => $articulo['fecha_caducidad'] != 'N/A' ? $articulo['fecha_caducidad'] : null,
                            'existencia_sistema'        => $articulo['existencia_sistema'],
                            'existencia_fisica'         => $articulo['existencia_fisica'],
                            'movimientos_inventario_id' => $id_movimiento,
                            'lotes_id'                  => $id_movimiento,
                            'articulos_id'              => $articulo['id'],
                            'nota'                      => $articulo['nota'],
                            /**entrada de lostes por ajuste */
                        ]
                    );
                }
                /**actualizando el inventario */
                foreach ($request->ajuste as $key => $articulo) {
                    DB::table('inventario')->insert(
                        [
                            'lotes_id'            => $id_movimiento,
                            'precio_compra_neto'  => $articulo['precio_compra'],
                            'fecha_caducidad'     => $articulo['fecha_caducidad'] != 'N/A' ? $articulo['fecha_caducidad'] : null,
                            'existencia'          => $articulo['existencia_fisica'],
                            'articulos_id'        => $articulo['id'],
                            'num_lote_inventario' => $num_lote_inventario,
                        ]
                    );
                }
            } else {
                /**es un ajuste de inventario del invnetario actual */
                $id_movimiento = DB::table('movimientos_inventario')->insertGetId(
                    [
                        'nota'                => $request->nota,
                        'fecha_registro'      => now(),
                        'fecha_movimiento'      => now(),
                        'registro_id'         => (int) $request->user()->id,
                        'tipo_movimientos_id' => 1,
                        //'subtotal' => 0,
                        //'descuento' => 0,
                        //'impuestos' => 0,
                        //'total' => 0
                        /**entrada de lostes por ajuste */
                    ]
                );
                /**crea el detalle del ajuste */
                foreach ($request->ajuste as $key => $articulo) {
                    DB::table('ajuste_detalle')->insert(
                        [
                            'fecha_caducidad'           => $articulo['fecha_caducidad'] != 'N/A' ? $articulo['fecha_caducidad'] : null,
                            'existencia_sistema'        => $articulo['existencia_sistema'],
                            'existencia_fisica'         => $articulo['existencia_fisica'],
                            'movimientos_inventario_id' => $id_movimiento,
                            'lotes_id'                  => $articulo['lote'],
                            'articulos_id'              => $articulo['id'],
                            'nota'                      => $articulo['nota'],
                            /**entrada de lostes por ajuste */
                        ]
                    );
                }
                /**actualizando el inventario */
                foreach ($request->ajuste as $key => $articulo) {
                    DB::table('inventario')->where('lotes_id', $articulo['lote'])->where('articulos_id', $articulo['id'])->update(
                        [
                            'existencia' => $articulo['existencia_fisica'],
                        ]
                    );
                }
            }
            //return $this->errorResponse('E.', 409);
            DB::commit();
            return $id_movimiento;
        } catch (\Throwable $th) {
            DB::rollBack();
            //return $this->errorResponse('Error al guardar ajuste, reinicie la página e intente nuevamente.', 409);
            return $th;
        }
    }

    public function control_articulos(Request $request, $tipo_servicio = '')
    {
        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        /**procede la peticion */

        //validaciones
        $validaciones = [
            'id_articulo_modificar'  => '',
            'descripcion'            => 'required',
            'tipo_articulo.value'    => 'numeric|required',
            'departamento.value'     => 'numeric|required',
            'categoria.value'        => 'numeric|required',
            'unidad_sat.value'       => 'numeric|required',
            'unidad.value'           => 'numeric|required',
            'opcion_iva.value'       => 'numeric|required',
            'opcion_caducidad.value' => 'numeric|required',
            'minimo_inventario'      => 'integer|required|min:1',
            'maximo_inventario'      => 'integer|required|gte:minimo_inventario',
            'opcion_caducidad.value' => 'numeric|required',
            'costo_compra'            => 'numeric|required|min:1',
            'costo_venta'            => 'numeric|required|min:1',
            'codigo_barras'          => '',
        ];

        /**verificando si es tipo modificar para validar que venga el id a modificar */
        $datos_venta = array();
        if ($tipo_servicio == 'modificar') {
            $validaciones['id_articulo_modificar'] = 'required';
        }
        $opcion_caducidad = $request->opcion_caducidad['value'];
        if ($request->tipo_articulo['value'] != 2) {
            if (trim($request->codigo_barras) == '') {
                return $this->errorResponse('Ingrese un código de barras.', 409);
            } else {
                /**revisando que el codigo de barras tenga al menos 6 caracteres para que no vaya chocar con los ids de los articulos
                 * que lo usaran como clave alterna
                 */
                if (strlen($request->codigo_barras) < 7) {
                    return $this->errorResponse('El código de barras debe ser al menos 7 caracteres.', 409);
                }
            }
            /**codigo de barras requerido */
            /**revisando si existe el codigo de berras */
            $articulo = Articulos::where('codigo_barras', $request->codigo_barras)->first();
            if (!empty($articulo)) {
                if ($articulo->status == 1) {
                    if ($tipo_servicio == 'modificar') {
                        if ($articulo->id != $request->id_articulo_modificar) {
                            return $this->errorResponse('El código de barras ingresado ya ha sido registrado.', 409);
                        }
                    } else {
                        return $this->errorResponse('El código de barras ingresado ya ha sido registrado.', 409);
                    }
                }
            }
        } else {
            /**es de tipo servicio */
            $request->minimo_inventario = 1;
            $request->maximo_inventario = 1;
            $request->codigo_barras     = null;
            $opcion_caducidad           = 0;
        }

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'id_articulo_modificar.required' => 'Ingrese un la clave única del artículo',
            'required'                       => 'Ingrese este dato',
            'numeric'                        => 'Este dato debe ser un número',
            'integer'                        => 'Este dato debe ser un número entero',
            'min'                            => 'la cantidad debe ser mínimo 1 (Uno)',
            'gte'                            => 'la cantidad debe ser igual o mayor al minimo de inventario',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /*  $unidad_compra = '';
        $unidad_venta = '';

        if ($request->tipo_articulo['value'] == 1) {
        //unidad de pieza
        $unidad_compra = 1;
        $unidad_venta = 1;
        } else {
        //unidad de servicio
        $unidad_compra = 2;
        $unidad_venta = 2;
        }
         */
        $id_articulo = 0;
        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                $id_articulo = DB::table('articulos')->insertGetId(
                    [
                        'imagen'                     => trim($request->imagen) === '' ? null : trim($request->imagen),
                        'tipo_articulos_id'          => $request->tipo_articulo['value'],
                        'sat_productos_servicios_id' => $request->unidad_sat['value'],
                        'factor'                     => 1,
                        'codigo_barras'              => $request->codigo_barras,
                        'descripcion'                => $request->descripcion,
                        'descripcion_ingles'         => $request->descripcion,
                        'precio_compra'              => $request->costo_compra,
                        'precio_venta'               => $request->costo_venta,
                        'minimo'                     => $request->minimo_inventario,
                        'maximo'                     => $request->maximo_inventario,
                        'caduca_b'                   => $opcion_caducidad,
                        'grava_iva_b'                => $request->opcion_iva['value'],
                        'categorias_id'              => $request->categoria['value'],
                        'sat_unidades_compra'        => $request->unidad['value'],
                        'sat_unidades_venta'         => $request->unidad['value'],
                        'nota'                       => $request->nota,
                    ]
                );
            }
            /**fin if servicio tipo agregar */
            else {

                $r = new \Illuminate\Http\Request();
                $r->replace(['sample' => 'sample']);
                $inventario = $this->get_articulos($r, $request->id_articulo_modificar, '', 0, 0, 0, 0);
                if (count($inventario) > 0) {
                    if (count($inventario[0]['inventario']) > 0) {
                        /**checa si cambio algun dato sensible
                         * tipo_articulo
                         * caduca
                         */
                        if ($opcion_caducidad != $inventario[0]['caduca_b']) {
                            return $this->errorResponse('No se puede cambiar la opción caducidad, pues ya existe inventario de este artículo.', 409);
                        }

                        if ($request->tipo_articulo['value'] != $inventario[0]['tipo_articulos_id']) {
                            return $this->errorResponse('No se puede cambiar el tipo de artículo, pues ya existe inventario de este artículo.', 409);
                        }
                    }
                } else {
                    return $this->errorResponse('Este artículo no fue encontrado en la Base de Datos.', 409);
                }

                /**validando que nno se cambie el nombre ni tipo, categoria, departamento para cuidar la integridad de los contratos */
                $ajuste = AjusteInventarioDetalle::where('articulos_id', $request->id_articulo_modificar)->get();
                $venta  = VentaDetalle::where('articulos_id', $request->id_articulo_modificar)->get();

                $articulo = Articulos::where('id', $request->id_articulo_modificar)->first();
                /*if (count($ajuste) > 0 || count($venta) > 0) {
                if ($articulo['tipo_articulos_id'] != $request->tipo_articulo['value']) {
                return $this->errorResponse('No se puede cambiar el tipo de artículos ya que se tienen movimientos registrados con este artículo.', 409);
                }

                if ($articulo['sat_productos_servicios_id'] != $request->unidad_sat['value']) {
                return $this->errorResponse('No se puede cambiar la clave del sat ya que se tienen movimientos registrados con este artículo.', 409);
                }

                if ($articulo['categorias_id'] != $request->categoria['value']) {
                return $this->errorResponse('No se puede cambiar la categoría ya que se tienen movimientos registrados con este artículo.', 409);
                }
                }*/

                /**verificar que no cambie el tipo de caducidad si ya fue vendido algo de ese producto */
                /**es modificar */
                DB::table('articulos')->where('id', '=', $request->id_articulo_modificar)->update(
                    [
                        'imagen'                     => trim($request->imagen) === '' ? null : trim($request->imagen),
                        'tipo_articulos_id'          => $request->tipo_articulo['value'],
                        'sat_productos_servicios_id' => $request->unidad_sat['value'],
                        'factor'                     => 1,
                        'codigo_barras'              => $request->codigo_barras,
                        'descripcion'                => $request->descripcion,
                        'descripcion_ingles'         => $request->descripcion,
                        'precio_compra'              => $request->costo_compra,
                        'precio_venta'               => $request->costo_venta,
                        'minimo'                     => $request->minimo_inventario,
                        'maximo'                     => $request->maximo_inventario,
                        'caduca_b'                   => $opcion_caducidad,
                        'grava_iva_b'                => $request->opcion_iva['value'],
                        'categorias_id'              => $request->categoria['value'],
                        'sat_unidades_compra'        => $request->unidad['value'],
                        'sat_unidades_venta'         => $request->unidad['value'],
                        'nota'                       => $request->nota,
                    ]
                );
            }
            DB::commit();
            return
            $tipo_servicio == 'agregar' ? $id_articulo : $request->id_articulo_modificar;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_ajustes(Request $request, $id_ajuste = 'all', $paginated = '')
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $articulo                 = $request->articulo;
        $numero_control           = $request->numero_control;
        $resultado_query          = MovimientosInventario::select(
            'id',
            'registro_id',
            'fecha_registro',
            'tipo_movimientos_id',
            'nota',
            DB::raw(
                '(NULL) AS fecha_registro_texto'
            ),
            DB::raw(
                '(NULL) AS tipo_ajuste_texto'
            )
        )
            ->with('registro:id,nombre')
            ->with('detalles.articulos:id,descripcion,codigo_barras')
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('movimientos_inventario.id', '=', $numero_control);
                    }
                }
            })
            ->where(function ($q) use ($id_ajuste) {
                if (trim($id_ajuste) == 'all' || $id_ajuste > 0) {
                    if (trim($id_ajuste) == 'all') {
                        $q->where('movimientos_inventario.id', '>', $id_ajuste);
                    } else if ($id_ajuste > 0) {
                        $q->where('movimientos_inventario.id', '=', $id_ajuste);
                    }
                }
            })
            ->whereIn('tipo_movimientos_id', [1, 2])
            ->orderBy('movimientos_inventario.id', 'desc')
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
        foreach ($resultado as $key_ajuste => &$ajuste) {
            $ajuste['fecha_registro_texto'] = fecha_abr($ajuste['fecha_registro']);
            if ($ajuste['tipo_movimientos_id'] == 1) {
                $ajuste['tipo_ajuste_texto'] = 'Ajustes de inventario';
            } else {
                $ajuste['tipo_ajuste_texto'] = 'Entrada de lote por ajustes';
            }
            /**determinando el estado del lote afectado
             * resultado_ajuste
             * resultado_ajuste_texto
             */
            /**0- resto, 1- igual, 3- sumo */
            /**aqui voy */
            $lotes_ids = [];
            foreach ($ajuste['detalles'] as $key_detalle => $detalle) {
                /**obtengo los indexes de los lotes afectados para obtener el num de lote de usuario */
                if (!in_array($detalle['lotes_id'], $lotes_ids)) {
                    array_push($lotes_ids, $detalle['lotes_id']);
                }
            }

            $lotes_nums = Inventario::select('lotes_id', 'num_lote_inventario')->whereIn('lotes_id', $lotes_ids)->get();

            foreach ($ajuste['detalles'] as $key_detalle => &$detalle) {

                /**agrego el num_lote_inventario */
                foreach ($lotes_nums as $key => $value) {
                    if ($value['lotes_id'] == $detalle['lotes_id']) {
                        $detalle['num_lote_inventario'] = $value['num_lote_inventario'];
                        break;
                    }
                }

                if ($detalle['existencia_sistema'] == $detalle['existencia_fisica']) {
                    $detalle['resultado_ajuste']       = 1;
                    $detalle['resultado_ajuste_texto'] = 'Sin Cambios';
                } elseif ($detalle['existencia_sistema'] > $detalle['existencia_fisica']) {
                    if ($ajuste['tipo_movimientos_id'] == 1) {
                        $detalle['resultado_ajuste']       = 0;
                        $detalle['resultado_ajuste_texto'] = 'Salida de Mercancías';
                    } else {
                        $detalle['resultado_ajuste']       = 1;
                        $detalle['resultado_ajuste_texto'] = 'Ingreso de Mercancía';
                    }
                } else {
                    $detalle['resultado_ajuste']       = 2;
                    $detalle['resultado_ajuste_texto'] = 'Ingreso de Mercancía';
                }
                /**diferencia real del cambio */
                $detalle['diferencia'] = abs($detalle['existencia_sistema'] - $detalle['existencia_fisica']);
            }
        }

        return $resultado_query;
    }

    public function get_articulos(Request $request, $id_articulo = 'all', $paginated = '', $id_departamento = 0, $id_categoria = 0, $tipo_articulo = 0, $solo_inventariable = 0)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $articulo                 = $request->articulo;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;
        $id_articulo_request      = $request->id_articulo;
        $codigo_barras_request    = $request->codigo_barras;
        $resultado_query          = Articulos::select(
            '*',
            DB::raw(
                '(NULL) AS grava_iva_texto'
            ),
            DB::raw(
                '(NULL) AS caduca_texto'
            ),
            DB::raw(
                '(NULL) AS existencia'
            ),
            DB::raw(
                '(NULL) AS estatus_texto'
            ),
            DB::raw(
                '(NULL) AS estatus_inventario_b'
            ),
            DB::raw(
                '(NULL) AS estatus_inventario_texto'
            )
        )
            ->with('categoria')
            ->whereHas('categoria', function ($query) use ($id_categoria) {
                if (trim($id_categoria) != '' && $id_categoria > 0) {
                    $query->where('id', $id_categoria);
                }
            })
            ->with('categoria.departamento')
            ->whereHas('categoria.departamento', function ($query) use ($id_departamento) {
                if (trim($id_departamento) != '' && $id_departamento > 0) {
                    $query->where('id', $id_departamento);
                }
            })
            ->where(function ($q) use ($id_articulo) {
                if (trim($id_articulo) == 'all' || $id_articulo > 0) {
                    if (trim($id_articulo) == 'all') {
                        $q->where('articulos.id', '>', $id_articulo);
                    } else if ($id_articulo > 0) {
                        $q->where('articulos.id', '=', $id_articulo);
                    }
                }
            })
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('articulos.id', '=', $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        if (trim($numero_control) != '') {
                            /**filtro por numero de solicitud */
                            $q->where('articulos.codigo_barras', '=', $numero_control);
                        }
                    }
                }
            })

            ->where(function ($q) use ($id_articulo_request) {
                if (trim($id_articulo_request) == 'all' || $id_articulo_request > 0) {
                    if (trim($id_articulo_request) == 'all') {
                        $q->where('articulos.id', '>', $id_articulo_request);
                    } else if ($id_articulo_request > 0) {
                        $q->where('articulos.id', '=', $id_articulo_request);
                    }
                }
            })
            ->where(function ($q) use ($codigo_barras_request) {
                if (trim($codigo_barras_request) == 'all' || $codigo_barras_request > 0) {
                    if (trim($codigo_barras_request) == 'all') {
                        $q->where('articulos.codigo_barras', '>', $codigo_barras_request);
                    } else if ($codigo_barras_request > 0) {
                        $q->where('articulos.codigo_barras', '=', $codigo_barras_request);
                    }
                }
            })
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('articulos.status', '=', $status);
                }
            })
            ->where('descripcion', 'like', '%' . $articulo . '%')
            ->with('tipo_articulo')
            ->with('inventario')

            ->whereHas('tipo_articulo', function ($query) use ($tipo_articulo) {
                if ((trim($tipo_articulo) != '' && $tipo_articulo > 0)) {
                    $query->where('id', $tipo_articulo);
                }
            })
            ->whereHas('tipo_articulo', function ($query) use ($solo_inventariable) {
                if ((trim($solo_inventariable) != '' && $solo_inventariable > 0)) {
                    $query->where('id', '<>', 2);
                }
            })
            ->with('unidad_compra:id,clave,unidad')
            ->with('unidad_venta:id,clave,unidad')
            ->orderBy('articulos.id', 'desc')
            ->get()
            ->map(function ($articulos) {
                $articulos->inventario = $articulos->inventario->take(35);
                return $articulos;
            });

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

    /**ENABLE DISABLE PRECIO DE PROPIEDAD*/
    public function enable_disable(Request $request, $tipo_servicio = '')
    {

        if (!(trim($tipo_servicio) == 'enable' || trim($tipo_servicio) == 'disable')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        /**procede la peticion */

        //validaciones directas sin condicionales
        $validaciones = [
            'articulo_id' => 'required',
        ];

        $mensajes = [
            'required' => 'Dese ingresar la clave del artículo',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        try {
            DB::beginTransaction();
            if (trim($tipo_servicio) == 'enable') {
                $res = DB::table('articulos')->where('id', $request->articulo_id)->update(
                    [
                        'status' => 1,
                    ]
                );
            } else {
                /**verificando si existe inventario */
                $r = new \Illuminate\Http\Request();
                $r->replace(['sample' => 'sample']);
                $inventario = $this->get_articulos($r, $request->articulo_id, '', 0, 0, 0, 0);
                if (count($inventario) > 0) {
                    if ($inventario[0]['existencia'] <= 0 || $inventario[0]['existencia'] == 'N/A') {
                        $res = DB::table('articulos')->where('id', $request->articulo_id)->update(
                            [
                                'status' => 0,
                            ]
                        );
                    } else {
                        return $this->errorResponse('Este artículo cuenta con existencias, no se puede deshabilitar.', 409);
                    }
                } else {
                    return $this->errorResponse('Este artículo cuenta con existencias, no se puede deshabilitar.', 409);
                }
            }

            /**todo salio bien y se debe de modificar */
            DB::commit();
            return $request->articulo_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function get_inventario_pdf(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            if ($email == true) {
                if (!$request->email_addres || !$request->destinatario) {
                    $this->errorResponse('Es necesario un correo y un destinatario', 409);
                }
            }
            $email_to      = $request->email_address;
            $datos_request = json_decode($request->request_parent[0], true);

            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $inventario = $this->get_articulos($r, 'all', '', 0, 0, 0, 0);
            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$email = false;
            $email_to = 'hector@gmail.com';
             */
            //obtengo la informacion de esa venta
            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('inventarios/inventario_completo/inventario', ['empresa' => $empresa, 'inventario' => $inventario]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = 'Reporte de Inventario Registrado.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('inventarios.inventario_completo.footer'),
            ]);

            $pdf->setOptions([
                'header-html' => view('inventarios.inventario_completo.header'),
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    $request->destinatario,
                    'Reporte de Inventario Registrado',
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

    public function get_ajuste_pdf(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            if ($email == true) {
                if (!$request->email_addres || !$request->destinatario) {
                    $this->errorResponse('Es necesario un correo y un destinatario', 409);
                }
            }
            $email_to      = $request->email_address;
            $datos_request = json_decode($request->request_parent[0], true);
            $id_ajuste     = $datos_request['id_ajuste'];

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$email = false;
            $id_ajuste = 10;
            $email_to = 'hector@gmail.com';
             */
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $ajuste = $this->get_ajustes($r, $id_ajuste, '');
            //obtengo la informacion de esa venta
            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('inventarios/ajustes/ajustes', ['empresa' => $empresa, 'ajustes' => $ajuste]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = 'Reporte de Ajuste de Inventario.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('inventarios.ajustes.footer'),
            ]);
            $pdf->setOptions([
                'header-html' => view('inventarios.ajustes.header'),
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    $request->destinatario,
                    'Reporte de Ajuste de Inventario',
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

    public function get_pdf_etiquetas(Request $request)
    {
        /**estos valores verifican si el usuario quiere mandar el pdf por correo */
        $email = $request->email_send === 'true' ? true : false;
        if ($email == true) {
            if (!$request->email_addres || !$request->destinatario) {
                $this->errorResponse('Es necesario un correo y un destinatario', 409);
            }
        }
        $email_to      = $request->email_address;
        $datos_request = json_decode($request->request_parent[0], true);
        $datos         = $datos_request['etiquetas'];

        $lotes    = [];
        $ids      = [];
        $cantidad = 0;
        foreach ($datos as $dato) {
            if (!in_array($dato['lotes_id'], $lotes)) {
                array_push($lotes, $dato['lotes_id']);
            }
            if (!in_array($dato['id_articulo'], $ids)) {
                array_push($ids, $dato['id_articulo']);
            }

            $cantidad += $dato['cantidad'];
        }

        if ($cantidad > 1500) {
            return $this->errorResponse('La impresora puede imprimir un máximo de 1500 por rollo.', 409);
        }

        $articulos = Articulos::
            with('inventario')
            ->with(['inventario' => function ($q) use ($lotes) {
                $q->whereIn('lotes_id', $lotes);
            }])
            ->whereIn('id', $ids)
            ->get();

        $etiquetas = [];
        foreach ($articulos as $key => $articulo) {
            foreach ($datos as $dato) {
                foreach ($articulo['inventario'] as $inventario) {
                    if ($dato['id_articulo'] == $articulo['id'] && $inventario['lotes_id'] == $dato['lotes_id']) {
/**lo repito el numero de veces que se ocupa la etiqueta */
                        for ($i = 0; $i < $dato['cantidad']; $i++) {
                            array_push($etiquetas, [
                                'id'                  => $articulo['id'],
                                'descripcion'         => $articulo['descripcion'],
                                'lotes_id'            => $inventario['lotes_id'],
                                'num_lote_inventario' => $inventario['num_lote_inventario'],
                            ]);
                        }
                    }
                }
            }
        }

        /**obteniendo los datos para crear las etiquetas */

        /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
         * por lo cual puede variar de paramtros degun la ncecesidad
         */
        /*$email     = false;
        $id_ajuste = 1;
        $email_to  = 'hector@gmail.com';
         */

        //obtengo la informacion de esa venta
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView('inventarios/etiquetado/ajustes', ['empresa' => $empresa, 'etiquetas' => $etiquetas]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = 'Reporte de Ajuste de Inventario.pdf';

        $pdf->setOption('margin-left', 5.4);
        $pdf->setOption('margin-right', 5.4);
        $pdf->setOption('margin-top', 5.4);
        $pdf->setOption('margin-bottom', 5.4);

        $pdf->setOption('page-height', 76.2);
        $pdf->setOption('page-width', 101.6);
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
                'Reporte de Ajuste de Inventario',
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }

    }

    public function get_inventario_conteo_pdf(Request $request)
    {
        try {
            /**estos valores verifican si el usuario quiere mandar el pdf por correo */
            $email = $request->email_send === 'true' ? true : false;
            if ($email == true) {
                if (!$request->email_addres || !$request->destinatario) {
                    $this->errorResponse('Es necesario un correo y un destinatario', 409);
                }
            }
            $email_to      = $request->email_address;
            $datos_request = json_decode($request->request_parent[0], true);

            /**aqui obtengo los datos que se ocupan para generar el reporte, es enviado desde cada modulo al reporteador
             * por lo cual puede variar de paramtros degun la ncecesidad
             */
            /*$email = false;
            $email_to = 'hector@gmail.com';
             */
            $r = new \Illuminate\Http\Request();
            $r->replace(['sample' => 'sample']);
            $articulos = $this->get_articulos($r, 'all', '', 0, 0, 0, 1);
            //obtengo la informacion de esa venta
            $get_funeraria = new EmpresaController();
            $empresa       = $get_funeraria->get_empresa_data();
            $pdf           = PDF::loadView('inventarios/ajuste_cantidades/ajustes', ['empresa' => $empresa, 'articulos' => $articulos]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = 'Reporte de Inventario Por Lotes.pdf';
            $pdf->setOptions([
                'title'       => $name_pdf,
                'footer-html' => view('inventarios.ajuste_cantidades.footer'),
            ]);
            $pdf->setOptions([
                'header-html' => view('inventarios.ajuste_cantidades.header'),
            ]);
            //$pdf->setOption('orientation', 'landscape');
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
                $enviar_email     = $email_controller->pdf_email(
                    $email_to,
                    $request->destinatario,
                    'Reporte de Inventario Por Lotes',
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






    public function control_compras(Request $request, $tipo_servicio = '')
    {
        if (!(trim($tipo_servicio) == 'agregar' || trim($tipo_servicio) == 'modificar')) {
            return $this->errorResponse('Error, debe especificar que tipo de control está solicitando.', 409);
        }
        /**procede la peticion */

        //validaciones
        $validaciones = [
            'id_proveedor'             => 'required',
            'fecha_compra'          => 'required',
            'referencia'             => 'required',
            'tasa_iva'=> 'required|numeric|min:0|max:25',
            'pago_efectivo'=> 'required|numeric|min:0',
            'pago_cheque'=> 'required|numeric|min:0',
            'pago_tarjeta'=> 'required|numeric|min:0',
            'pago_transferencia'=> 'required|numeric|min:0',
            'articulos.*.id'                   => 'integer|min:1',
            'articulos.*.cantidad'             => 'integer|min:1',
            'articulos.*.costo_neto_normal'    => 'numeric|min:0',
            'articulos.*.costo_neto_descuento' => 'numeric|min:0',
            'articulos.*.descuento_b'          => 'boolean',
            'articulos.*.facturable_b'         => 'boolean',
            'costo_incurrido_costo'             => '',
            'costo_incurrido_detalle'             => '',
        ];


        if(trim($request->costo_incurrido_detalle)!=''){
             $validaciones['costo_incurrido_costo']='required|numeric|min:0';
        }

        if(trim($request->costo_incurrido_costo)!=''){
             $validaciones['costo_incurrido_detalle']='required';
        }

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'id_proveedor.requeired'             => 'Seleccione al proveedor.',
            'fecha_compra.required'          => 'Seleccione la fecha de la compra.',
            'referencia.required'=>'Ingrese una referencia, Núm. Factura o Nota de Venta.',
            'tasa_iva.required'=>'Ingrese el IVA.',
            'pago_efectivo.required'=> 'Ingrese la cantidad a pagar con efectivo.',
            'pago_efectivo.min'=> 'El mínimo debe ser 0.00.',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        /**validaciones */

        /**calculando totales segun los articulos y sus descuentos */

        $total_compra=0;
        foreach ($request->articulos as $key => $articulo) {
            /**validando que el tipo de articulo no sea servicio */
           if($articulo['tipo_articulos_id']==2){
              return $this->errorResponse('Los conceptos de la compra deben ser de tipo artículos.',409);
           }

           if($articulo['descuento_b']==true){
               $total_compra+=$articulo['cantidad']*$articulo['costo_neto_descuento'];
           }else{
                $total_compra+=$articulo['cantidad']*$articulo['costo_neto_normal'];
           }
        }

        $total_compra+=$request->costo_incurrido_costo;


        if(count($request->articulos)==0){
              return $this->errorResponse('Ingrese los artículos de la compra.',409);
        }
        $total_pagado=$request->pago_efectivo+$request->pago_cheque+$request->pago_tarjeta+$request->pago_transferencia;


        if($total_compra!=$total_pagado){
              return $this->errorResponse('La cantidad pagada no cubre el total de la compra.',409);
        }

        


        try {
            DB::beginTransaction();
            $id_compra=0;
            
            $num_compra = (int) MovimientosInventario::max('num_compra');
            $num_compra++;
            $id_compra = DB::table('movimientos_inventario')->insertGetId(
                [
                    'folio_referencia'=>$request->referencia,
                    'fecha_registro'          => now(),
                    'fecha_movimiento'=>$request->fecha_compra,
                    'registro_id'    => (int) $request->user()->id,
                    'nota'=>$request->nota,
                    'tipo_movimientos_id'=>3,//CM
                    'proveedores_id'=>$request->id_proveedor,
                    'pago_efectivo'=>$request->pago_efectivo,
                    'pago_cheque'=>$request->pago_cheque,
                    'pago_tarjeta'=>$request->pago_tarjeta,
                    'pago_transferencia'=>$request->pago_transferencia,
                    'iva_porcentaje'=>$request->tasa_iva,
                    'num_compra'=> $num_compra
                ]
            );

            DB::table('costos_incurridos')->insertGetId(
                [
                    'costo_detalle'=>$request->costo_incurrido_detalle,
                    'costo_neto'=>$request->costo_incurrido_costo,
                    'movimientos_inventario_id'=>$id_compra,
                    'facturable_b'=>$request->facturable_b_gasto_incurrido
                ]
            );

            /**guardand el detalle de la compra */
            $num_lote_inventario = (int) Inventario::max('num_lote_inventario');
            $num_lote_inventario++;

             foreach ($request->articulos as $key => $articulo) {
               DB::table('compra_detalle')->insertGetId(
                    [
                        'articulos_id'=>$articulo['id'],
                        'movimientos_inventario_id'          => $id_compra,
                        'cantidad'=>$articulo['cantidad'],
                        'costo_neto'=>$articulo['costo_neto_normal'],
                        'costo_neto_descuento'=>$articulo['costo_neto_descuento'],
                        'facturable_b'=>$articulo['facturable_b'],
                        'descuento_b'=>$articulo['descuento_b'],
                    ]
                );

                /**creando los lotes en el inventario */
                //aqui voy
               
                 DB::table('inventario')->insertGetId(
                    [
                        'lotes_id'          => $id_compra,
                        'articulos_id'=>$articulo['id'],
                        'precio_compra_neto'          => $articulo['descuento_b']==1?$articulo['costo_neto_normal']:$articulo['costo_neto_descuento'],
                        'existencia'=>$articulo['cantidad'],
                        'num_lote_inventario'=>$num_lote_inventario
                    ]
                );
            }

            DB::commit();
            return $id_compra;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

     public function cancelar_compra(Request $request)
    {
         //validaciones
        $validaciones = [
            'id_compra'             => 'required|integer'
        ];

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'id_compra.required'             => 'Seleccione una compra a cancelar'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        $id_compra=$request->id_compra;

        /**se valida que este activa esta compra */

        $compra=MovimientosInventario::where('id','=',$id_compra)->where('tipo_movimientos_id','=',3)->get()->toArray();
        if(count($compra)>0){
            /**existe */
            if($compra[0]['status']!=0){
                /**se verifica si tiene lotes involucrados*/
                $venta_detalle=VentaDetalle::where('lotes_id','=',$id_compra)->get()->toArray();
                /*if(count($compra)>0){
                    //ya tiene ventas asociadas y 
                }*/
                //se pone todo el inventario en 0
                try {
                    DB::beginTransaction();
                    DB::table('inventario')->where('lotes_id', $id_compra)->update(
                        [
                            'existencia'               => 0
                        ]
                    );
                    DB::table('movimientos_inventario')->where('id', $id_compra)->update(
                        [
                            'status'               => 0
                        ]
                    );
                /**todo salio bien y se debe de guardar */
                DB::commit();
                return $id_compra;
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return $th;
                }
            }else{
                return $this->errorResponse('Esta compra ya fue cancelada anteriormente.', 409);
            }
        }else{
            //no se puede cancelar esta compra
             return $this->errorResponse('No se encontró esta venta registrada.', 409);
        }

    }

     public function get_compras(Request $request, $num_compra = 'all', $paginated = false)
    {
        
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $proveedor                  = $request->proveedor;
        $numero_control           = $request->numero_control;
        $status                   = $request->status;
        $fecha_compra          = $request->fecha_compra;
        $resultado_query = MovimientosInventario::
        select(
            'movimientos_inventario.id',
            "num_compra",
            "folio_referencia",
            "fecha_registro",
            "fecha_movimiento",
            "movimientos_inventario.nota",
            "cancelo_id",
            "fecha_cancelacion",
            "nota_cancelacion",
            "tipo_movimientos_id",
            "proveedores.id as id_proveedor",
            "registro_id",
            "iva_porcentaje",
            "fecha_vencimiento_credito",
            "pago_credito",
            "pago_tarjeta",
            "pago_transferencia",
            "pago_cheque",
            "pago_efectivo",
            "movimientos_inventario.status",
                 /**venta operacion */
            DB::raw(
                '(0) AS subtotal'
            ),
            DB::raw(
                '(0) AS descuento'
            ),
            DB::raw(
                '(null) AS iva'
            ),
             DB::raw(
                '(null) AS total'
            ),
            DB::raw(
                '(NULL) AS status_texto'
            ), 
            'nombre_comercial',
            'razon_social',
            'nombre_contacto'
        )
        ->with('compra_detalle')
        ->with('registro:id,nombre')
        ->with('cancelo:id,nombre')
        ->with('costos_incurridos')
        
        /**solo ventas de planes funerarios */
            //solo compras a proveedores
        ->join('proveedores', 'proveedores.id', '=', 'movimientos_inventario.proveedores_id')

        ->where(function ($query) use ($proveedor) {
            return $query->where('nombre_comercial', 'like', '%'.$proveedor.'%')
                ->orWhere('razon_social', 'like', '%'.$proveedor.'%')
                ->orWhere('nombre_contacto', 'like', '%'.$proveedor.'%');
        })
        ->where('tipo_movimientos_id',3)
        ->where(function ($q) use ($num_compra) {
                if (trim($num_compra) == 'all' || trim($num_compra) > 0) {
                     if ($num_compra > 0) {
                        $q->where('movimientos_inventario.num_compra', '=', $num_compra);
                    }
                }
        })
        ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de registro */
                        $q->where('movimientos_inventario.num_compra', '=', $numero_control);
                    }elseif ($filtro_especifico_opcion == 2) {
                        /**filtro por numero de referencia */
                        $q->where('movimientos_inventario.folio_referencia', '=', $numero_control);
                    }
                }
        })
        ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('movimientos_inventario.status', '=', $status);
                }
        })
        ->orderBy('movimientos_inventario.id', 'desc')
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

        foreach ($resultado as $index_venta => &$compra) {
            /**subtotal */
            /**descuento */
            /**IVA */
            /**importe */
             foreach($compra['compra_detalle'] as &$detalle){
                if($detalle['facturable_b']==1){
                     $iva_porcentaje=(1+($compra['iva_porcentaje']/100));
                }else{
                    $iva_porcentaje=1;
                }

                $detalle['subtotal']=($detalle['costo_neto']/$iva_porcentaje);
                if($detalle['descuento_b']==1){
                    $detalle['descuento']=(($detalle['costo_neto']-$detalle['costo_neto_descuento'])/$iva_porcentaje);
                }else{
                    $detalle['descuento']=0;
                }
                $detalle['iva']=($detalle['subtotal']-$detalle['descuento'])*($iva_porcentaje-1);
                $detalle['importe']=(($detalle['subtotal']-$detalle['descuento'])*$detalle['cantidad'])*$iva_porcentaje;


                $detalle['subtotal']=round($detalle['subtotal'],2);
                $detalle['descuento']=round($detalle['descuento'],2);
                $detalle['iva']=round($detalle['iva'],2);
                $detalle['importe']=round($detalle['importe'],2);

                $compra['subtotal']+=$detalle['subtotal'];
                $compra['descuento']+=$detalle['descuento'];
                $compra['iva']+=$detalle['iva'];
                $compra['total']+=$detalle['importe'];
             }

             /**agregagamos los costos incurridos */
              foreach($compra['costos_incurridos'] as &$costo){
                if($costo['facturable_b']==1){
                     $iva_porcentaje=(1+($compra['iva_porcentaje']/100));
                }else{
                    $iva_porcentaje=1;
                }
                $costo['subtotal']=($costo['costo_neto']/$iva_porcentaje);
                $costo['iva']=($costo['subtotal'])*($iva_porcentaje-1);

                $costo['subtotal']=round($costo['subtotal'],2);
                $costo['iva']=round($costo['iva'],2);


                $compra['subtotal']+=$costo['subtotal'];
                $compra['iva']+=$costo['iva'];
                $compra['total']+=$costo['costo_neto'];
              }

            $compra['subtotal']=round($compra['subtotal'],2);
            $compra['iva']=round($compra['iva'],2);
            $compra['total']=round($compra['total'],2);

            $compra['total_compra_texto']=numeros_a_letras($compra['total']);
            $compra['fecha_compra_texto'] = fecha_abr($compra['fecha_movimiento']);
            $compra['fecha_registro_texto'] = fecha_abr($compra['fecha_registro']);

            /**status segun pago o cancelación */
            $total_pagado=$compra['pago_efectivo']+$compra['pago_cheque']+$compra['pago_tarjeta']+$compra['pago_transferencia'];

            if($compra['status']==0){
                $compra['status_texto']='Cancelada';
            }else{
                if($total_pagado>=$compra['total']){
                     $compra['status_texto']='Pagada';
                }else{
                     $compra['status_texto']='Por liquidar';
                }
            }

        } //fin foreach compra

        return $resultado_query;
        /**aqui se puede hacer todo los calculos para llenar la informacion calculada del servicio get_ventas */
    }



    public function pdf_nota_compra(Request $request)
    {
        /*
        $id_compra = 2;
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
        $id_compra           = $requestVentasList['id_compra'];

        //obtengo la informacion de esa venta
        $r = new \Illuminate\Http\Request();
        $r->replace(['sample' => 'sample']);
        $compra = $this->get_compras($r, $id_compra)[0];

        if (empty($compra)) {
            /**datos no encontrados */
            return $this->errorResponse('Error al cargar los datos.', 409);
        }

        /**verificando si el documento aplica para esta solictitud */
        /*if ($datos_venta['numero_convenio_raw'] == null) {
        return 0;
        }*/

        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView('inventarios/nota_compra/nota', ['datos' => $compra, 'empresa' => $empresa]);
        //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
        $name_pdf = strtoupper($compra['num_compra']) . '.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view('inventarios.nota_compra.footer'),
        ]);
        if ($compra['status'] == 0) {
            $pdf->setOptions([
                'header-html' => view('inventarios.nota_compra.header'),
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
                $compra['num_compra'],
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }
    }



     public function get_reporte_existencias_costos($fecha)
    {
       $articulos=Articulos::where('tipo_articulos_id',1)
        ->select(
            'id',
            'codigo_barras',
            'descripcion',
            'precio_compra',
            'precio_venta',
            'tipo_articulos_id',
            'status',
            DB::raw(
                    '(0) AS total_costo_articulo'
            )
        )
        ->with('inventario_existencia_cero')
        ->get()->toArray();
        

        //2- (EA) Entrada de lote por ajustes
       $movimientos=MovimientosInventario::
        select(
            'id',
            'fecha_movimiento',
            'operaciones_id',
            'tipo_movimientos_id',
            'status'
        )
        ->with('detalles')
        ->with('articulosserviciofunerario')
        ->with('compra_detalle')
        ->with('costos_incurridos')
        ->with('operacion:id,status')
        ->where('fecha_movimiento','<=',$fecha)
        ->orderBy('fecha_movimiento', 'asc')
        ->get()->toArray();


        /**asigno el costo de compra a cada articulo directo desde el modulo de articulos.*/
        foreach($articulos as $index_articulo => &$articulo){
            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                if($articulo['id']==$lote['articulos_id']){
                    $lote['costo_costeado']= $articulo['precio_compra'];
                }
            }
        }
        
         foreach($movimientos as $index => $movimiento){
             /**sumando los ingresos de mercancia por ajuste no inventariado
              * 2- (EA) Entrada de lote por ajustes
              */
              if($movimiento['tipo_movimientos_id']==2){
                   foreach($movimiento['detalles'] as $index_detalle => $detalle){
                         foreach($articulos as $index_articulo => &$articulo){
                            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                                if($detalle['articulos_id']==$lote['articulos_id'] && $detalle['lotes_id']==$lote['lotes_id']){
                                    $lote['existencia']+=$detalle['existencia_fisica'];
                                    $lote['ver_inventario_b']=1;
                                }
                            }
                        }
                   }
              }


              /**sumando o restando los ingresos de mercancia por ajuste no inventariado
              * 1- (AI) Ajustes de inventario
              */
              if($movimiento['tipo_movimientos_id']==1){
                   foreach($movimiento['detalles'] as $index_detalle => $detalle){
                         foreach($articulos as $index_articulo => &$articulo){
                            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                                if($detalle['articulos_id']==$lote['articulos_id'] && $detalle['lotes_id']==$lote['lotes_id']){
                                    $lote['existencia']=$detalle['existencia_fisica'];
                                    $lote['ver_inventario_b']=1;
                                }
                            }
                        }
                   }
              }


              /**sumando los ingresos de mercancia por compra
              * 3- (CM) Compra de mercancías a proveedor
              */
              if($movimiento['tipo_movimientos_id']==3 && $movimiento['status']!=0){
                  /**aqui saco el total de articulos y su costeo con tod y costo incurridos */
                  $cantidad_por_compra=0;
                  foreach($movimiento['compra_detalle'] as $index_detalle => $detalle){
                      $cantidad_por_compra+=$detalle['cantidad'];
                  }
                  $costos_total=0;
                  foreach($movimiento['costos_incurridos'] as $index_costo => $costo){
                      $costos_total+=$costo['costo_neto'];
                  }
                  /**el costo incuttido por articulo solo se le suma al costo de adquision de cada articulo */
                  $costo_incurrido_por_articulo=$costos_total/$cantidad_por_compra;

                   foreach($movimiento['compra_detalle'] as $index_detalle => $detalle){
                         foreach($articulos as $index_articulo => &$articulo){
                            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                                if($detalle['articulos_id']==$lote['articulos_id'] && $detalle['movimientos_inventario_id']==$lote['lotes_id']){
                                    $lote['existencia']+=$detalle['cantidad'];
                                    $lote['ver_inventario_b']=1;
                                    if($detalle['descuento_b']==1){
                                        $lote['costo_costeado']= $detalle['costo_neto_descuento']+ $costo_incurrido_por_articulo;
                                    }else{
                                        $lote['costo_costeado']= $detalle['costo_neto']+ $costo_incurrido_por_articulo;
                                    }
                                }
                            }
                        }
                   }
              }


              /**restando mercancia vendida en los servicios funerarios
              * 9- (VM) Venta de mercancías
              */
              if($movimiento['tipo_movimientos_id']==9 && $movimiento['operacion']['status']!=0){
                   foreach($movimiento['articulosserviciofunerario'] as $index_detalle => $detalle){
                         foreach($articulos as $index_articulo => &$articulo){
                            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                                if($detalle['articulos_id']==$lote['articulos_id'] && $detalle['lotes_id']==$lote['lotes_id']){
                                  $lote['existencia']-=$detalle['cantidad'];
                                  $lote['ver_inventario_b']=1;
                                }
                            }
                        }
                   }
              }
         }


        unset($movimientos);
         /**aqui saco los totales de los articulos */
        $total=0;
        foreach($articulos as $index_articulo => &$articulo){
            foreach($articulo['inventario_existencia_cero'] as $index_lote => &$lote){
                    $lote['total_costo_lote']= $lote['existencia']*$lote['costo_costeado'];
                    $articulo['total_costo_articulo']+=$lote['total_costo_lote'];
                    $total+=$articulo['total_costo_articulo'];
            }
        }

        $array=['fecha'=>fecha_abr($fecha),'costo_inventario'=>$total,'articulos'=>$articulos];
         return $array;
    }



}
