<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $table = 'pagos';

    public function pagos_pagos_programados()
    {
        return $this->belongsToMany('App\PagosProgramados', 'pagos_pagos_programados', 'pagos_id', 'pagos_programados_id')->as('pagos_cubiertos')
            ->select(
                '*',
                DB::raw(
                    '(0) AS pago_total'
                ),
            )
            ->withPivot('monto', 'movimientos_pagos_id');
    }
}