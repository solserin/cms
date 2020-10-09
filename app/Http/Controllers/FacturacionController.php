<?php

namespace App\Http\Controllers;

use App\MetodosPago;
use App\SatFormasPago;
use App\SatPais;
use App\TiposRelacion;
use App\TipoComprobantes;
use Illuminate\Http\Request;
use App\SATProductosServicios;
use App\SatUnidades;
use App\SatUsosCfdi;;

class FacturacionController extends ApiController
{
    /**get tipo de comprobantes */
    public function get_tipos_comprobante()
    {
        $datos = TipoComprobantes::whereNotIn('id', [3, 4])->orderBy('id', 'asc')->get();
        foreach ($datos as $key => &$dato) {
            $dato['tipo'] = $dato['tipo'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_metodos_pago()
    {
        $metodos = MetodosPago::orderBy('id', 'asc')->get();


        foreach ($metodos as $key => &$metodo) {
            $metodo['metodo'] = $metodo['metodo'] . ' (' . $metodo['clave'] . ')';
        }

        return $metodos;
    }

    public function get_sat_formas_pago()
    {
        return SatFormasPago::orderBy('id', 'asc')->get();
    }


    public function get_tipos_relacion()
    {
        return TiposRelacion::orderBy('id', 'asc')->get();
    }

    public function get_claves_productos_sat()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SATProductosServicios::whereNotIn('clave', ['84111506', '42262102'])->get();

        foreach ($datos as $key => &$dato) {
            $dato['clave'] = $dato['descripcion'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUnidades::get();

        foreach ($datos as $key => &$dato) {
            $dato['clave'] = $dato['unidad'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_usos_cfdi()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatUsosCfdi::where('aplica_b', 1)->get();

        foreach ($datos as $key => &$dato) {
            $dato['uso'] = $dato['uso'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

    public function get_sat_paises()
    {
        /**todos menos el tipo de servicios de facturacion */
        $datos = SatPais::get();

        foreach ($datos as $key => &$dato) {
            $dato['pais'] = $dato['pais'] . ' (' . $dato['clave'] . ')';
        }
        return $datos;
    }

        public function get_empresa_tipo_operaciones()
    {
        /**los diferentes tipos de operaciones que maneja la empresa */
        $datos = [
            [
                'id'=>1,
            'tipo'=>'Venta de terrenos',
            'ver_b'=>1
            ],
            [
                'id'=>2,
            'tipo'=>'Mantenimiento en cementerio',
            'ver_b'=>0
            ],
            [
                'id'=>3,
            'tipo'=>'Servicios funerarios',
            'ver_b'=>1
            ],
            [
                'id'=>4,
            'tipo'=>'Venta de plan funerario a futuro',
            'ver_b'=>1
            ],
            [
                'id'=>5,
            'tipo'=>'Servicios con extremidadades',
            'ver_b'=>0
            ],
            [
                'id'=>6,
            'tipo'=>'Ventas en general',
            'ver_b'=>0
            ]
        ];

        $operaciones=[];
        foreach ($datos as $key => $value) {
        if($value['ver_b']==1){
            array_push($operaciones,$value);
        }
        }
        return $operaciones;
    }


    
}