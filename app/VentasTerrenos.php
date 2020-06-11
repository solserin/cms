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
}