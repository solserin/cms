<?php

namespace App\Http\Controllers;

use App\MetodosPago;
use App\SatFormasPago;

use App\TiposRelacion;
use App\TipoComprobantes;
use Illuminate\Http\Request;
use App\SATProductosServicios;
use App\SatUnidades;

;

class FacturacionController extends ApiController
{
    /**get tipo de comprobantes */
    public function get_tipos_comprobante()
    {
        return TipoComprobantes::
        whereNotIn('id',[3,4])->orderBy('id', 'asc')->get();
    }

    public function get_metodos_pago()
    {
        $metodos= MetodosPago::
        orderBy('id', 'asc')->get();


foreach ($metodos as $key => &$metodo) {
   $metodo['metodo']=$metodo['metodo'].' ('.$metodo['clave'].')';
}

return $metodos;

    }

    public function get_sat_formas_pago()
    {
        return SatFormasPago::
        orderBy('id', 'asc')->get();
    }


    public function get_tipos_relacion()
    {
        return TiposRelacion::
        orderBy('id', 'asc')->get();
    }

    public function get_claves_productos_sat()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos= SATProductosServicios::whereNotIn('clave', ['84111506', '42262102'])->get();

        foreach ($datos as $key => &$dato) {
            $dato['clave']=$dato['descripcion'].' ('.$dato['clave'].')';
         }
return $datos;         
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos= SatUnidades::get();

        foreach ($datos as $key => &$dato) {
            $dato['clave']=$dato['unidad'].' ('.$dato['clave'].')';
         }
return $datos;         
    }


    
}
