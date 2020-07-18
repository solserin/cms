<?php

namespace App\Http\Controllers;

use App\Articulos;
use App\Categorias;
use App\Departamentos;
use App\SATProductosServicios;
use App\SatUnidades;
use App\TipoArticulos;
use Illuminate\Http\Request;

class InventarioController extends ApiController
{
    public function get_tipo_articulos()
    {
        return TipoArticulos::get();
    }

    public function get_categorias()
    {
        return $datos = Departamentos::with('categorias')->get();
    }

    public function get_unidades()
    {
        return SatUnidades::whereIn('id', [1, 2, 4, 5])->get();
    }

    public function get_sat_unidades()
    {
        /**todos menos el tipo de servicios de facturacion */
        return SATProductosServicios::whereNotIn('clave', ['84111506'])->get();
    }
}