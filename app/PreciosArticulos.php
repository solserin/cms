<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreciosArticulos extends Model
{

    protected $table  = 'precios_articulos';
    public function precioParent() {
        return $this->belongsTo('App\Precios', 'precios_id', 'id');
    }
}
