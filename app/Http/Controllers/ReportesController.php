<?php

namespace App\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\EmpresaController;

class ReportesController extends ApiController
{
    public function get_reportes(Request $request)
    {
        $modulo=1;
        $reporte=1;
        $fecha=now();
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

         
        $id_compra = 2;
        $email = false;
        $email_to = 'hector@gmail.com';
        

        $inventario = new InventarioController();
        /**obteniendo datos para los reportes segun la peticion del usuario */



        /**guardo los datos a mostrar en el reporte */
        $datos_reporte=[];
        $name_pdf='';
        $header='';
        $footer='';
        $pdf_template='';


        if ($modulo == 1) {
            $header='reportes.inventario.header';
            $footer='reportes.inventario.footer';
            /**Inventarios*/
             if ($reporte == 1) {
                /**Existencias y Costos*/
                $datos_reporte=$inventario->get_reporte_existencias_costos($fecha);
                $name_pdf='Existencias y Costos';
                $pdf_template='reportes/inventario/existencias_costos/reporte';
             }
        }

        $datos_reporte['name_pdf']=$name_pdf;
        /**creando el pdf */
        $get_funeraria = new EmpresaController();
        $empresa       = $get_funeraria->get_empresa_data();
        $pdf           = PDF::loadView($pdf_template, ['datos' => $datos_reporte, 'empresa' => $empresa]);


        $name_pdf=$name_pdf.'.pdf';

        $pdf->setOptions([
            'title'       => $name_pdf,
            'footer-html' => view($footer),
        ]);
        $pdf->setOptions([
                'header-html' => view($header),
        ]);

        $pdf->setOption('margin-left', 12.5);
        $pdf->setOption('margin-right', 12.5);
        $pdf->setOption('margin-top', 12.5);
        $pdf->setOption('margin-bottom', 12.5);
        $pdf->setOption('page-size', 'letter');

        if ($email == true) {
            /**email */
            /**
             * parameters lista de la funcion
             * to destinatario
             * to_name nombre del destinatario
             * subject motivo del correo
             * name_pdf nombre del pdf
             * pdf archivo pdf a enviar
             */
            /**quiere decir que el usuario desa mandar el archivo por correo y no consultarlo */
            $email_controller = new EmailController();
            $enviar_email     = $email_controller->pdf_email(
                $email_to,
                $request->destinatario,
                $compra['num_compra'],
                $name_pdf,
                $pdf
            );
            return $enviar_email;
            /**email fin */
        } else {
            return $pdf->inline($name_pdf);
        }

         return $this->errorResponse($datos_reporte,409);
    }
}
