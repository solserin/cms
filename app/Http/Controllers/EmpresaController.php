<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Funeraria;
use App\RegistroPublico;
use App\Cementerios;
use App\Crematorios;
use App\Velatorios;
use App\Salas;
use App\Facturacion;
use App\SATMonedas;

class EmpresaController extends ApiController
{

    public function save(Request $request)
    {
        $funerariaData = (object) $request->all();

        $funeraria = Funeraria::get()->first();
        if (!$funeraria) {
            $funeraria = new Funeraria;
        }

        foreach ($funerariaData as $key => $value) {
            $funeraria->{$key} = !is_null($funerariaData->{$key}) ? $funerariaData->{$key} : $funeraria->{$key};
        }

        $funeraria->save();

        return response()->json(['message' => 'funeraria creada satisfactoriamente'], 200);
    }

    public function saveRegistroPublico(Request $request)
    {
        $registroData = (object) $request->all();

        $funeraria = Funeraria::get()->first();
        $registro = RegistroPublico::get()->first();
        if (!$registro) {
            $registro = new RegistroPublico;
        }

        foreach ($registroData as $key => $value) {
            $registro->{$key} = !is_null($registroData->{$key}) ? $registroData->{$key} : $registro->{$key};
        }
        $registro->funeraria_id = $funeraria->id;
        $registro->save();

        return response()->json(['message' => 'registro publico creado satisfactoriamente'], 200);
    }

    public function getRegistroPublico()
    {
        $registro = RegistroPublico::with('localidadNP')
            ->with('localidadNP.municipio')
            ->with('localidadNP.municipio.estado')
            ->with('localidadRPC')
            ->with('localidadRPC.municipio')
            ->with('localidadRPC.municipio.estado')
            ->get()->first();

        if ($registro) {
            $data = ['registro' => $registro];
            return response()->json($data, 200);
        }

        return $this->errorResponse('Registro Publico not found', 404);
    }

    public function get()
    {
        $funeraria = Funeraria::with('regimen')->with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data = array();

        if ($funeraria) {
            $data = ['funeraria' => $funeraria];
            return response()->json($data, 200);
        }

        return $this->errorResponse('Funeraria not found', 404);
    }

    ///cementerio
    public function saveCementerio(Request $request)
    {
        $data = $request->all();

        $cementerio = Cementerios::get()->first();
        if (!$cementerio) {
            $cementerio = new Cementerios;
        }

        foreach ($data as $key => $value) {
            $cementerio->{$key} = !is_null($data[$key]) ? $data[$key] : $cementerio->{$key};
        }
        $funeraria = Funeraria::get()->first();
        $cementerio->funeraria_id = $funeraria->id;

        $cementerio->save();
        return response()->json(['message' => 'cementerio guardado satisfactoriamente'], 200);
    }

    public function getCementerio()
    {
        $cementerio = Cementerios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data = array();

        if ($cementerio) {
            return response()->json($cementerio, 200);
        }

        return $this->errorResponse('Cementerio not found', 404);
    }

    ///crematorio
    public function saveCrematorio(Request $request)
    {
        $data = $request->all();

        $crematorio = Crematorios::get()->first();
        if (!$crematorio) {
            $crematorio = new Crematorios;
        }

        foreach ($data as $key => $value) {
            $crematorio->{$key} = !is_null($data[$key]) ? $data[$key] : $crematorio->{$key};
        }
        $funeraria = Funeraria::get()->first();
        $crematorio->funeraria_id = $funeraria->id;

        $crematorio->save();
        return response()->json(['message' => 'crematorio guardado satisfactoriamente'], 200);
    }

    public function getCrematorio()
    {
        $crematorio = Crematorios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data = array();

        if ($crematorio) {
            return response()->json($crematorio, 200);
        }

        return $this->errorResponse('Crematorio not found', 404);
    }

    ///velatorios
    public function saveVelatorio(Request $request)
    {
        $data = $request->all();

        $velatorio = Velatorios::get()->first();
        if (!$velatorio) {
            $velatorio = new Velatorios;
        }

        $salas = $data['salas'];
        $dataVelatorio = $data['velatorio'];
        foreach ($dataVelatorio as $key => $value) {
            $velatorio->{$key} = !is_null($dataVelatorio[$key]) ? $dataVelatorio[$key] : $velatorio->{$key};
        }
        $funeraria = Funeraria::get()->first();
        $velatorio->funeraria_id = $funeraria->id;
        $velatorio->save();

        //Sala 1
        $sala1 = Salas::find(1);
        if (!$sala1) {
            $sala1 = new Salas;
        }
        $sala1->sala = $salas[0];
        $sala1->velatorios_id = $velatorio->id;
        //Sala 2
        $sala2 = Salas::find(2);
        if (!$sala2) {
            $sala2 = new Salas;
        }
        $sala2->sala = $salas[1];
        $sala2->velatorios_id = $velatorio->id;
        //Sala 3
        $sala3 = Salas::find(3);
        if (!$sala3) {
            $sala3 = new Salas;
        }
        $sala3->sala = $salas[2];
        $sala3->velatorios_id = $velatorio->id;

        $sala1->save();
        $sala2->save();
        $sala3->save();
        return response()->json(['message' => 'velatorio guardado satisfactoriamente'], 200);
    }

