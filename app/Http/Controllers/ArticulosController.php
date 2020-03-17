<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articulos;
use App\PreciosArticulos;
use App\ArticulosImpuestos;
use App\ArticulosRetenciones;

class ArticulosController extends ApiController
{
    public function create(Request $request) {
        $data = (object)$request->articulo;
        $precios = (object)$request->precios;
        $articulo = new Articulos;
        $existsCodigo = Articulos::whereRaw('LOWER(codigo_barras) = LOWER(?)', $data->codigo_barras)->exists();
        if ($existsCodigo) {
            return $this->errorResponse('Articulo con el codigo de barras '.$data->codigo_barras.' ya existe', 409);
        }

        $articulo->codigo_barras = $data->codigo_barras;
        $articulo->nombre = $data->nombre;
        $articulo->costo_neto = $data->costo_neto;
        $articulo->sat_productos_servicios_id = $data->sat_productos_servicios_id;
        $articulo->cuenta_predial = $data->cuenta_predial;
        $articulo->grupos_profeco_id = $data->grupos_profeco_id;
        $articulo->maximo = $data->maximo;
        $articulo->minimo = $data->minimo;
        $articulo->almacenes_id = $data->almacenes_id;
        $articulo->unidades_venta_id = $data->unidades_venta_id;
        $articulo->unidades_compra_id = $data->unidades_compra_id;
        $articulo->factor = $data->factor;
        $articulo->localizacion = $data->localizacion;
        if (!is_null($data->facturable)) {
            $articulo->facturable = $data->facturable;
        }

        if (!is_null($data->caduca)) {
            $articulo->caduca = $data->caduca;
        }

        if (!is_null($data->rentable)) {
            $articulo->rentable = $data->rentable;
        }
        $articulo->familias_id = $data->familias_id;
        $articulo->tipos_producto_id = $data->tipos_producto_id;
        
        $articulo->save();

        if ($articulo->id) {
            if (!empty($data->impuestos)) {
                foreach($data->impuestos as $impuesto) {
                    $articuloImpuesto = new ArticulosImpuestos;
                    $articuloImpuesto->articulos_id = $articulo->id;
                    $articuloImpuesto->sat_impuestos_id = $impuesto;

                    $articuloImpuesto->save();
                }
            }
            if (!empty($data->retenciones)) {
                foreach($data->retenciones as $retencion) {
                    $articuloRetencion = new ArticulosRetenciones;
                    $articuloRetencion->articulos_id = $articulo->id;
                    $articuloRetencion->sat_impuestos_id = $impuesto;

                    $articuloRetencion->save();
                }
            }

            $precioNormal = new PreciosArticulos;
            $precioNormal->precios_id = 1;
            $precioNormal->precio = $precios->precio1;
            $precioNormal->articulos_id = $articulo->id;

            $precioDescuento = new PreciosArticulos;
            $precioDescuento->precios_id = 2;
            $precioDescuento->precio = $precios->precio2;
            $precioDescuento->articulos_id = $articulo->id;

            $precioNormal->save();
            $precioDescuento->save();
        }



        if ($articulo->id) {
            return response()->json(['message' => 'Articulo creado', 'articulo' => $articulo->id], 201);
        }

        return $this->errorResponse('Articulo con el codigo de barras '.$data->codigo_barras.' ya existe', 501);
    }

