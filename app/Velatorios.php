<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Velatorios extends Model
{
    protected $table = 'velatorios';

    public function localidad() {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }
}
