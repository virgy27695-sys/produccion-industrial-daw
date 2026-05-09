<?php

namespace App\Http\Controllers;

// IMPORTS
use App\Models\Pieza;
use App\Models\ProgramaDetalle;
use App\Models\Fabricacion;
use App\Models\Entrega;

class PlanningController extends Controller
{
    /**
     * PLANNING SEMANAL INDUSTRIAL
     *
     * Calcula:
     * - stock actual
     * - cobertura en días
     * - programa semanal
     * - fabricado
     * - entregado
     * - disponible real
     * - faltante
     * - semáforo
     *
     * Inspirado en el planning Excel real utilizado en planta.
     */
    public function semanal()
    {
        $resultado = [];

        // SEMANA Y AÑO DE CONSULTA
        // Si no se envían parámetros, se usa la semana y año actuales.
        $semanaActual = (int) request()->query('semana', date('W'));
        $anioActual = (int) request()->query('anio', date('Y'));

        // TODAS LAS PIEZAS
        // Se cargan también modelo y molde para mostrar información productiva.
        $piezas = Pieza::with([
            'molde',
            'modelo',
        ])
            ->orderBy('codigo')
            ->get();

        foreach ($piezas as $pieza) {

            // -------------------------
            // PROGRAMA SEMANA ACTUAL
            // -------------------------
            $programaSemana = (int) ProgramaDetalle::where('pieza_id', $pieza->id)
                ->where('anio', $anioActual)
                ->where('semana', $semanaActual)
                ->sum('cantidad');


            // -------------------------
            // FABRICADO SEMANA ACTUAL
            // -------------------------
            $fabricado = (int) Fabricacion::where('pieza_id', $pieza->id)
                ->where('anio', $anioActual)
                ->where('semana', $semanaActual)
                ->sum('cantidad');


            // -------------------------
            // ENTREGADO SEMANA ACTUAL
            // -------------------------
            $entregado = (int) Entrega::where('pieza_id', $pieza->id)
                ->where('anio', $anioActual)
                ->where('semana', $semanaActual)
                ->sum('cantidad');


            // -------------------------
            // STOCK ACTUAL
            // -------------------------
            $stockActual = (int) ($pieza->stock_actual ?? 0);


            // -------------------------
            // CONSUMO DIARIO
            // -------------------------
            // Se asume una semana productiva de 5 días.
            $consumoDiario = $programaSemana > 0
                ? ($programaSemana / 5)
                : 0;


            // -------------------------
            // COBERTURA EN DÍAS
            // -------------------------
            // Indica cuántos días puede cubrir el stock actual.
            $coberturaDias = $consumoDiario > 0
                ? ($stockActual / $consumoDiario)
                : 0;


            // -------------------------
            // DISPONIBLE REAL
            // -------------------------
            // Stock + fabricado - entregado.
            $disponible = $stockActual + $fabricado - $entregado;


            // -------------------------
            // FALTANTE PRINCIPAL
            // -------------------------
            // Si sale negativo, significa que hay excedente, por eso se muestra 0.
            $faltante = max($programaSemana - $disponible, 0);


            // -------------------------
            // SEMÁFORO
            // -------------------------
            // Sin programa: no hay necesidad cargada para esa semana.
            // Crítico: cobertura inferior a 3 días.
            // Medio: cobertura entre 3 y 5 días.
            // OK: cobertura superior a 5 días.
            $estado = 'sin_programa';

            if ($programaSemana > 0) {
                if ($coberturaDias < 3) {
                    $estado = 'critico';
                } elseif ($coberturaDias < 5) {
                    $estado = 'medio';
                } else {
                    $estado = 'ok';
                }
            }


            // -------------------------
            // SEMANAS FUTURAS
            // -------------------------
            // Se muestran 4 semanas: actual + 3 siguientes.
            $semanas = [];

            for ($i = 0; $i < 4; $i++) {
                $semanaConsulta = $this->calcularSemana($semanaActual, $i);
                $anioConsulta = $this->calcularAnio($anioActual, $semanaActual, $i);

                $programa = (int) ProgramaDetalle::where('pieza_id', $pieza->id)
                    ->where('anio', $anioConsulta)
                    ->where('semana', $semanaConsulta)
                    ->sum('cantidad');

                $faltanteSemana = $programa - $disponible;

                $semanas[] = [
                    'anio' => $anioConsulta,
                    'semana' => $semanaConsulta,
                    'programa' => $programa,
                    'faltante' => $faltanteSemana,
                ];
            }


            // -------------------------
            // RESULTADO FINAL
            // -------------------------
            $resultado[] = [
                'pieza_id' => $pieza->id,
                'codigo' => $pieza->codigo,
                'denominacion' => $pieza->denominacion,
                'modelo' => $pieza->modelo?->nombre,
                'molde' => $pieza->molde?->codigo,

                'stock_actual' => $stockActual,
                'cobertura_dias' => round($coberturaDias, 1),
                'programa_semana' => $programaSemana,
                'fabricado' => $fabricado,
                'entregado' => $entregado,
                'disponible' => $disponible,
                'faltante' => $faltante,
                'estado' => $estado,

                'semanas' => $semanas,
            ];
        }

        return response()->json($resultado);
    }


    // CALCULAR SEMANA FUTURA
    // Si se supera la semana 53, vuelve a la semana 1.
    private function calcularSemana(int $semanaActual, int $incremento): int
    {
        $semana = $semanaActual + $incremento;

        if ($semana > 53) {
            return $semana - 53;
        }

        return $semana;
    }


    // CALCULAR AÑO FUTURO
    // Si se pasa de la semana 53, aumenta el año.
    private function calcularAnio(int $anioActual, int $semanaActual, int $incremento): int
    {
        $semana = $semanaActual + $incremento;

        if ($semana > 53) {
            return $anioActual + 1;
        }

        return $anioActual;
    }
}
