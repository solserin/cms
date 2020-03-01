<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistroPublico extends Model
{
    protected $table = 'registro_publico';

    public function localidadNP() {
        return $this->hasOne('App\Localidades', 'id', 'ciudad_np');
    }

    public function localidadRPC() {
        return $this->hasOne('App\Localidades', 'id', 'ciudad_rpc');
    }
}
