<?php

namespace App\Http\Controllers;

use App\Propiedades;
use App\tipoPropiedades;
use Illuminate\Http\Request;

class CementerioController extends ApiController
{
    public function get_list(Request $request)
    {
        return Propiedades::with('filas_columnas')->get();
    }
}