<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends ApiController
{
    /**
     * parameters lista de la funcion
     * to destinatario
     * to_name nombre del destinatario
     * subject motivo del correo
     * name_pdf nombre del pdf
     * pdf archivo pdf a enviar
     */
    public function pdf_email($to = '', $to_name = '', $subject = '', $name_pdf = '', $pdf = null)
    {
        /**validar que el correo tiene formato correcto */


        $request = new Request([
            'email' => $to,
        ]);

        $this->validate($request, [
            'email' => 'required|email',
        ], [
            'email' => 'El email debe ser un correo válido.',
            'required' => 'Ingrese un email válido.',
        ]);

        $get_funeraria = new EmpresaController();
        $empresa = $get_funeraria->get_empresa_data();
        $data["email"] = $to;
        $data["client_name"] = $to_name;
        $data["subject"] = $subject;
        $data["address"] = config('services.mailsistema.address');
        $data["empresa"] = $empresa;
        $data["name_pdf"] = $name_pdf;
        try {
            //return view('mails.mail', $data);
            Mail::send('mails.mail', $data, function ($message) use ($empresa, $data, $name_pdf, $pdf) {
                $message->from($data["address"], strtoupper($empresa->nombre_comercial));
                $message
                    ->to($data["email"], $data["client_name"])
                    ->subject($data["subject"])
                    ->attachData($pdf->output(), $name_pdf);
            });
        } catch (JWTException $exception) {
            //$this->serverstatuscode = "0";
            //$this->serverstatusdes = $exception->getMessage();
            return $this->errorResponse($exception->getMessage(), 0);
        }
        if (Mail::failures()) {
            $this->statusdesc  =   "Ocurrió un error al enviar el correo.";
            $this->statuscode  =   "0";
            return $this->errorResponse($this->statusdesc,  $this->statuscode);
        } else {
            return $this->successResponse(1, 200);
        }
    }
}