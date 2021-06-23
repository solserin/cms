<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ReportesController extends ApiController
{
    public function get_reportes(Request $request)
    {
        $modulo=1;
        $reporte=1;
        $fecha='2021-06-022';
        $rango_fechas='';
        if(isset($request->modulo['value'])){
            $modulo=$request->modulo['value'];
            $reporte=$request->reporte['value'];
            $fecha=$validaciones['fecha'];
            $rango_fechas='';
        }
        //return $this->errorResponse($request['author'],409);
        //validaciones
        $validaciones = [
            'modulo.value'            => 'required',
            'reporte.value'            => 'required',
            'fecha'            => '',
            'rango_fechas'            => ''
        ];
        
        if ($modulo == 1) {
            /**Inventarios*/
             if ($reporte == 1) {
                /**Existencias y Costos*/
                $validaciones['fecha'] = 'required|date_format:Y-m-d';
             }else{
                 $validaciones['rango_fechas'] = 'required';
             }
        }
    
        /**FIN DE VALIDACIONES*/
        $mensajes = [
            'required'                           => 'Ingrese este dato',
            'ajuste.fecha_caducidad.date_format' => 'indique la fecha de caducidad(Y-m-d)',
        ];

       /* request()->validate(
            $validaciones,
            $mensajes
        );
        */

        $inventario = new InventarioController();

        /**obteniendo datos para los reportes segun la peticion del usuario */
        if ($modulo == 1) {
            /**Inventarios*/
             if ($reporte == 1) {
                /**Existencias y Costos*/
                $datos=$inventario->get_reporte_existencias_costos($fecha);
                return $this->errorResponse($datos,409);
             }
        }
    }
}
