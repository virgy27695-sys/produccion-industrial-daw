<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Molde;
use App\Models\ParteProduccion;
use App\Models\Pedido;

class DashboardController extends Controller
{
    public function resumen()
    {
        $hoy = now()->toDateString();

        $partesHoy = ParteProduccion::whereDate('fecha', $hoy)->get();

        $fabricadasHoy = $partesHoy->sum('cantidad_fabricada');
        $rechazadasHoy = $partesHoy->sum('cantidad_rechazada');

        $eficiencia = $fabricadasHoy > 0
            ? round((($fabricadasHoy - $rechazadasHoy) / $fabricadasHoy) * 100, 2)
            : 0;

        return response()->json([
            'kpis' => [
                'fabricadas_hoy' => $fabricadasHoy,
                'rechazadas_hoy' => $rechazadasHoy,
                'eficiencia' => $eficiencia,
                'moldes_activos' => Molde::where('activo', true)->count(),
                'entregas_pendientes' => Entrega::count(),
                'partes_pendientes' => ParteProduccion::where('estado', 'pendiente')->count(),
            ],

            'ultimos_partes' => ParteProduccion::with([
                'pieza',
                'molde',
                'user',
            ])
                ->latest()
                ->take(5)
                ->get(),

            'ultimos_pedidos' => Pedido::with([
                'programa.cliente',
            ])
                ->latest()
                ->take(5)
                ->get(),

            'ultimas_entregas' => Entrega::with([
                'pieza',
            ])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
