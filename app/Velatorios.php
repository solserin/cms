<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Velatorios extends Model
{
    protected $table = 'velatorios';







    /**de aqui abajo son cambios de andres */
    public function localidad()
    {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }
}