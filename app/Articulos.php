<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Articulos extends Model
{
    protected $table = 'articulos';


    public function categoria()
    {
        return $this->belongsTo('App\Categorias', 'categorias_id', 'id');
    }

    public function unidad_compra()
    {
        return $this->belongsTo('App\SatUnidades', 'sat_unidades_compra', 'id');
    }

    public function unidad_venta()
    {
        return $this->belongsTo('App\SatUnidades', 'sat_unidades_venta', 'id');
    }

    public function tipo_articulo()
    {
        return $this->belongsTo('App\TipoArticulos', 'tipo_articulos_id', 'id');
    }

    public function inventario()
    {
        return $this->hasMany('App\Inventario', 'articulos_id', 'id')->select('*')
            ->where('existencia', '>', 0)
            ->orderBy('lotes_id', 'desc')->limit(150);
    }
}
