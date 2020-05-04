<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasTerrenos extends Model
{
    protected $table = 'ventas_terrenos';


    /**la venta tiene uno o muchos pagos programados */
    public function programacionPagos()
    {
        return $this->hasMany('App\ProgramacionPagosTerrenos', 'ventas_terrenos_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    /**obteniendo el progrmacion_pagos_terrnos actual y vigente "el ultimo registrado" */
    public function programacionPagosActual()
    {
        /**seleccionado el progrmacion pagos terrenos actual */
        return $this->programacionPagos()->orderBy('id', 'desc')->limit(1);
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }


    public function beneficiarios()
    {
        return $this->hasMany('App\BeneficiariosTerrenos', 'ventas_terrenos_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id');
    }

    public function antiguedad()
    {
        return $this->belongsTo('App\AntiguedadesVenta', 'antiguedad_ventas_id', 'id');
    }


    public function ajustesIntereses()
    {
        return $this->hasOne('App\VentaTerrenosAjustesIntereses', 'ventas_terrenos_id', 'id');
    }
}