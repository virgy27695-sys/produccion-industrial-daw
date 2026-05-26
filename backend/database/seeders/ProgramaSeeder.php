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

        /*
         * Cogemos una pieza por molde.
         * Así evitamos crear necesidades duplicadas por separado
         * y luego generamos automáticamente sus referencias asociadas.
         */
        $piezasBase = Pieza::with('molde')
            ->whereNotNull('molde_id')
            ->get()
            ->unique('molde_id')
            ->values()
            ->take(5);

        if ($piezasBase->isEmpty()) {
            return;
        }

        $anio = now()->year;
        $semana = (int) now()->format('W');

        foreach ($piezasBase as $index => $piezaBase) {

            $programa = $index % 2 === 0
                ? $programaAudi
                : $programaBmw;

            $semanaPrograma =
                $semana + ($index % 4);

            $cantidad =
                500 + ($index * 250);

            /*
             * Si el molde es izquierda/derecha,
             * se crean necesidades para todas las piezas
             * asociadas al mismo molde con la misma cantidad.
             *
             * Ejemplo:
             * Molde M-AUDI-270
             * 90112502 -> 500
             * 90112503 -> 500
             */
            $piezasDelMolde = Pieza::where(
                'molde_id',
                $piezaBase->molde_id
            )->get();

            foreach ($piezasDelMolde as $pieza) {

                ProgramaDetalle::create([
                    'programa_id' => $programa->id,
                    'pieza_id' => $pieza->id,
                    'anio' => $anio,
                    'semana' => $semanaPrograma,
                    'cantidad' => $cantidad,
                    'familia_texto' => $pieza->categoria_funcional ?? 'Faros',
                    'comentarios' => 'Necesidad generada por seeder',
                ]);
            }
        }
    }
}
