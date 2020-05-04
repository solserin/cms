<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramacionPagosTerrenos extends Model
{
    protected $table = 'programacion_pagos_terrenos';


    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramadosTerrenos', 'programacion_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }
}