<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Pieza;
use App\Models\ProgramaDetalle;
use App\Models\ProgramaNecesidad;
use Illuminate\Database\Seeder;

class ProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $audi = Cliente::where('nombre', 'Audi')->first();
        $bmw = Cliente::where('nombre', 'BMW')->first();

        if (!$audi || !$bmw) {
            return;
        }

        $programaAudi = ProgramaNecesidad::create([
            'cliente_id' => $audi->id,
            'fecha_solicitud' => now(),
            'fecha_entrega' => now()->addDays(30),
            'estado' => 'pendiente',
            'observaciones' => 'Producción inicial',
        ]);

        $programaBmw = ProgramaNecesidad::create([
            'cliente_id' => $bmw->id,
            'fecha_solicitud' => now(),
            'fecha_entrega' => now()->addDays(45),
            'estado' => 'pendiente',
            'observaciones' => 'Programa trimestral',
        ]);

        $piezas = Pieza::take(6)->get();

        if ($piezas->isEmpty()) {
            return;
        }

        $anio = now()->year;
        $semana = (int) now()->format('W');

        foreach ($piezas as $index => $pieza) {
            ProgramaDetalle::create([
                'programa_id' => $index % 2 === 0
                    ? $programaAudi->id
                    : $programaBmw->id,

                'pieza_id' => $pieza->id,
                'anio' => $anio,
                'semana' => $semana + ($index % 4),
                'cantidad' => 500 + ($index * 250),
                'familia_texto' => $pieza->categoria_funcional ?? 'Faros',
                'comentarios' => 'Necesidad generada por seeder',
            ]);
        }
    }
}
