<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SATImpuestos;

class SATImpuestosController extends ApiController
{
    public function getImpuestos() {
        $impuestos = SATImpuestos::where('retencion', 0)->select('id as value', 'impuesto as label')->get();
        return $this->showAll($impuestos);
    }

    public function getRetenciones() {
        $impuestos = SATImpuestos::where('retencion', 1)->select('id as value', 'impuesto as label')->get();
        return $this->showAll($impuestos);
    }
}
