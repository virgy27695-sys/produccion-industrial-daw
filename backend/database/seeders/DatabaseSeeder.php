<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([

            ClienteSeeder::class,

            ModeloSeeder::class,

            // Crear moldes antes de piezas
            MoldeSeeder::class,

            PiezaSeeder::class,

            ProgramaSeeder::class,

            // Datos productivos para dashboard
            ParteProduccionSeeder::class,

            PedidoSeeder::class,
        ]);
    }
}
