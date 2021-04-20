<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Firmas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


         $id_tipo_documento=DB::table('tipo_documentos')->insertGetId(['tipo' => 'Venta de Terrenos']);
         $documentos=[
             [
                 'nombre'=>'FORMATO DE SOLICITUD',
                 'firmas'=>[
                        'cliente'
                 ]
            ],
            [
                'nombre'=>'CONVENIO',
                'firmas'=>[
                    'cliente'
                ]
            ],
            [
            'nombre'=>'TÍTULO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ESTADO DE CUENTA',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'TALONARIO DE PAGOS',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'REGLAMENTO DE PAGO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ACUSE DE CANCELACIÓN',
            'firmas'=>[
            'cliente'
            ]
            ],
         ];
         foreach ($documentos as $key => $documento) {
             $id_documento=DB::table('documentos')->insertGetId([
                 'documento' => $documento['nombre'],
                 'descripcion' =>null,
                 'tipo_documentos_id' => $id_tipo_documento,
            ]);
            foreach ($documento['firmas'] as $key_area => $area) {
                DB::table('areas_firmas')->insertGetId([
                 'area' => $area,
                 'documentos_id' => $id_documento,
            ]);
            }
        }
            /** */



        $id_tipo_documento=DB::table('tipo_documentos')->insertGetId(['tipo' => 'Venta de planes Funerarios']);
         $documentos=[
             [
                 'nombre'=>'FORMATO DE SOLICITUD',
                 'firmas'=>[
                        'cliente'
                 ]
            ],
            [
                'nombre'=>'CONVENIO',
                'firmas'=>[
                    'cliente'
                ]
            ],
            [
            'nombre'=>'CONSTANCIA DE FINIQUITO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ESTADO DE CUENTA',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'TALONARIO DE PAGOS',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'REGLAMENTO DE PAGO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ACUSE DE CANCELACIÓN',
            'firmas'=>[
            'cliente'
            ]
            ],
         ];
         foreach ($documentos as $key => $documento) {
             $id_documento=DB::table('documentos')->insertGetId([
                 'documento' => $documento['nombre'],
                 'descripcion' =>null,
                 'tipo_documentos_id' => $id_tipo_documento,
            ]);
            foreach ($documento['firmas'] as $key_area => $area) {
                DB::table('areas_firmas')->insertGetId([
                 'area' => $area,
                 'documentos_id' => $id_documento,
            ]);
            }
         }


          $id_tipo_documento=DB::table('tipo_documentos')->insertGetId(['tipo' => 'Servicios Funerarios']);
         $documentos=[
             [
                 'nombre'=>'SOLICITUD DE SERVICIO',
                 'firmas'=>[
                     'Entrega de pertenencias',
                     'No portaba documentos'
                 ]
            ],
            [
                'nombre'=>'AUTORIZACIÓN DE SERVICIO FUNERARIO',
                'firmas'=>[
                    'cliente',
                    'testigo 1',
                    'testigo 2'
                ]
            ],
            [
            'nombre'=>'CONSTANCIA DE FINIQUITO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ESTADO DE CUENTA',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'TALONARIO DE PAGOS',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'REGLAMENTO DE PAGO',
            'firmas'=>[
            'cliente'
            ]
            ],
            [
            'nombre'=>'ACUSE DE CANCELACIÓN',
            'firmas'=>[
            'cliente'
            ]
            ],
         ];
         foreach ($documentos as $key => $documento) {
             $id_documento=DB::table('documentos')->insertGetId([
                 'documento' => $documento['nombre'],
                 'descripcion' =>null,
                 'tipo_documentos_id' => $id_tipo_documento,
            ]);
            foreach ($documento['firmas'] as $key_area => $area) {
                DB::table('areas_firmas')->insertGetId([
                 'area' => $area,
                 'documentos_id' => $id_documento,
            ]);
            }
         }
        
    }
}
