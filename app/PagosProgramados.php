<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagosProgramados extends Model
{
    protected $table = 'pagos_programados';

    public function pagados()
    {
        return $this->belongsToMany('App\Pagos', 'pagos_pagos_programados', 'pagos_programados_id', 'pagos_id')->as('pagos_cubiertos')->withPivot('monto', 'movimientos_pagos_id')->orderBy('fecha_pago', 'asc');
    }
}