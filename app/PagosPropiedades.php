<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagosPropiedades extends Model
{
    protected $table = 'pagos_propiedades';

    public function tipoPagoSat()
    {
        return $this->belongsTo('App\SatFormasPago', 'sat_formas_pago_id', 'id');
    }
}