<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SATImpuestos;

class SATImpuestosController extends ApiController
{
    public function getImpuestos() {
        $impuestos = SATImpuestos::select('id as value', 'impuesto as label')->get();
        return $this->showAll($impuestos);
    }

    public function getRetenciones() {
        $impuestos = SATImpuestos::select('id as value', 'impuesto as label')->get();
        return $this->showAll($impuestos);
    }
}
