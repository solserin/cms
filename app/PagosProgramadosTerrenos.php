<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PagosProgramadosTerrenos extends Model
{
    protected $table = 'pagos_programados_terrenos';


    /**la venta tiene uno o muchos pagos programados */
    public function pagosRealizados()
    {
        return $this->hasMany('App\PagosTerrenos', 'pagos_programados_terrenos_id', 'id')->select(
            '*',
            DB::raw(
                '("") AS fecha_realizado_texto'
            )
        )->orderBy('id', 'asc');
    }

    public function conceptoPago()
    {
        return $this->belongsTo('App\ConceptosPagos', 'conceptos_pagos_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }
}