    public function save($id, Request $request) {
        if (Articulos::where('id', $id)->exists()) {
            $articulo = Articulos::find($id);

            $data = (object)$request->articulo;
                
            $existsCodigo = Articulos::whereRaw('LOWER(codigo_barras) = LOWER(?)', $data->codigo_barras)->first();
            if ($existsCodigo && $existsCodigo->id != $articulo->id) {
                return $this->errorResponse('Articulo con el codigo de barras '.$data->codigo_barras.' ya existe', 409);
            }

            $articulo->codigo_barras = is_null($data->codigo_barras) ? $articulo->codigo_barras : $data->codigo_barras;
            $articulo->nombre = is_null($data->nombre) ? $articulo->nombre : $data->nombre;
            $articulo->costo_neto = is_null($data->costo_neto) ? $articulo->costo_neto : $data->costo_neto;
            $articulo->sat_productos_servicios_id = is_null($data->sat_productos_servicios_id) ? $articulo->sat_productos_servicios_id : $data->sat_productos_servicios_id;
            $articulo->cuenta_predial = is_null($data->cuenta_predial) ? $articulo->cuenta_predial : $data->cuenta_predial;
            $articulo->grupos_profeco_id = is_null($data->grupos_profeco_id) ? $articulo->grupos_profeco_id : $data->grupos_profeco_id;
            $articulo->maximo = is_null($data->maximo) ? $articulo->maximo : $data->maximo;
            $articulo->minimo = is_null($data->minimo) ? $articulo->minimo : $data->minimo;
            $articulo->almacenes_id = is_null($data->almacenes_id) ? $articulo->almacenes_id : $data->almacenes_id;
            $articulo->unidades_venta_id = is_null($data->unidades_venta_id) ? $articulo->unidades_venta_id : $data->unidades_venta_id;
            $articulo->unidades_compra_id = is_null($data->unidades_compra_id) ? $articulo->unidades_compra_id : $data->unidades_compra_id;
            $articulo->factor = is_null($data->factor) ? $articulo->factor : $data->factor;
            $articulo->localizacion = is_null($data->localizacion) ? $articulo->localizacion : $data->localizacion;
            $articulo->facturable = is_null($data->facturable) ? $articulo->facturable : $data->facturable;
            $articulo->caduca = is_null($data->caduca) ? $articulo->caduca : $data->caduca;
            $articulo->rentable = is_null($data->rentable) ? $articulo->rentable : $data->rentable;
            $articulo->familias_id = is_null($data->familias_id) ? $articulo->familias_id : $data->familias_id;
            $articulo->tipos_producto_id = is_null($data->tipos_producto_id) ? $articulo->tipos_producto_id : $data->tipos_producto_id;
            
            if (!is_null($data->impuestos)) {
                ArticulosImpuestos::where('articulos_id', $articulo->id)->delete();
                if (count($data->impuestos)) {
                    foreach($data->impuestos as $impuesto) {
                        $articuloImpuesto = new ArticulosImpuestos;
                        $articuloImpuesto->articulos_id = $articulo->id;
                        $articuloImpuesto->sat_impuestos_id = $impuesto;

                        $articuloImpuesto->save();
                    }
                }
            }
            if (!is_null($data->retenciones)) {
                ArticulosRetenciones::where('articulos_id', $articulo->id)->delete();
                if (count($data->retenciones)) {
                    foreach($data->retenciones as $retencion) {
                        $articuloRetencion = new ArticulosRetenciones;
                        $articuloRetencion->articulos_id = $articulo->id;
                        $articuloRetencion->sat_impuestos_id = $impuesto;

                        $articuloRetencion->save();
                    }
                }
            }

            PreciosArticulos::where('articulos_id', $articulo->id)->delete();
            
            $precios = (object)$request->precios;
            $precioNormal = new PreciosArticulos;
            $precioNormal->precios_id = 1;
            $precioNormal->precio = $precios->precio1;
            $precioNormal->articulos_id = $articulo->id;

            $precioDescuento = new PreciosArticulos;
            $precioDescuento->precios_id = 2;
            $precioDescuento->precio = $precios->precio2;
            $precioDescuento->articulos_id = $articulo->id;

            $precioNormal->save();
            $precioDescuento->save();
        }

        $articulo->save();

        return response()->json(['message' => 'Articulo modificado', 'articulo' => $articulo->id], 200);
    }

    public function getAll(Request $request) {
        $search = $request->search;
        $tipoProducto = $request->tipo_producto;
        $grupoProfeco = $request->grupo_profeco;
        $almacen = $request->almacen;
        $familia = $request->familia;

        $articulos = Articulos::with('unidadCompra','unidadVenta', 'tipoProducto',
        'satProductoServicio', 'familia',
        'familia.categoria', 'almacen',
        'grupoProfeco', 'impuestos',
        'impuestos.satImpuesto', 'retenciones',
        'retenciones.satImpuesto', 'precios')->where(function($query) use (&$search) {
            $query->where('codigo_barras', 'like', '%'.$search.'%')
            ->orWhere('nombre', 'like', '%'.$search.'%')
            ->orWhere('localizacion', 'like', '%'.$search.'%');
        })->where(function ($q) use ($tipoProducto, $grupoProfeco, $almacen, $familia) {
            if (!is_null($tipoProducto)) {
                $q->where('tipos_producto_id', $tipoProducto);
            }
            if (!is_null($grupoProfeco)) {
                $q->where('grupos_profeco_id', $grupoProfeco);
            }
            if (!is_null($almacen)) {
                $q->where('almacenes_id', $almacen);
            }
            if (!is_null($familia)) {
                $q->where('familias_id', $familia);
            }
        })->get();

        $paginatedData = $this->showAllPaginated($articulos);
        return response($paginatedData, 200);
    }
}
