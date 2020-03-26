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
        $this->call(SeccionesSeeder::class);
        $this->call(ModulosSeeder::class);
        $this->call(MetodosPagoSeeder::class);
        $this->call(RolesSeederTable::class);
        $this->call(EstadosTableSeeder::class);
        $this->call(MunicipiosTableSeeder::class);
        $this->call(SATMonedasSeeder::class);
        $this->call(SATRegimenesSeeder::class);
        $this->call(SATImpuestosSeeder::class);
        $this->call(tiposPropiedadesSeeder::class);
        $this->call(PropiedadesSeeder::class);
        $this->call(columnasFilasSeeder::class);
        $this->call(SATProductosServiciosSeeder::class);
        $this->call(TiposProductoSeeder::class);
        $this->call(UnidadesSeeder::class);
        $this->call(AlmacenesSeeder::class);
        $this->call(CategoriasSeeder::class);
        $this->call(FamiliasTableSeeder::class);
        $this->call(GruposProfecoTableSeeder::class);
        $this->call(TipoPreciosSeeder::class);
        factory(User::class, 30)->create();
        $this->call(PreciosPropiedadesSeeder::class);
        $this->call(PreciosSeeder::class);
    }
}