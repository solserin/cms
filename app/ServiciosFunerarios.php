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
        return $this->hasOne('App\Operaciones', 'servicios_funerarios_id', 'id')->select(
            'id',
            'clientes_id',
            'subtotal',
            'descuento',
            'impuestos',
            'tasa_iva',
            'total',
            'servicios_funerarios_id',
            'fecha_operacion',
            'fecha_registro',
            'fecha_modificacion',
            'modifico_id',
            'registro_id',
            'fecha_cancelacion',
            'motivos_cancelacion_id',
            'cantidad_a_regresar_cancelacion',
            'cancelo_id',
            'nota_cancelacion',
            'status'
        );
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

    public function materialrentado()
    {
        return $this->hasMany('App\MaterialRentado', 'servicios_funerarios_id', 'id')
            ->select('servicios_funerarios_id', 'articulos_id', 'descripcion', 'cantidad', 'material_rentado.nota')
            ->join('articulos', 'material_rentado.articulos_id', '=', 'articulos.id');
    }


    public function titulo()
    {
        return $this->hasOne('App\Titulos', 'id', 'titulos_id');
    }
}