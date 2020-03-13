<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SATProductosServicios;

class SATProductosServiciosController extends ApiController
{
    public function getAll() {
        $data = SATProductosServicios::get();
        return $this->showAll($data);
    }
}
