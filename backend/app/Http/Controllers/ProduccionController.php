<?php

namespace App\Http\Controllers;

use App\Models\ProgramaDetalle;

class ProduccionController extends Controller
{
    /**
     * PLANIFICACIÓN DE PRODUCCIÓN POR MOLDE
     *
     * Agrupa necesidades por:
     * - Molde
     * - Año
     * - Semana
     *
     * Un molde izquierda/derecha aparece una sola vez,
     * con sus referencias agrupadas.
     */
    public function resumen()
    {
        $detalles = ProgramaDetalle::with([
            'pieza.molde',
            'pieza.modelo',
        ])
            ->get()
            ->filter(function ($item) {
                return
                    $item->pieza &&
                    $item->pieza->molde;
            });

        $agrupado = $detalles->groupBy(function ($item) {
            return implode('_', [
                $item->pieza->molde_id,
                $item->anio,
                $item->semana,
            ]);
        });

        $resultado = [];

        foreach ($agrupado as $grupo) {

            $primer =
                $grupo->first();

            $molde =
                $primer->pieza->molde;

            $referencias = $grupo
                ->pluck('pieza.codigo')
                ->filter()
                ->unique()
                ->values();

            $modelos = $grupo
                ->pluck('pieza.modelo.nombre')
                ->filter()
                ->unique()
                ->values();

            /*
             * Lógica industrial:
             *
             * Si el mismo molde tiene varias referencias en la misma semana,
             * por ejemplo izquierda/derecha, se fabrica el conjunto en el
             * mismo ciclo.
             *
             * Por eso no se suman ambas cantidades para calcular ciclos.
             * Se toma la mayor necesidad del grupo.
             */
            $cantidadPrevista =
                $referencias->count() > 1
                ? $grupo->max('cantidad')
                : $grupo->sum('cantidad');

            $cavidades =
                $molde->cavidades ?: 1;

            $ciclos =
                ceil(
                    $cantidadPrevista /
                        $cavidades
                );

            $resultado[] = [
                'molde_codigo' =>
                $molde->codigo,

                'descripcion' =>
                $molde->descripcion,

                'referencias' =>
                $referencias->implode(' / '),

                'modelo' =>
                $modelos->implode(' / '),

                'anio' =>
                $primer->anio,

                'semana' =>
                $primer->semana,

                'cantidad_prevista' =>
                $cantidadPrevista,

                'cavidades' =>
                $cavidades,

                'ciclos_necesarios' =>
                $ciclos,
            ];
        }

        return response()->json(
            collect($resultado)
                ->sortBy([
                    ['anio', 'asc'],
                    ['semana', 'asc'],
                    ['molde_codigo', 'asc'],
                ])
                ->values()
        );
    }
}
