<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasPropiedades extends Model
{
    protected $table = 'ventas_propiedades';


    /**la venta tiene uno o muchos pagos programados */
    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramadosPropiedades', 'ventas_propiedades_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }
}