<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    protected $table = 'municipios';    
    
    public function estado() {
        return $this->belongsTo('App\Estados', 'estado_id', 'id');
    }
}
