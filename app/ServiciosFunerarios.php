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

    public function operacion()
    {
        return $this->belongsTo('App\Operaciones', 'servicios_funerarios_id', 'servicio_id');
    }

    public function nacionalidad()
    {
        return $this->hasOne('App\Nacionalidades', 'id', 'nacionalidades_id');
    }

    public function estado_civil()
    {
        return $this->hasOne('App\EstadosCiviles', 'id', 'estados_civiles_id');
    }

    public function escolaridad()
    {
        return $this->hasOne('App\Escolaridades', 'id', 'escolaridades_id');
    }
}