<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasPlanes extends Model
{
    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id')
            ->select(
                'id',
                'nombre'
            );
    }
}