<?php

use App\TipoPagos;
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
        //$this->call(SATMonedasSeeder::class);
        //$this->call(formasPagoSeeder::class);
        /**sedders de SAT */


        /**sedders de empresa */
        $this->call(FunerariaSeeder::class);
        $this->call(CementerioSeeder::class);
        $this->call(FacturacionSeeder::class);
        $this->call(RegistroPublicoSeeder::class);
        /**fin de sedders de empresa */


        /**seeders de usuarios */
        $this->call(ModulosSeeder::class);
        $this->call(RolesSeederTable::class);
        $this->call(UsuariosSeeder::class);
        //factory(User::class, 5)->create();
        /**fin seeders de usuarios */

        /**
         * clientes
         */
        $this->call(GenerosSeeder::class);
        $this->call(NacionalidadesSeeder::class);
        $this->call(ClientesSeeder::class);


        /**seeders para el control de ventas del cementerio y pagos */
        $this->call(ConceptosPagosSeeder::class);
        $this->call(MotivosCancelacionSeeder::class);
        $this->call(TipoPagosSeeder::class);
        /**seeder de cementerio */
        //$this->call(AjustesInteresesPropiedadesSeeder::class);
        //$this->call(tiposPropiedadesSeeder::class);
        //$this->call(PropiedadesSeeder::class);
        //$this->call(columnasFilasSeeder::class);
        //$this->call(TipoPreciosSeeder::class);
        //$this->call(PreciosPropiedadesSeeder::class);
        //$this->call(ventasReferenciasSeeder::class);
        /**pagos */
        //
        //$this->call(antiguedadVentasSeeder::class);
        //$this->call(AjustesSeeder::class);
        /**fin de seeder de cementerio */











        /**pendiente, hechas por andres */

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


    }
}