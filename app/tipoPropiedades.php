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
}