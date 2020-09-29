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
                )
            );
    }

    /**la venta tiene uno o muchos pagos programados */
    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramados', 'operaciones_id', 'operacion_id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS fecha_programada_abr'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_abr'
                ),
                DB::raw(
                    '(0) AS status_pago' //0-vencido-1-pendiente,2-vencido
                ),
                DB::raw(
                    '(0) AS status_pago_texto' //0-vencido-1-pendiente,2-pagado
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
                    '(NULL) AS fecha_ultimo_pago'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago_abr'
                ),

                DB::raw(
                    '(0) AS aplica_pronto_pago_b'
                ),
                DB::raw(
                    '(0) AS descuento_pronto_pago'
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


    /**pagos programados para relacion al consultar pagos  desde pagos controller get_pagos*/
    public function get_pagos_pagos_programados()
    {
        return $this->hasMany('App\PagosProgramados', 'operaciones_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS fecha_programada_abr'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar'
                ),
                DB::raw(
                    '(NULL) AS fecha_a_pagar_abr'
                ),
                DB::raw(
                    '(0) AS status_pago' //0-vencido-1-pendiente,2-vencido
                ),
                DB::raw(
                    '(0) AS status_pago_texto' //0-vencido-1-pendiente,2-pagado
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
                    '(NULL) AS fecha_ultimo_pago'
                ),
                DB::raw(
                    '(NULL) AS fecha_ultimo_pago_abr'
                ),

                DB::raw(
                    '(0) AS aplica_pronto_pago_b'
                ),
                DB::raw(
                    '(0) AS descuento_pronto_pago'
                )
            )
            ->orderBy('id', 'asc');
    }



    public function cliente()
    {
        return $this->belongsTo('App\Clientes', 'clientes_id', 'id');
    }




    public function registro()
    {
        return $this->hasOne('App\User', 'id', 'registro_id');
    }

    public function modifico()
    {
        return $this->hasOne('App\User', 'id', 'modifico_id');
    }

    public function cancelador()
    {
        return $this->hasOne('App\User', 'id', 'cancelo_id');
    }

    /**relacion con venta de planes funerarios */
    public function venta_plan()
    {
        return $this->belongsTo('App\VentasPlanes', 'ventas_planes_id', 'id')
            ->select(
                '*',
                DB::raw(
                    '(NULL) AS tipo_financiamiento_texto'
                )
            );
    }


    public function movimientoinventario()
    {
        return $this->belongsTo('App\MovimientosInventario', 'id', 'operaciones_id');
    }
}