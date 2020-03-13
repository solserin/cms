<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Almacenes;

class AlmacenesController extends ApiController
{
    public function getAll() {
        $almacenes = Almacenes::select('id as value', 'almacen as label')->get();
        return $this->showAll($almacenes);
    }
}
