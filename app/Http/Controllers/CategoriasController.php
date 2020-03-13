<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categorias;

class CategoriasController extends ApiController
{
    public function getAll() {
        $categorias = Categorias::select('id as value', 'categoria as label')->get();
        return $this->showAll($categorias);
    }
}
