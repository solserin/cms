<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FunerariaController extends ApiController
{
    /**REGISTRAR PLAN FUNERARIO*/
    public function registrar_plan(Request $request)
    {
        //validaciones directas sin condicionales
        $validaciones = [
            'descripcion' => 'required',
            'descripcion_ingles' => 'required',
            'conceptos.0.conceptos' => [
                'required'
            ],
        ];

        /**FIN DE  VALIDACIONES CONDICIONADAS*/
        $mensajes = [
            'descripcion.required' => 'Ingrese el nombre del plan funerario.',
            'descripcion_ingles.required' => 'Ingrese el nombre del plan funerario(en inglés).',
            'conceptos.0.conceptos.required' => 'Debe ingresar al menos 1 Artículo/Servicio que aplique en la sección "Plan Funerario".',
        ];
        request()->validate(
            $validaciones,
            $mensajes
        );

        return $this->errorResponse('Ya existe un precio para esta propiedad con este financiamiento', 409);

        try {

            DB::beginTransaction();
            $id_plan = 0;
            $id_plan = DB::table('planes_funerarios')->insertGetId(
                [
                    'plan' => $request->descripcion,
                    'plan_ingles' => $request->descripcion_ingles,
                    'nota' => $request->nota,
                    'nota_ingles' => $request->nota_ingles,
                    'registro_id' => (int) $request->user()->id,
                    'fecha_registro' => now(),
                    'fecha_modificacion' => now()
                ]
            );

            /**todo salio bien y se debe de guardar */
            DB::commit();
            return $id_plan;
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }
}