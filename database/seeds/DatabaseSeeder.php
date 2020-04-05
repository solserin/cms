<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**sedders de SAT */
        $this->call(SATRegimenesSeeder::class);
        $this->call(SATMonedasSeeder::class);
        $this->call(formasPagoSeeder::class);
        /**sedders de SAT */


        /**sedders de empresa */
        $this->call(FunerariaSeeder::class);
        $this->call(CementerioSeeder::class);
        $this->call(FacturacionSeeder::class);
        $this->call(RegistroPublicoSeeder::class);
        /**fin de sedders de empresa */


        /**seeders de usuarios */
        $this->call(SeccionesSeeder::class);
        $this->call(ModulosSeeder::class);
        $this->call(RolesSeederTable::class);
        factory(User::class, 5)->create();
        /**fin seeders de usuarios */




        //$this->call(MetodosPagoSeeder::class);
        //$this->call(EstadosTableSeeder::class);
        //$this->call(MunicipiosTableSeeder::class);



        //$this->call(SATImpuestosSeeder::class);

        //$this->call(SATProductosServiciosSeeder::class);
        //$this->call(TiposProductoSeeder::class);
        //$this->call(UnidadesSeeder::class);
        //$this->call(AlmacenesSeeder::class);
        //$this->call(CategoriasSeeder::class);
        //$this->call(FamiliasTableSeeder::class);
        //$this->call(GruposProfecoTableSeeder::class);
        // $this->call(PreciosSeeder::class);

        /**seeder de cementerio */
        $this->call(tiposPropiedadesSeeder::class);
        $this->call(PropiedadesSeeder::class);
        $this->call(columnasFilasSeeder::class);
        $this->call(TipoPreciosSeeder::class);
        $this->call(PreciosPropiedadesSeeder::class);
        $this->call(tiposVentaSeeder::class);
        $this->call(ventasReferenciasSeeder::class);
        /**fin de seeder de cementerio */
    }
}