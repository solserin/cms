<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Propiedades extends Model
{
    protected $table = 'propiedades';


    public function tipoPropiedad()
    {
        return $this->belongsTo('App\tipoPropiedades', 'tipo_propiedades_id', 'id');
    }

    public function filas_columnas()
    {
        return $this->hasMany('App\columnasFilas', 'propiedades_id', 'id');
    }
}