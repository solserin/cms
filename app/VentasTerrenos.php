<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasTerrenos extends Model
{
    protected $table = 'ventas_terrenos';

    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }



    public function tipo_propiedad()
    {
        return $this->belongsTo('App\tipoPropiedades', 'tipo_propiedades_id', 'id')
            ->select(
                'id',
                'tipo',
                'capacidad'
            );
    }
}