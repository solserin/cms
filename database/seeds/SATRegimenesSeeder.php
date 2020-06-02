<?php

use Illuminate\Database\Seeder;

class SATRegimenesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sat_regimenes')->insert(['id' => 601, 'regimen' => 'General de Ley Personas Morales']);
        DB::table('sat_regimenes')->insert(['id' => 603, 'regimen' => 'Personas Morales con Fines no Lucrativos']);
        DB::table('sat_regimenes')->insert(['id' => 605, 'regimen' => 'Sueldos y Salarios e Ingresos Asimilados a Salarios']);
        DB::table('sat_regimenes')->insert(['id' => 606, 'regimen' => 'Arrendamiento']);
        DB::table('sat_regimenes')->insert(['id' => 608, 'regimen' => 'Demás ingresos']);
        DB::table('sat_regimenes')->insert(['id' => 609, 'regimen' => 'Consolidación']);
        DB::table('sat_regimenes')->insert(['id' => 610, 'regimen' => 'Residentes en el Extranjero sin Establecimiento Permanente en México']);
        DB::table('sat_regimenes')->insert(['id' => 611, 'regimen' => 'Ingresos por Dividendos (socios y accionistas)']);
        DB::table('sat_regimenes')->insert(['id' => 612, 'regimen' => 'Personas Físicas con Actividades Empresariales y Profesionales']);
        DB::table('sat_regimenes')->insert(['id' => 614, 'regimen' => 'Ingresos por intereses']);
        DB::table('sat_regimenes')->insert(['id' => 616, 'regimen' => 'Sin obligaciones fiscales']);
        DB::table('sat_regimenes')->insert(['id' => 620, 'regimen' => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos']);
        DB::table('sat_regimenes')->insert(['id' => 621, 'regimen' => 'Incorporación Fiscal']);
        DB::table('sat_regimenes')->insert(['id' => 622, 'regimen' => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras']);
        DB::table('sat_regimenes')->insert(['id' => 623, 'regimen' => 'Opcional para Grupos de Sociedades']);
        DB::table('sat_regimenes')->insert(['id' => 624, 'regimen' => 'Coordinados']);
        DB::table('sat_regimenes')->insert(['id' => 628, 'regimen' => 'Hidrocarburos']);
        DB::table('sat_regimenes')->insert(['id' => 607, 'regimen' => 'Régimen de Enajenación o Adquisición de Bienes']);
        DB::table('sat_regimenes')->insert(['id' => 629, 'regimen' => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales']);
        DB::table('sat_regimenes')->insert(['id' => 630, 'regimen' => 'Enajenación de acciones en bolsa de valores']);
        DB::table('sat_regimenes')->insert(['id' => 615, 'regimen' => 'Régimen de los ingresos por obtención de premios']);
    }
}