<?php

namespace App\Console\Commands;

// IMPORTS
use App\Models\Molde;
use App\Models\Pieza;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AutogenerarMoldes extends Command
{
    // NOMBRE DEL COMANDO
    protected $signature = 'moldes:autogenerar';

    // DESCRIPCIÓN DEL COMANDO
    protected $description = 'Genera moldes automáticamente agrupando piezas por denominación base';

    public function handle()
    {
        $this->info('Iniciando autogeneración de moldes...');

        // Obtener todas las piezas
        $piezas = Pieza::orderBy('codigo')->get();

        // Agrupar piezas por una denominación base común
        $grupos = $piezas->groupBy(function ($pieza) {
            return $this->obtenerDenominacionBase($pieza->denominacion);
        });

        $contadorMoldes = 0;
        $contadorPiezas = 0;

        foreach ($grupos as $denominacionBase => $piezasGrupo) {
            // Solo generamos molde automático si hay más de una pieza relacionada
            if ($piezasGrupo->count() < 2) {
                continue;
            }

            // Crear un código de molde limpio y único
            $codigoMolde = $this->generarCodigoMolde($denominacionBase);

            // Crear o recuperar el molde si ya existía
            $molde = Molde::firstOrCreate(
                ['codigo' => $codigoMolde],
                [
                    'descripcion' => $denominacionBase,
                    'cavidades' => $piezasGrupo->count(),
                    'tipo_configuracion' => $piezasGrupo->count() > 2
                        ? 'multiple_referencias'
                        : 'izquierda_derecha',
                    'maquina' => null,
                    'tiempo_ciclo_segundos' => null,
                    'stock_seguridad_dias' => 2,
                    'activo' => true,
                ]
            );

            if ($molde->wasRecentlyCreated) {
                $contadorMoldes++;
                $this->line("Molde creado: {$molde->codigo}");
            }

            // Asociar todas las piezas del grupo al molde generado
            foreach ($piezasGrupo as $pieza) {
                $pieza->update([
                    'molde_id' => $molde->id,
                ]);

                $contadorPiezas++;
                $this->line(" - Pieza {$pieza->codigo} asociada a {$molde->codigo}");
            }
        }

        $this->info("Proceso finalizado.");
        $this->info("Moldes creados: {$contadorMoldes}");
        $this->info("Piezas asociadas: {$contadorPiezas}");

        return Command::SUCCESS;
    }

    // OBTENER DENOMINACIÓN BASE
    // Elimina sufijos de lateralidad para poder agrupar piezas del mismo molde.
    // Ejemplo:
    // SOPORTE DRL VW GOLF A8 IZQ -> SOPORTE DRL VW GOLF A8
    // SOPORTE DRL VW GOLF A8 DER -> SOPORTE DRL VW GOLF A8
    private function obtenerDenominacionBase(string $denominacion): string
    {
        $texto = strtoupper(trim($denominacion));

        // Eliminar sufijos claros de lado al final de la denominación
        $texto = preg_replace('/\s+(IZQ|DER|LEFT|RIGHT|I|D|DRC)$/', '', $texto);

        // Limpiar espacios dobles
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim($texto);
    }

    // GENERAR CÓDIGO DE MOLDE
    // Crea un código automático a partir de la denominación base.
    private function generarCodigoMolde(string $denominacionBase): string
    {
        $slug = Str::slug($denominacionBase, '_');

        // Limitar longitud para que el código sea manejable
        $slug = strtoupper(substr($slug, 0, 35));

        return 'MOLDE_' . $slug;
    }
}
