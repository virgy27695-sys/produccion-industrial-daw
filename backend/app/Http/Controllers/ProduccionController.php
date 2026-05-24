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
     * Devuelve:
     * - código molde
     * - referencias asociadas
     * - cantidad prevista
     * - cavidades
     * - ciclos estimados
     */
    public function resumen()
    {
        // Cargar relaciones
        $detalles = ProgramaDetalle::with([
            'pieza.molde',
            'pieza.modelo',
        ])->get();

        // Agrupar por molde + año + semana
        $agrupado = $detalles->groupBy(function ($item) {

            return
                $item->pieza->molde_id .
                '_' .
                $item->anio .
                '_' .
                $item->semana;
        });

        $resultado = [];

        foreach ($agrupado as $grupo) {

            $primer = $grupo->first();

            $molde = $primer->pieza?->molde;

            // Ignorar piezas sin molde
            if (!$molde) {
                continue;
            }

            // Referencias asociadas al molde
            $referencias = $grupo
                ->pluck('pieza.codigo')
                ->unique()
                ->implode(' / ');

            // Modelos asociados
            $modelos = $grupo
                ->pluck('pieza.modelo.nombre')
                ->filter()
                ->unique()
                ->implode(' / ');

            /*
             * Para moldes izquierda/derecha:
             * el molde genera un conjunto por ciclo
             * así que usamos la mayor cantidad
             *
             * Para el resto:
             * usamos la suma normal
             */

            if ($molde->tipo_configuracion === 'izquierda_derecha') {

                $cantidadPrevista =
                    $grupo->max('cantidad');
            } else {

                $cantidadPrevista =
                    $grupo->sum('cantidad');
            }

            $cavidades = $molde->cavidades ?: 1;

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
                $referencias,

                'modelo' =>
                $modelos,

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
                ])
                ->values()
        );
    }
}
