<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class servicios_funerarios_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** insertando titulos */
        DB::table('titulos')->insert(['titulo' => 'Sr/Sra']);
        DB::table('titulos')->insert(['titulo' => 'Joven/Srta']);
        DB::table('titulos')->insert(['titulo' => 'Niño/Niña']);
        DB::table('titulos')->insert(['titulo' => 'Recién Nacido']);

        /**tipo de servicios funerarios */
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Embalsamar']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Velación']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Cremacion']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Inhunación']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Traslado']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Exhumación']);
        DB::table('tipo_servicios_funerarios')->insert(['tipo' => 'Reinhumación']);

        /**Tipo Solicitud*/
        DB::table('tipo_solicitud')->insert(['tipo' => 'Servicio Funerario']);
        DB::table('tipo_solicitud')->insert(['tipo' => 'Exhumación']);

        /**escolaridades */
        DB::table('escolaridades')->insert(['escolaridad' => 'ninguna']);
        DB::table('escolaridades')->insert(['escolaridad' => 'preescolar']);
        DB::table('escolaridades')->insert(['escolaridad' => 'primaria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'secundaria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'preparatoria']);
        DB::table('escolaridades')->insert(['escolaridad' => 'profesional']);
        DB::table('escolaridades')->insert(['escolaridad' => 'posgrado']);
        DB::table('escolaridades')->insert(['escolaridad' => 'se ignora']);

        /**afilaciones */
        DB::table('afiliaciones')->insert(['afiliacion' => 'ninguna']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'imss']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'pemex']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'unidad médica privada']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'imss prospera']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'isste']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'semar']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'sedena']);
        DB::table('afiliaciones')->insert(['afiliacion' => 'otros']);


        /**estados civiles */
        DB::table('estados_civiles')->insert(['estado' => 'soltero (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'casado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'separado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'divorciado (a)']);
        DB::table('estados_civiles')->insert(['estado' => 'unión libre']);
        DB::table('estados_civiles')->insert(['estado' => 'viudo']);

        /**sitio muerte */
        DB::table('sitios_muerte')->insert(['sitio' => 'domicilio']);
        DB::table('sitios_muerte')->insert(['sitio' => 'imss']);
        DB::table('sitios_muerte')->insert(['sitio' => 'pemex']);
        DB::table('sitios_muerte')->insert(['sitio' => 'unidad médica privada']);
        DB::table('sitios_muerte')->insert(['sitio' => 'imss prospera']);
        DB::table('sitios_muerte')->insert(['sitio' => 'isste']);
        DB::table('sitios_muerte')->insert(['sitio' => 'semar']);
        DB::table('sitios_muerte')->insert(['sitio' => 'sedena']);
        DB::table('sitios_muerte')->insert(['sitio' => 'otro']);

        /**estado afectado */
        DB::table('estado_afectado')->insert(['estado' => 'recién Fallecido']);
        DB::table('estado_afectado')->insert(['estado' => 'embalsamado']);
        DB::table('estado_afectado')->insert(['estado' => 'en descomposición']);
        DB::table('estado_afectado')->insert(['estado' => 'cenizas']);
        DB::table('estado_afectado')->insert(['estado' => 'restos Aridos']);
        DB::table('estado_afectado')->insert(['estado' => 'por confirmar']);

        /**cementerios servicio */
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Aeternus']);
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Otro cementerio']);
        DB::table('cementerios_servicio')->insert(['cementerio' => 'Fosa Común']);
    }
}