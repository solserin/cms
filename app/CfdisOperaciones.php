<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CfdisOperaciones extends Model
{
    protected $table = 'cfdis_operaciones';

    public function operaciones()
    {
        return $this->hasMany('App\Operaciones', 'id', 'operaciones_id')
            ->select(
                '*'
            );
    }
}
