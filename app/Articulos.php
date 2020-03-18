<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulos extends Model
{
    protected $table = 'articulos';

    public function unidadCompra() {
        return $this->hasOne('App\Unidades', 'id', 'unidades_compra_id');
    }    
    
    public function unidadVenta() {
        return $this->hasOne('App\Unidades', 'id', 'unidades_venta_id');
    }

    public function tipoProducto() {
        return $this->hasOne('App\TiposProducto', 'id', 'tipos_producto_id');
    }    
    
    public function satProductoServicio() {
        return $this->hasOne('App\SATProductosServicios', 'id', 'sat_productos_servicios_id');
    }    
    
    public function familia() {
        return $this->hasOne('App\Familias', 'id', 'familias_id');
    }

    public function almacen() {
        return $this->hasOne('App\Almacenes', 'id', 'almacenes_id');
    }

    public function grupoProfeco() {
        return $this->hasOne('App\GruposProfeco', 'id', 'grupos_profeco_id');
    }    
    
    public function impuestos() {
        return $this->hasMany('App\ArticulosImpuestos');
    }

    public function retenciones() {
        return $this->hasMany('App\ArticulosRetenciones');
    }

    public function precios() {
        return $this->hasMany('App\PreciosArticulos');
    }

    public function paquete() {
        return $this->hasMany('App\ArticulosPaquete', 'articulos_parent_id', 'id');
    }
}
