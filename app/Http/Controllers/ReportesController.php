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

        $email             = $request->email_send === 'true' ? true : false;
        $email_to          = $request->email_address;
        $datosRequest=[];
        $modulo='';
        $reporte='';
        $fecha='';
        $fecha_inicio='';
        $fecha_fin='';


        if(isset($request->request_parent[0])){
            $datosRequest = json_decode($request->request_parent[0], true);
        }
        

         if(isset($datosRequest['modulo']['value'])){
            $modulo=$datosRequest['modulo']['value'];
            $reporte=$datosRequest['modulo']['value'];
            $fecha=$datosRequest['fecha'];
            $fecha_inicio=$datosRequest['fecha_inicio'];
            $fecha_fin=$datosRequest['fecha_fin'];
        }else{
            $modulo=1;
            $reporte=1;
            $fecha=now();
            $fecha_inicio='1994-01-01';
            $fecha_fin=now();
        }

    
        

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
                 /**valido que ingresó la fecha */
                 if(trim($fecha)==null){
                     return $this->errorResponse('Ingrese la fecha para generar el reporte.',409);
                 }
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
                '',
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
