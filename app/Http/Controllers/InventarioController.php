<?php

namespace App\Http\Controllers;

use App\Articulos;
use App\Categorias;
use App\SatUnidades;
use App\Departamentos;
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
        $opcion_caducidad = 1;
        if ($request->tipo_articulo['value'] == 1) {
            /**codigo de barras requerido */
            $validaciones['codigo_barras'] = 'required';
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



    public function get_articulos(Request $request, $id_articulo = 'all', $paginated = '', $id_departamento = 0, $id_categoria = 0)
    {
        $filtro_especifico_opcion = $request->filtro_especifico_opcion;
        $articulo = $request->articulo;
        $numero_control = $request->numero_control;
        $status = $request->status;


        $resultado_query = Articulos::select(
            '*',
            DB::raw(
                '(NULL) AS grava_iva_texto'
            ),
            DB::raw(
                '(NULL) AS caduca_texto'
            )
        )
            ->whereHas('categoria', function ($query) use ($id_categoria) {
                if (trim($id_categoria) != '' && $id_categoria > 0) {
                    $query->where('id', $id_categoria);
                }
            })
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
            ->where(function ($q) use ($status) {
                if (trim($status) != '') {
                    $q->where('articulos.status', '=', $status);
                }
            })
            ->where('descripcion', 'like', '%' . $articulo . '%')
            ->with('tipo_articulo')
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
                $res = DB::table('articulos')->where('id', $request->articulo_id)->update(
                    [

                        'status' =>  0
                    ]
                );
            }

            /**todo salio bien y se debe de modificar */
            DB::commit();
            return $request->articulo_id;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}