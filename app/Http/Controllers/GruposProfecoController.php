<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GruposProfeco;

class GruposProfecoController extends ApiController
{
    public function getAll() {
        $grupos = GruposProfeco::select('id as value', 'ver_nombre as label')->get();
        return $this->showAll($grupos);
    }
}
