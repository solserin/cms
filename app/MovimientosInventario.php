<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class MovimientosInventario extends Model
{
    protected $table = 'movimientos_inventario';


    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function detalles()
    {
        return $this->hasMany('App\AjusteInventarioDetalle', 'movimientos_inventario_id', 'id')
            ->select(
                '*',
                /**0- resto, 1- igual, 3- sumo */
                DB::raw(
                    '(NULL) AS resultado_ajuste'
                ),
                DB::raw(
                    '(NULL) AS resultado_ajuste_texto'
                ),
                DB::raw(
                    '(0) AS diferencia'
                )
            )
            ->orderBy('articulos_id', 'asc');
    }
}