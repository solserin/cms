<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Operaciones extends Model
{
    protected $table = 'operaciones';

    public function venta_terreno()
    {
        return $this->belongsTo('App\VentasTerrenos', 'ventas_terrenos_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS tipo_financiamiento_texto'
                ),
                DB::raw(
                    'DATE(fecha_venta) as fecha_date_format',
                ),
                DB::raw(
                    '(NULL) AS area_nombre'
                ),
                DB::raw(
                    '(NULL) AS tipo_texto'
                ),
                DB::raw(
                    '(NULL) AS fila_raw'
                ),
                DB::raw(
                    '(NULL) AS lote_raw'
                ),
                DB::raw(
                    '(NULL) AS ubicacion_texto'
                ),
            );
    }

    /**la venta tiene uno o muchos pagos programados */
    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramados', 'operaciones_id', 'operacion_id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_abr'
                ),
                DB::raw(
                    '(0) AS status_pago', //0-vencido-1-pendiente,2-vencido
                ),
                DB::raw(
                    '(0) AS status_pago_texto', //0-vencido-1-pendiente,2-pagado
                ),
                DB::raw(
                    '(0) AS intereses'
                ),
                DB::raw(
                    '(0) AS abonado_capital'
                ),
                DB::raw(
                    '(0) AS abonado_intereses'
                ),
                DB::raw(
                    '(0) AS descontado_pronto_pago'
                ),
                DB::raw(
                    '(0) AS descontado_capital'
                ),
                DB::raw(
                    '(0) AS complementado_cancelacion'
                ),
                DB::raw(
                    '(NULL) AS total_cubierto'
                ),
                DB::raw(
                    '(0) AS saldo_neto'
                ),
                DB::raw(
                    '(0) AS dias_vencido'
                ),
                DB::raw(
                    '(0) AS monto_pronto_pago'
                ),
                DB::raw(
                    '(NULL) AS concepto_texto'
                ),
                DB::raw(
                    '(NULL) AS fecha_programada_abr'
                ),

                DB::raw(
                    '(NULL) AS fecha_ultimo_pago'
                )
            )
            ->orderBy('id', 'asc');
    }

    public function beneficiarios()
    {
        return $this->hasMany('App\Beneficiarios', 'operaciones_id', 'operacion_id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }


    public function AjustesPoliticas()
    {
        return $this->hasOne('App\AjustesPoliticasOperacion', 'operaciones_id', 'operacion_id');
    }
}