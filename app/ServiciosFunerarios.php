<?php

namespace App;

use Illuminate\Support\Facades\DB;
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

    public function terreno()
    {
        return $this->hasOne('App\VentasTerrenos', 'id', 'ventas_terrenos_id')
            ->select(
                'ubicacion',
                'operaciones.ventas_terrenos_id',
                'operaciones.id as id_operacion',
                'ventas_terrenos.id',
                DB::raw(
                    '(NULL) as ubicacion_servicio'
                ),
                DB::raw(
                    '(NULL) as status_operacion'
                ),
                DB::raw(
                    '(NULL) as status_operacion_texto'
                )
            )
            ->join('operaciones', 'operaciones.ventas_terrenos_id', '=', 'ventas_terrenos.id');
    }
}