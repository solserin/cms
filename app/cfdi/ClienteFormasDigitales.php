<?php
namespace App\cfdi;

use DOMDocument;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use XSLTProcessor;

class ClienteFormasDigitales
{

    private $xml;
    private $autentica;
    private $cadena_original_xslt;

    public function __construct($xmlPath)
    {
        $this->xml = new DOMDocument();
        $this->xml->load($xmlPath) or die("XML invalido");
        $this->cadena_original_xslt = Storage::disk(ENV('STORAGE_DISK_CREDENTIALS'))->path(ENV('CADENA_ORIGINAL_FILE'));
    }

    public function timbrar($parametros)
    {
        /* conexion al web service */
        $client = new SoapClient(ENV('WEB_SERVICE_DEVELOP'), array('encoding' => 'UTF-8'));
        return $client->TimbrarCFDI($parametros);
    }

    public function sellarXML($certFile, $keyFile)
    {
        $private        = openssl_pkey_get_private(file_get_contents($keyFile));
        $cert           = file_get_contents($certFile);
        $certificado    = str_replace(array('\n', '\r'), '', base64_encode($cert));
        $data           = openssl_x509_parse(file_get_contents($certFile . '.pem'), true);
        $serial_number  = $data['serialNumberHex'];
        $no_certificado = $this->getNoCertificado($serial_number);
        $fecha_actual   = substr(date('c'), 0, 19);
        $comprobante    = $this->xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0);
        $comprobante->setAttribute('Fecha', $fecha_actual);
        $cadena_original = $this->generarCadenaOriginal();
        openssl_sign($cadena_original, $signature, $private, "sha256WithRSAEncryption");
        $sello = base64_encode($signature);
        $comprobante->setAttribute('Sello', $sello);
        $comprobante->setAttribute('NoCertificado', $no_certificado);
        $comprobante->setAttribute('Certificado', $certificado);
        return $this->xml->saveXML($this->xml->documentElement);
        //return $this->xml->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0)->getAttribute('NoCertificado');
    }

    public function getNoCertificado($serial)
    {
        $noCertificado = "";

        if ((strlen($serial) % 2) == 1) {
            $serial = " " . $serial;
        }
        for ($i = 0; $i < strlen($serial) / 2; $i++) {
            $aux = substr($serial, $i * 2, ($i * 2) + 2);
            $noCertificado .= substr($aux, 1, 1);
        }
        return $noCertificado;
    }

    public function generarCadenaOriginal()
    {
        $XSL = new DOMDocument();
        $XSL->load($this->cadena_original_xslt);
        $proc = new XSLTProcessor();
        @$proc->importStyleSheet($XSL);
        return $proc->transformToXML($this->xml);
    }
}