<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasPropiedades extends Model
{
    protected $table = 'ventas_propiedades';


    /**la venta tiene uno o muchos pagos programados */
    public function pagosProgramados()
    {
        return $this->hasMany('App\PagosProgramadosPropiedades', 'ventas_propiedades_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function propiedad()
    {
        return $this->belongsTo('App\Propiedades', 'propiedades_area_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function beneficiarios()
    {
        return $this->hasMany('App\BeneficiariosPropiedades', 'ventas_propiedades_id', 'id');
        //return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
    }

    public function vendedor()
    {
        return $this->belongsTo('App\User', 'vendedor_id', 'id');
    }

    public function antiguedad()
    {
        return $this->belongsTo('App\AntiguedadesVenta', 'antiguedad_ventas_id', 'id');
    }
}