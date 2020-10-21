<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cfdis extends Model
{
    protected $table = 'cfdis';

    public function cliente()
    {
        return $this->belongsTo('App\Clientes', 'clientes_id', 'id');
    }

    public function cfdis_operaciones()
    {
        return $this->hasMany('App\CfdisOperaciones', 'cfdis_id', 'id')->select('*')->distinct('operaciones_id');
    }

    public function pagos_asociados()
    {
        /**relacion por pago */
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')->where('tipo_relacion_id', '=', 2);
    }

    public function cfdis_relacionados()
    {
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id');
    }
}
