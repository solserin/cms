<?php

namespace App\Http\Controllers;

use App\Proveedores;
use Illuminate\Http\Request;
use PDF;

class ProveedoresController extends ApiController
{
    public function create(Request $request) {
        $proveedorData = (object) $request->all();
        $rfc = trim($request->rfc);

        $existsRFC = Proveedores::whereRaw('LOWER(rfc) = LOWER(?)', $rfc)->exists();
        if ($existsRFC) {
            return $this->errorResponse('Proveedor con el RFC '.$rfc.' ya existe', 409);
        }

        $proveedor = new Proveedores;
        foreach ($proveedorData as $key => $value) {
            $proveedor->{$key} = $proveedorData->{$key};
        }

        $proveedor->save();
        if ($proveedor->id) {
            return response()->json(['message' => 'Proveedor creado', 'proveedor' => $proveedor->id], 201);
        }

        return $this->errorResponse('Proveedor con el RFC '.$rfc.' ya existe', 501);
    }

    public function save($id, Request $request)
    {
        if (Proveedores::where('id', $id)->exists()) {
            $proveedor = Proveedores::find($id);

            $rfc = trim($request->rfc);

            $existsRFC = Proveedores::whereRaw('LOWER(rfc) = LOWER(?)', $rfc)->first();
            if ($existsRFC && $existsRFC->id != $proveedor->id) {
                return $this->errorResponse('Proveedor con el RFC '.$rfc.' ya existe', 409);
            }

            $proveedorData = (object) $request->all();            

            foreach ($proveedorData as $key => $value) {
                $proveedor->{$key} = !is_null($proveedorData->{$key}) ? $proveedorData->{$key} : $proveedor->{$key};
            }
        }

        $proveedor->save();

        return response()->json(['message' => 'Proveedor modificado', 'proveedor' => $proveedor->id], 200);
    }

    public function getAll(Request $request) {
        $search = $request->search;
        $status = $request->estado;

        $proveedores = Proveedores::where(function($query) use (&$search) {
            $query->where('nombre_comercial', 'like', '%'.$search.'%')
            ->orWhere('razon_social', 'like', '%'.$search.'%')
            ->orWhere('rfc', 'like', '%'.$search.'%')
            ->orWhere('nombre_contacto', 'like', '%'.$search.'%')
            ->orWhere('telefono', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%');
        })->where(function ($q) use ($status) {
            if (!is_null($status)) {
                $q->where('status', $status);
            }
        })->get();

        $paginatedData = $this->showAllPaginated($proveedores);
        return response($paginatedData, 200);
    }

    public function get($id) {
        if (Proveedores::where('id', $id)->exists()) {
            $proveedor = Proveedores::where('id', $id)->get()->first();
            return response()->json($proveedor, 200);
        } else {
            return $this->errorResponse('Proveedor no encontrado', 404);
        }
    }

    public function getPDF(Request $request) {
        $search = $request->search;
        $status = $request->estado;
        
        $proveedores = Proveedores::where(function($query) use (&$search) {
            $query->where('nombre_comercial', 'like', '%'.$search.'%')
            ->orWhere('razon_social', 'like', '%'.$search.'%')
            ->orWhere('rfc', 'like', '%'.$search.'%')
            ->orWhere('nombre_contacto', 'like', '%'.$search.'%')
            ->orWhere('telefono', 'like', '%'.$search.'%')
            ->orWhere('email', 'like', '%'.$search.'%');
        })->where(function ($q) use ($status) {
            if (!is_null($status)) {
                $q->where('status', $status);
            }
        })->get();

        $getFuneraria = new EmpresaController();
        $empresa = $getFuneraria->get_empresa_data();
        $pdf = PDF::loadView('lista_proveedores', ['usuarios' => $proveedores, 'empresa' => $empresa]);
        $pdf->setOptions([
            'title' => 'Reporte de Proveedores',
            'footer-html' => view('footer'),
            'header-html' => view('header'),
        ]);
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 15);

        return $pdf->inline();
    }

    public function proveedorPDF($id, Request $request) {
        $proveedor = Proveedores::find($id);

        $getFuneraria = new EmpresaController();
        $empresa = $getFuneraria->get_empresa_data();
        $pdf = PDF::loadView('proveedor', ['proveedor' => $proveedor, 'empresa' => $empresa]);
        $pdf->setOptions([
            'title' => 'Proveedor',
            'footer-html' => view('footer'),
            'header-html' => view('header'),
        ]);

        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 15);

        return $pdf->inline();
    }
}
