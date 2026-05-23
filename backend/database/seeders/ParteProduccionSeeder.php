<?php

namespace Database\Seeders;

use App\Models\ParteProduccion;
use App\Models\Pieza;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParteProduccionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            [
                'email' => 'admin@isavex.com',
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
            ]
        );

        $piezas = Pieza::whereNotNull('molde_id')
            ->take(5)
            ->get();

        foreach ($piezas as $index => $pieza) {
            ParteProduccion::create([
                'user_id' => $user->id,
                'pieza_id' => $pieza->id,
                'molde_id' => $pieza->molde_id,
                'fecha' => now()->toDateString(),
                'turno' => ['mañana', 'tarde', 'noche'][$index % 3],
                'maquina' => 'Inyectora ' . ($index + 1),
                'cantidad_fabricada' => 500 + ($index * 100),
                'cantidad_buena' => 470 + ($index * 95),
                'cantidad_rechazada' => 30 + ($index * 5),
                'minutos_paro' => $index * 10,
                'motivo_paro' => $index % 2 === 0 ? 'Cambio de molde' : null,
                'motivo_rechazo' => $index % 2 === 0 ? 'Rebaba' : null,
                'averia' => $index === 2 ? 'calentadores' : null,
                'estado' => $index < 2 ? 'pendiente' : 'validado',
                'observaciones' => 'Parte generado por seeder',
            ]);
        }
    }
}
