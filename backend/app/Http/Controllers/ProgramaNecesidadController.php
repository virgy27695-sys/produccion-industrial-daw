<?php

namespace App\Http\Controllers;

use App\Models\ProgramaNecesidad;
use Illuminate\Http\Request;

class ProgramaNecesidadController extends Controller
{
    public function index()
    {
        return ProgramaNecesidad::with([
            'cliente',
            'detalles.pieza.molde',
            'detalles.pieza.modelo.cliente',
        ])
        ->orderByDesc('fecha_solicitud')
        ->get();
    }

    public function show(string $id)
    {
        $programa = ProgramaNecesidad::with([
            'cliente',
            'detalles.pieza.molde',
            'detalles.pieza.modelo.cliente',
        ])->findOrFail($id);

        return response()->json($programa);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha_solicitud' => ['required', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'estado' => ['required', 'in:pendiente,aprobado,rechazado'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $programa = ProgramaNecesidad::create($validated);

        return response()->json($programa, 201);
    }

    public function update(Request $request, string $id)
    {
        $programa = ProgramaNecesidad::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'fecha_solicitud' => ['required', 'date'],
            'fecha_entrega' => ['nullable', 'date'],
            'estado' => ['required', 'in:pendiente,aprobado,rechazado'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $programa->update($validated);

        return response()->json($programa);
    }

    public function destroy(string $id)
    {
        $programa = ProgramaNecesidad::findOrFail($id);
        $programa->delete();

        return response()->json([
            'message' => 'Programa eliminado correctamente'
        ]);
    }
}