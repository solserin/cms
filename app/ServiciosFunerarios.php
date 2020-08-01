<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiciosFunerarios extends Model
{
    protected $table = 'servicios_funerarios';


    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function recogio()
    {
        return $this->hasOne('App\User', 'id', 'recogio_id');
    }
}