<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MovimientosInventario extends Model
{
    protected $table = 'movimientos_inventario';

    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function detalles()
    {
        return $this->hasMany('App\AjusteInventarioDetalle', 'movimientos_inventario_id', 'id')
            ->select(
                '*',
                /**0- resto, 1- igual, 3- sumo */
                DB::raw(
                    '(NULL) AS resultado_ajuste'
                ),
                DB::raw(
                    '(NULL) AS resultado_ajuste_texto'
                ),
                DB::raw(
                    '(0) AS diferencia'
                )
            )
            ->orderBy('ajuste_detalle.articulos_id', 'asc');
    }

    public function articulosserviciofunerario()
    {
        return $this->hasMany('App\VentaDetalle', 'movimientos_inventario_id', 'id')

            ->join('inventario', 'inventario.lotes_id', '=', 'venta_detalle.lotes_id');
    }

    public function articulos_operacion()
    {
        return $this->hasMany('App\VentaDetalle', 'movimientos_inventario_id', 'id')
            ->join('articulos', 'articulos.id', '=', 'venta_detalle.articulos_id')
            ->join('sat_productos_servicios', 'articulos.sat_productos_servicios_id', '=', 'sat_productos_servicios.id')
            ->join('sat_unidades', 'articulos.sat_unidades_venta', '=', 'sat_unidades.id')
        ;
    }
}
