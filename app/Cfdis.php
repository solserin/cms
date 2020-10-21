<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cfdis extends Model
{
    protected $table = 'cfdis';

    public function cliente()
    {
        return $this->belongsTo('App\Clientes', 'clientes_id', 'id');
    }

    public function cfdis_operaciones()
    {
        return $this->hasMany('App\CfdisOperaciones', 'cfdis_id', 'id')->select('*')->distinct('operaciones_id');
    }

    public function pagos_asociados()
    {
        /**relacion por pago o nota de egreso*/
        return $this
            ->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')
            ->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id')
            ->select('tipo_relacion_id', 'uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id')
            ->where('tipo_relacion_id', '<>', 1);
        /**diferente de ingreso */
    }

    public function cfdis_relacionados()
    {
        /**relacion por relacion de comprobante sat */
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')->where('tipo_relacion_id', '=', 1);
    }

    public function cfdis_pagados()
    {
        /**relacion de cfdis que cubre este pago*/
        return $this->hasMany('App\CfdisTipoRelacion', 'cfdis_id', 'id')
        //->join('cfdis', 'cfdis.id', '=', 'cfdis_tipo_relacion.cfdis_id')
        //->select('tipo_relacion_id', 'uuid', 'tipo_relacion_id', 'cfdis_tipo_relacion.sat_metodos_pago_id', 'monto_relacion', 'cfdis.status', 'sat_tipo_comprobante_id', 'cfdis_tipo_relacion.cfdis_id')
            ->where('tipo_relacion_id', '=', 2);
        /**solo de tipo pago */
    }
}
