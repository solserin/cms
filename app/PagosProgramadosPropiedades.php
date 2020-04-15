<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagosProgramadosPropiedades extends Model
{
    protected $table = 'pagos_programados_propiedades';


    /**la venta tiene uno o muchos pagos programados */
    public function pagosRealizados()
    {
        return $this->hasMany('App\PagosPropiedades', 'pagos_programados_propiedades_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function tipoPago()
    {
        return $this->belongsTo('App\TipoPagos', 'tipo_pagos_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }
}