<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use App\Models\ProgramaDetalle;
use App\Models\Entrega;
use App\Models\Fabricacion;

class SituacionController extends Controller
{
    /**
     * SITUACIÓN REAL DE PRODUCCIÓN POR PIEZA
     *
     * Calcula:
     * - necesidades del cliente
     * - producción
     * - entregas
     * - stock
     * - estado (semáforo)
     */
    public function resumen()
    {
        $resultado = [];

        // Obtener todas las piezas
        $piezas = Pieza::all();

        foreach ($piezas as $pieza) {

            // --------------------------
            // 1. PROGRAMA SEMANA ACTUAL
            // --------------------------

            // Aquí simplificamos usando TODAS las semanas
            // luego podemos filtrar semana actual
            $programado = ProgramaDetalle::where('pieza_id', $pieza->id)
                ->sum('cantidad');


            // --------------------------
            // 2. FABRICADO
            // --------------------------

            $fabricado = Fabricacion::where('pieza_id', $pieza->id)
                ->sum('cantidad');


            // --------------------------
            // 3. ENTREGADO
            // --------------------------

            $entregado = Entrega::where('pieza_id', $pieza->id)
                ->sum('cantidad');


            // --------------------------
            // 4. STOCK
            // --------------------------

            $stock = $pieza->stock_actual ?? 0;


            // --------------------------
            // 5. CONSUMO DIARIO
            // --------------------------

            // Suponemos semana de 5 días
            $consumoDiario = $programado > 0 ? ($programado / 5) : 0;


            // --------------------------
            // 6. STOCK SEGURIDAD (3 días)
            // --------------------------

            $stockSeguridad = $consumoDiario * $pieza->stock_seguridad_dias;


            // --------------------------
            // 7. DISPONIBLE REAL
            // --------------------------

            $disponible = $stock + $fabricado - $entregado;


            // --------------------------
            // 8. PENDIENTE
            // --------------------------

            $pendiente = $programado - $entregado;


            // --------------------------
            // 9. SEMÁFORO
            // --------------------------

            $estado = 'ok';

            if ($disponible < $stockSeguridad) {
                $estado = 'critico';
            } elseif ($disponible < ($stockSeguridad * 1.2)) {
                $estado = 'medio';
            }


            $resultado[] = [
                'pieza' => $pieza->codigo,
                'denominacion' => $pieza->denominacion,

                'programado' => $programado,
                'fabricado' => $fabricado,
                'entregado' => $entregado,

                'stock_actual' => $stock,
                'stock_seguridad' => round($stockSeguridad),

                'disponible' => $disponible,
                'pendiente' => $pendiente,

                'estado' => $estado,
            ];
        }

        return response()->json($resultado);
    }
}
