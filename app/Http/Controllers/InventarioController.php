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
            'id_articulo' => '',
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
            $validaciones['id_articulo'] = 'required';
        }
        $opcion_caducidad = 1;
        if ($request->tipo_articulo['value'] == 1) {
            /**codigo de barras requerido */
            $validaciones['codigo_barras'] = 'required';
            /**revisando si existe el codigo de berras */
            $articulo = Articulos::where('codigo_barras', $request->codigo_barras)->first();
            if (!empty($articulo)) {
                if ($articulo->status == 1) {
                    return $this->errorResponse('El código de barras ingresado ya ha sido registrado.', 409);
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
            'id_articulo.required' => 'Ingrese un la clave única del artículo',
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
                /**es modificar */
                DB::table('ventas_terrenos')->where('id', '=', $request->id_venta)->update(
                    [
                        'ubicacion' => $request->ubicacion,
                        'propiedades_id' => $request->propiedades_id,
                        'tipo_propiedades_id' => $request->tipo_propiedades_id,
                        'vendedor_id' => (int) $request->vendedor['value'],
                        'tipo_financiamiento' => $request->tipo_financiamiento,
                        'salarios_minimos' => $request->salarios_minimos
                    ]
                );
            }

            DB::commit();
            return
                $tipo_servicio == 'agregar' ? $id_articulo : $request->id_articulo;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}