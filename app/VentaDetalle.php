<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalle';

    public function articulosventa()
    {
        return $this->hasMany('App\MovimientosInventario', 'movimientos_inventario_id', 'id');
    }
}