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
}