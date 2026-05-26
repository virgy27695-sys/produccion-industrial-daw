<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use App\Models\ProgramaDetalle;
use App\Models\Entrega;
use App\Models\ParteProduccion;

class SituacionController extends Controller
{
    /**
     * SITUACIÓN REAL DE PRODUCCIÓN POR PIEZA
     */
    public function resumen()
    {
        $resultado = [];

        $piezas = Pieza::all();

        foreach ($piezas as $pieza) {

            // NECESIDADES PROGRAMADAS
            $programado = ProgramaDetalle::where(
                'pieza_id',
                $pieza->id
            )->sum('cantidad');


            // FABRICACIÓN VALIDADA
            $fabricado = ParteProduccion::where(
                'pieza_id',
                $pieza->id
            )
                ->where(
                    'estado',
                    'validado'
                )
                ->sum('cantidad_buena');


            // ENTREGAS REALIZADAS
            $entregado = Entrega::where(
                'pieza_id',
                $pieza->id
            )->sum('cantidad');


            // STOCK REAL DISPONIBLE
            $stockActual = (int)(
                $pieza->stock_actual ?? 0
            );


            // STOCK MÍNIMO
            $stockSeguridad = (int)(
                $pieza->stock_seguridad_dias ?? 0
            );


            /*
             * DISPONIBLE REAL
             *
             * stock_actual ya tiene:
             * + producción validada
             * - entregas realizadas
             */
            $disponible = $stockActual;


            /*
             * PENDIENTE
             *
             * Lo que todavía falta entregar
             */
            $pendiente = max(
                $programado - $entregado,
                0
            );


            // SEMÁFORO
            $estado = 'ok';

            if (
                $disponible <=
                $stockSeguridad
            ) {

                $estado = 'critico';
            } elseif (

                $disponible <=
                ($stockSeguridad * 1.5)

            ) {

                $estado = 'medio';
            }


            $resultado[] = [

                'pieza' =>
                $pieza->codigo,

                'denominacion' =>
                $pieza->denominacion,

                'programado' =>
                $programado,

                'fabricado' =>
                $fabricado,

                'entregado' =>
                $entregado,

                'stock_actual' =>
                $stockActual,

                'stock_seguridad' =>
                $stockSeguridad,

                'disponible' =>
                $disponible,

                'pendiente' =>
                $pendiente,

                'estado' =>
                $estado,
            ];
        }

        return response()->json(
            collect($resultado)
                ->values()
        );
    }
}
