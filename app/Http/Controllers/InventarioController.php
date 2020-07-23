<?php

namespace App\Http\Controllers;

use App\Ajustes;
use App\Ajustes as AppAjustes;
use PDF;
use App\Articulos;
use App\Categorias;
use App\SatUnidades;
use App\Departamentos;
use App\MovimientosInventario;
use App\TipoArticulos;
use Illuminate\Http\Request;
use App\SATProductosServicios;
use Illuminate\Support\Facades\DB;

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
        return SatUnidades::whereIn('id', [1, 2])->get();
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
            'tipoAjuste.value' => 'required',
            'ajuste.*.id' => [
                'required'
            ],
            'ajuste.*.caduca_b' => [
                'required'
            ],
            'ajuste.*.fecha_caducidad' => [
                ''
            ],
            'ajuste.*.lote' => [
                ''
            ],
            'ajuste.*.existencia_fisica' => [
                'required'
            ],
            'ajuste.*.existencia_sistema' => [
                'required'
            ]
        ];

        if ($request->tipoAjuste['value'] == 1) {
            /**es un ajuste de no inventariados */
            foreach ($request->ajuste as $key => $articulo) {
                if ($articulo['caduca_b'] == 1) {
                    $validaciones['ajuste.' . $key . '.fecha_caducidad'] = 'required|date_format:Y-m-d';
                }
            }
        }

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'required' => 'Ingrese la clave del ajuste',
            'ajuste.id.required' => 'ingrese el id del artículo',
            'ajuste.existencia_fisica.required' => 'ingrese la existencia física',
            'ajuste.existencia_sistema.required' => 'ingrese la existencia en el sistema',
            'ajuste.caduca_b.required' => 'indique si al artículo caduca',
            'ajuste.fecha_caducidad.required' => 'indique la fecha de caducidad',
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
                /**se crea un lote y despues se agregan al inventario */
                $id_movimiento = DB::table('movimientos_inventario')->insertGetId(
                    [
                        'nota' => $request->nota,
                        'fecha_registro' => now(),
                        'registro_id' => (int) $request->user()->id,
                        'tipo_movimientos_id' => 2,
                        'subtotal' => 0,
                        'descuento' => 0,
                        'impuestos' => 0,
                        'total' => 0
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
                            'fecha_caducidad' => $articulo['fecha_caducidad'] != 'N/A' ? $articulo['fecha_caducidad'] : NULL,
                            'existencia_sistema' => $articulo['existencia_sistema'],
                            'existencia_fisica' => $articulo['existencia_fisica'],
                            'movimientos_inventario_id' => $id_movimiento,
                            'lotes_id' => $id_movimiento,
                            'articulos_id' => $articulo['id'],
                            /**entrada de lostes por ajuste */
                        ]
                    );
                }
                /**actualizando el inventario */
                foreach ($request->ajuste as $key => $articulo) {
                    DB::table('inventario')->insert(
                        [
                            'lotes_id' => $id_movimiento,
                            'precio_compra_neto' => $articulo['precio_compra'],
                            'fecha_caducidad' => $articulo['fecha_caducidad'] != 'N/A' ? $articulo['fecha_caducidad'] : NULL,
                            'existencia' => $articulo['existencia_fisica'],
                            'articulos_id' => $articulo['id'],
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
            'id_articulo_modificar' => '',
            'descripcion' => 'required',
            'descripcion_ingles' => 'required',
            'tipo_articulo.value' => 'numeric|required',
            'departamento.value' => 'numeric|required',
            'categoria.value' => 'numeric|required',
            'unidad_sat.value' => 'numeric|required',
            'opcion_iva.value' => 'numeric|required',
            'opcion_caducidad.value' => 'numeric|required',
            'minimo_inventario' => 'integer|required|min:1',
            'maximo_inventario' => 'integer|required|gte:minimo_inventario',
            'opcion_caducidad.value' => 'numeric|required',
            'costo_compra' => 'numeric|required|min:1',
            'costo_venta' => 'numeric|required|min:1',
            'codigo_barras' => ''
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
            $request->codigo_barras = NULL;
            $opcion_caducidad = 0;
        }

        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'id_articulo_modificar.required' => 'Ingrese un la clave única del artículo',
            'required' => 'Ingrese este dato',
            'numeric' => 'Este dato debe ser un número',
            'integer' => 'Este dato debe ser un número entero',
            'min' => 'la cantidad debe ser mínimo 1 (Uno)',
            'gte' => 'la cantidad debe ser igual o mayor al minimo de inventario'
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );


        $unidad_compra = '';
        $unidad_venta = '';

        if ($request->tipo_articulo['value'] == 1) {
            /**unidad de pieza */
            $unidad_compra = 1;
            $unidad_venta = 1;
        } else {
            /**unidad de servicio */
            $unidad_compra = 2;
            $unidad_venta = 2;
        }
        $id_articulo = 0;
        try {
            DB::beginTransaction();
            if ($tipo_servicio == 'agregar') {
                $id_articulo = DB::table('articulos')->insertGetId(
                    [
                        'imagen' => trim($request->imagen) === '' ? NULL : trim($request->imagen),
                        'tipo_articulos_id' => $request->tipo_articulo['value'],
                        'sat_productos_servicios_id' => $request->unidad_sat['value'],
                        'factor' => 1,
                        'codigo_barras' => $request->codigo_barras,
                        'descripcion' => $request->descripcion,
                        'descripcion_ingles' => $request->descripcion_ingles,
                        'precio_compra' => $request->costo_compra,
                        'precio_venta' => $request->costo_venta,
                        'minimo' => $request->minimo_inventario,
                        'maximo' => $request->maximo_inventario,
                        'caduca_b' => $opcion_caducidad,
                        'grava_iva_b' => $request->opcion_iva['value'],
                        'categorias_id' => $request->categoria['value'],
                        'sat_unidades_compra' => $unidad_compra,
                        'sat_unidades_venta' => $unidad_venta,
                        'nota' => $request->nota
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
                            return $this->errorResponse('No se debe cambiar la opción caducidad, pues ya existe inventario de este artículo.', 409);
                        }

                        if ($request->tipo_articulo['value'] != $inventario[0]['tipo_articulos_id']) {
                            return $this->errorResponse('No se debe cambiar el tipo de artículo, pues ya existe inventario de este artículo.', 409);
                        }
                    }
                } else {
                    return $this->errorResponse('Este artículo no fue encontrado en la Base de Datos.', 409);
                }

                /**verificar que no cambie el tipo de caducidad si ya fue vendido algo de ese producto */
                /**es modificar */
                DB::table('articulos')->where('id', '=', $request->id_articulo_modificar)->update(
                    [
                        'imagen' => trim($request->imagen) === '' ? NULL : trim($request->imagen),
                        'tipo_articulos_id' => $request->tipo_articulo['value'],
                        'sat_productos_servicios_id' => $request->unidad_sat['value'],
                        'factor' => 1,
                        'codigo_barras' => $request->codigo_barras,
                        'descripcion' => $request->descripcion,
                        'descripcion_ingles' => $request->descripcion_ingles,
                        'precio_compra' => $request->costo_compra,
                        'precio_venta' => $request->costo_venta,
                        'minimo' => $request->minimo_inventario,
                        'maximo' => $request->maximo_inventario,
                        'caduca_b' => $opcion_caducidad,
                        'grava_iva_b' => $request->opcion_iva['value'],
                        'categorias_id' => $request->categoria['value'],
                        'sat_unidades_compra' => $unidad_compra,
                        'sat_unidades_venta' => $unidad_venta,
                        'nota' => $request->nota
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
        $articulo = $request->articulo;
        $numero_control = $request->numero_control;
        $resultado_query = MovimientosInventario::select(
            'id',
            'registro_id',
            'fecha_registro',
            'tipo_movimientos_id',
            DB::raw(
                '(NULL) AS fecha_registro_texto'
            ),
            DB::raw(
                '(NULL) AS tipo_ajuste_texto'
            ),
        )
            ->with('registro:id,nombre')
            ->with('detalles.articulos:id,descripcion,codigo_barras')
            ->where(function ($q) use ($numero_control, $filtro_especifico_opcion) {
                if (trim($numero_control) != '') {
                    if ($filtro_especifico_opcion == 1) {
                        /**filtro por numero de solicitud */
                        $q->where('movimientos_inventario.id', '=',  $numero_control);
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
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
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
            foreach ($ajuste['detalles'] as $key_detalle => &$detalle) {
                if ($detalle['existencia_sistema'] == $detalle['existencia_fisica']) {
                    $detalle['resultado_ajuste'] = 1;
                    $detalle['resultado_ajuste_texto'] = 'Sin Cambios';
                } elseif ($detalle['existencia_sistema'] < $detalle['existencia_fisica']) {
                    if ($ajuste['tipo_movimientos_id'] == 1) {
                        $detalle['resultado_ajuste'] = 0;
                        $detalle['resultado_ajuste_texto'] = 'Extravío de Mercancías';
                    } else {
                        $detalle['resultado_ajuste'] = 1;
                        $detalle['resultado_ajuste_texto'] = 'Ingreso de Mercancía no Inventariada';
                    }
                } else {
                    $detalle['resultado_ajuste'] = 2;
                    $detalle['resultado_ajuste_texto'] = 'Reingreso de Mercancías';
                }
            }
        }

        return $resultado_query;
    }

    public function get_articulos(Request $request, $id_articulo = 'all', $paginated = '', $id_departamento = 0, $id_categoria = 0, $tipo_articulo = 0, $solo_inventariable = 0)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $articulo = $request->articulo;
        $numero_control = $request->numero_control;
        $status = $request->status;
        $id_articulo_request = $request->id_articulo;
        $codigo_barras_request = $request->codigo_barras;
        $resultado_query = Articulos::select(
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
                        $q->where('articulos.id', '=',  $numero_control);
                    } else if ($filtro_especifico_opcion == 2) {
                        if (trim($numero_control) != '') {
                            /**filtro por numero de solicitud */
                            $q->where('articulos.codigo_barras', '=',  $numero_control);
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
            ->get();

        $resultado = array();
        if ($paginated == 'paginated') {
            /**queire el resultado paginado */
            $resultado_query = $this->showAllPaginated($resultado_query)->toArray();
            $resultado = &$resultado_query['data'];
        } else {
            $resultado_query = $resultado_query->toArray();
            $resultado = &$resultado_query;
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
                    $articulo['estatus_inventario_b'] = '0';
                    $articulo['estatus_inventario_texto'] = 'Desabastecido';
                } elseif ($existencia <= $articulo['maximo']) {
                    $articulo['estatus_inventario_b'] = '1';
                    $articulo['estatus_inventario_texto'] = 'Abastecido';
                } else {
                    $articulo['estatus_inventario_b'] = '2';
                    $articulo['estatus_inventario_texto'] = 'Sobrestock';
                }
            } else {
                $articulo['existencia'] = 'N/A';


                $articulo['estatus_inventario_b'] = '1';
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
                        'status' =>  1
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
                                'status' =>  0
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
            $email =  $request->email_send === 'true' ? true : false;
            if ($email == true) {
                if (!$request->email_addres || !$request->destinatario) {
                    $this->errorResponse('Es necesario un correo y un destinatario', 409);
                }
            }
            $email_to = $request->email_address;
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
            $empresa = $get_funeraria->get_empresa_data();
            $pdf = PDF::loadView('inventarios/inventario_completo/inventario', ['empresa' => $empresa, 'inventario' => $inventario]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = 'Reporte de Inventario Registrado.pdf';
            $pdf->setOptions([
                'title' => $name_pdf,
                'footer-html' => view('inventarios.inventario_completo.footer'),
            ]);

            $pdf->setOptions([
                'header-html' => view('inventarios.inventario_completo.header')
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
            $email =  $request->email_send === 'true' ? true : false;
            if ($email == true) {
                if (!$request->email_addres || !$request->destinatario) {
                    $this->errorResponse('Es necesario un correo y un destinatario', 409);
                }
            }
            $email_to = $request->email_address;
            $datos_request = json_decode($request->request_parent[0], true);
            $id_ajuste = $datos_request['id_ajuste'];

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
            $empresa = $get_funeraria->get_empresa_data();
            $pdf = PDF::loadView('inventarios/ajustes/ajustes', ['empresa' => $empresa, 'ajustes' => $ajuste]);
            //return view('lista_usuarios', ['usuarios' => $res, 'empresa' => $empresa]);
            $name_pdf = 'Reporte de Ajuste de Inventario.pdf';
            $pdf->setOptions([
                'title' => $name_pdf,
                'footer-html' => view('inventarios.ajustes.footer'),
            ]);
            $pdf->setOptions([
                'header-html' => view('inventarios.ajustes.header')
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
}