<?php

namespace App\Http\Controllers;

use App\Models\ProgramaDetalle;
use Illuminate\Http\Request;

class ProgramaDetalleController extends Controller
{
    public function index()
    {
        return ProgramaDetalle::with([
            'programa.cliente',
            'pieza.molde',
            'pieza.modelo.cliente',
        ])
            ->orderByDesc('anio')
            ->orderByDesc('semana')
            ->get();
    }

    public function show(string $id)
    {
        $detalle = ProgramaDetalle::with([
            'programa.cliente',
            'pieza.molde',
            'pieza.modelo.cliente',
        ])->findOrFail($id);

        return response()->json($detalle);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas_necesidades,id'],
            'pieza_id' => ['required', 'exists:piezas,id'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'familia_texto' => ['nullable', 'string', 'max:100'],
            'comentarios' => ['nullable', 'string'],
        ]);

        $detalle = ProgramaDetalle::create($validated);

        return response()->json($detalle, 201);
    }

    public function update(Request $request, string $id)
    {
        $detalle = ProgramaDetalle::findOrFail($id);

        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas_necesidades,id'],
            'pieza_id' => ['required', 'exists:piezas,id'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2100'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:0'],
            'familia_texto' => ['nullable', 'string', 'max:100'],
            'comentarios' => ['nullable', 'string'],
        ]);

        $detalle->update($validated);

        return response()->json($detalle);
    }

    public function destroy(string $id)
    {
        $detalle = ProgramaDetalle::findOrFail($id);
        $detalle->delete();

        return response()->json([
            'message' => 'Detalle de programa eliminado correctamente'
        ]);
    }
}
