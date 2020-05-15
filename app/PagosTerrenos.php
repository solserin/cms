<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagosTerrenos extends Model
{
    protected $table = 'pagos_terrenos';

    public function tipoPagoSat()
    {
        return $this->belongsTo('App\SatFormasPago', 'sat_formas_pago_id', 'id');
    }


    public function RegistroUsuario()
    {
        return $this->belongsTo('App\User', 'registro_id', 'id');
    }

    public function Cobrador()
    {
        return $this->belongsTo('App\User', 'cobrador_id', 'id');
    }


    public function Tipo()
    {
        return $this->belongsTo('App\TipoPagos', 'tipo_pagos_id', 'id');
    }
}