<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\Pieza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntregaController extends Controller
{
    public function index()
    {
        return Entrega::with('pieza')
            ->orderByDesc('fecha')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pieza_id' => ['required', 'exists:piezas,id'],
            'fecha' => ['required', 'date'],
            'anio' => ['required', 'integer', 'min:2000'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $entrega = DB::transaction(function () use ($validated) {
            $pieza = Pieza::findOrFail($validated['pieza_id']);

            if ($pieza->stock_actual < $validated['cantidad']) {
                abort(422, 'No hay stock suficiente para registrar la entrega.');
            }

            $pieza->decrement(
                'stock_actual',
                $validated['cantidad']
            );

            return Entrega::create($validated);
        });

        return response()->json(
            $entrega->load('pieza'),
            201
        );
    }

    public function show(string $id)
    {
        $entrega = Entrega::with('pieza')->findOrFail($id);

        return response()->json($entrega);
    }

    public function update(Request $request, string $id)
    {
        $entrega = Entrega::findOrFail($id);

        $validated = $request->validate([
            'pieza_id' => ['required', 'exists:piezas,id'],
            'fecha' => ['required', 'date'],
            'anio' => ['required', 'integer', 'min:2000'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($entrega, $validated) {
            $piezaAnterior = Pieza::findOrFail($entrega->pieza_id);

            $piezaAnterior->increment(
                'stock_actual',
                $entrega->cantidad
            );

            $piezaNueva = Pieza::findOrFail($validated['pieza_id']);

            if ($piezaNueva->stock_actual < $validated['cantidad']) {
                abort(422, 'No hay stock suficiente para actualizar la entrega.');
            }

            $piezaNueva->decrement(
                'stock_actual',
                $validated['cantidad']
            );

            $entrega->update($validated);
        });

        return response()->json(
            $entrega->fresh()->load('pieza')
        );
    }

    public function destroy(string $id)
    {
        $entrega = Entrega::findOrFail($id);

        DB::transaction(function () use ($entrega) {
            $entrega
                ->pieza()
                ->increment(
                    'stock_actual',
                    $entrega->cantidad
                );

            $entrega->delete();
        });

        return response()->json([
            'message' => 'Entrega eliminada correctamente'
        ]);
    }
}
