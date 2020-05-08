<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ProgramacionPagosTerrenos extends Model
{
    protected $table = 'programacion_pagos_terrenos';


    public function Programados()
    {
        return $this->hasMany('App\PagosProgramadosTerrenos', 'programacion_id', 'id');
    }

    public function pagosProgramados()
    {
        return $this->Programados()->select(
            '*',
            DB::raw(
                '(0) AS pagado'
            ),
            DB::raw(
                '(0) AS vencido'
            ),
            DB::raw(
                '(0) AS dias_vencido'
            ),
            DB::raw(
                '(0) AS intereses'
            ),
            DB::raw(
                '(0) AS subtotal_pagado'
            ),
            DB::raw(
                '(0) AS descuento_pagado'
            ),
            DB::raw(
                '(0) AS iva_pagado'
            ),
            DB::raw(
                '(0) AS total_pagado'
            ),
            DB::raw(
                '(0) AS intereses_pagado'
            ),
            DB::raw(
                '(0) AS total_a_pagar'
            ),
            DB::raw(
                '(0) AS intereses_a_pagar'
            ),
            DB::raw(
                '(0) AS fecha_a_pagar'
            ),
            DB::raw(
                '("") AS concepto'
            ),
            DB::raw(
                '("") AS fecha_programada_texto'
            ),


        )->orderBy('id', 'asc');
    }
}