<?php

namespace App\Http\Controllers;

use App\Models\ProgramaDetalle;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    /**
     * PLANIFICACIÓN DE PRODUCCIÓN POR MOLDE
     *
     * Agrupa las necesidades de producción por:
     * - molde
     * - año
     * - semana
     *
     * Calcula:
     * - total de piezas
     * - cavidades del molde
     * - ciclos necesarios (piezas / cavidades)
     */
    public function resumen()
    {
        // Cargar detalles con relaciones necesarias
        $detalles = ProgramaDetalle::with('pieza.molde')->get();

        // Agrupar por molde + semana
        $agrupado = $detalles->groupBy(function ($item) {
            return $item->pieza->molde_id . '_' . $item->anio . '_' . $item->semana;
        });

        $resultado = [];

        foreach ($agrupado as $grupo) {

            $primer = $grupo->first();

            $molde = $primer->pieza->molde;

            // Si la pieza no tiene molde, se ignora
            if (!$molde) continue;

            $totalPiezas = $grupo->sum('cantidad');

            $cavidades = $molde->cavidades ?: 1;

            // Cálculo de ciclos necesarios
            $ciclos = ceil($totalPiezas / $cavidades);

            $resultado[] = [
                'molde_codigo' => $molde->codigo,
                'descripcion' => $molde->descripcion,
                'anio' => $primer->anio,
                'semana' => $primer->semana,
                'total_piezas' => $totalPiezas,
                'cavidades' => $cavidades,
                'ciclos_necesarios' => $ciclos,
            ];
        }

        return response()->json($resultado);
    }
}
