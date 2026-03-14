<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::insert([
            ['nombre' => 'Audi'],
            ['nombre' => 'BMW'],
            ['nombre' => 'Volkswagen'],
            ['nombre' => 'Seat'],
            ['nombre' => 'Renault'],
            ['nombre' => 'Ford'],
            ['nombre' => 'Opel'],
            ['nombre' => 'Bugatti'],
        ]);
    }
}