<?php

namespace App\Http\Controllers;

use App\Documentos;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class FirmasController  extends ApiController
{
     public function get_areas_firmar(Request $request,$id_documento='')
    {
        $resultado = Documentos::with('areas.firma')->where('documentos.id','=',$id_documento)->get()->toArray();
        if(count($resultado)>0){
            return $resultado;
        }else{
            return $this->errorResponse('No se encontró este documento',409);
        }
    }
}
