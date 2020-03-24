<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipoPropiedades extends Model
{
    protected $table = 'tipo_propiedades';

    public function propiedades()
    {
        return $this->hasMany('App\Propiedades', 'tipo_propiedades_id', 'id');
    }

    //defino los precios que tienen cada tipo de propiedad
    public function precios()
    {
        return $this->hasMany('App\PreciosPropiedades', 'tipo_propiedades_id', 'id');
    }
}