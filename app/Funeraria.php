<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funeraria extends Model
{
    //
    protected $table = 'funeraria';

    public function localidad() {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }

    public function regimen() {
        return $this->hasOne('App\SATRegimenes', 'id', 'sat_regimenes_id');
    }
}
