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
        $this->call(RolesSeederTable::class);
        factory(User::class, 250)->create();
    }
}