    public function getVelatorio()
    {
        $velatorio = Velatorios::with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        $data = array();

        if ($velatorio) {
            $sala1 = Salas::find(1);
            $sala2 = Salas::find(2);
            $sala3 = Salas::find(3);
            $salas = [$sala1->sala, $sala2->sala, $sala3->sala];

            return response()->json(['velatorio' => $velatorio, 'salas' => $salas], 200);
        }

        return $this->errorResponse('Velatorio not found', 404);
    }

    public function validateCERFile(Request $request)
    {
        $extension = $request->certificate->getClientOriginalExtension();
        if ($extension !== 'cer') {
            return $this->errorResponse(['message' => 'El archivo no es un certificado'], 412);
        }

        $path = $request->certificate->path();
        $data = getCertificateData($path);
        $serialArray = str_split($data['serialNumberHex']);
        //To validate serial number
        $finalCert = "";
        for ($x = 0; $x < count($serialArray); $x++) {
            $finalCert .= ($x % 2 != 0) ? $serialArray[$x] : "";
        }

        if (!$finalCert or strlen($finalCert) != 20) {
            return $this->errorResponse(['message' => 'El numero de certificado no es valido'], 412);
        }

        //Validates if certificate is valid
        $validFrom = new \DateTime();
        $validFrom->setTimestamp($data['validFrom_time_t']);
        $validTo = new \DateTime();
        $validTo->setTimestamp($data['validTo_time_t']);
        $today = new \DateTime();
        if (!($today >= $validFrom) and ($today <= $validTo)) {
            return $this->errorResponse(['message' => 'El certificado no es valido'], 412);
        }

        return response()->json(['certificate' => ''], 200);
    }

    public function validateKEYFile(Request $request)
    {
        $extension = $request->key->getClientOriginalExtension();
        if ($extension !== 'key') {
            return $this->errorResponse(['message' => 'El archivo no es un archivo key'], 412);
        }

        return response()->json(['key' => ''], 200);
    }

    public function saveFacturacion(Request $request)
    {

        $data = $request->all();
        $facturacion = Facturacion::get()->first();
        if (!$facturacion) {
            $facturacion = new Facturacion;
        }

        $request->password = $request->password === 'null' ? null : $request->password;
        $request->certificateFile = $request->certificateFile === 'null' ? null : $request->certificateFile;
        $request->keyFile = $request->keyFile === 'null' ? null : $request->keyFile;
        $request->email_emisor = $request->email_emisor === 'null' ? null : $request->email_emisor;
        $request->sat_monedas_id = $request->sat_monedas_id === 'null' ? null : $request->sat_monedas_id;

        if (!is_null($request->certificateFile)) {
            $certificateFileName = 'certificate_' . date('YmdHis') . "." . $request->certificateFile->getClientOriginalExtension();
            $request->certificateFile->storeAs('cer', $certificateFileName);
            $facturacion->cer = $certificateFileName;

            $path = $request->certificateFile->path();
            $data = getCertificateData($path);

            $serialArray = str_split($data['serialNumberHex']);
            //To validate serial number
            $finalCert = "";
            for ($x = 0; $x < count($serialArray); $x++) {
                $finalCert .= ($x % 2 != 0) ? $serialArray[$x] : "";
            }

            $facturacion->numero_cert = $finalCert;
        }

        if (!is_null($request->keyFile)) {
            $keyFileName = 'key_' . date('YmdHis') . "." . $request->keyFile->getClientOriginalExtension();
            $request->keyFile->storeAs('key', $keyFileName);
            $facturacion->key = $keyFileName;
        }

        $facturacion->password = !is_null($request->password) ? $request->password : $facturacion->password;
        $facturacion->email_emisor = !is_null($request->email_emisor) ? $request->email_emisor : $facturacion->email_emisor;
        $facturacion->sat_monedas_id = !is_null($request->sat_monedas_id) ? $request->sat_monedas_id : $facturacion->sat_monedas_id;

        $facturacion->save();

        return response()->json(['key' => ''], 200);
    }

    public function getFacturacion()
    {
        $facturacion = Facturacion::get()->first();
        if (!$facturacion) {
            return $this->errorResponse('Facturacion not found', 404);
        }

        $moneda = SATMonedas::find($facturacion->sat_monedas_id);
        $facturacion->moneda = $moneda;
        return response()->json($facturacion, 200);
    }



    /**obtengo los datos de la empresa para crear los header de los reportes */
    public function get_empresa_data()
    {
        $funeraria = Funeraria::with('regimen')->with('localidad')->with('localidad.municipio')->with('localidad.municipio.estado')->get()->first();
        return $funeraria;
    }
}