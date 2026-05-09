<?php

namespace App\Http\Controllers;

// IMPORTS
use App\Models\Fabricacion;
use Illuminate\Http\Request;

class FabricacionController extends Controller
{
    // LISTAR FABRICACIONES
    // Devuelve todos los registros de fabricación con pieza y molde asociados.
    public function index()
    {
        return Fabricacion::with(['pieza', 'molde'])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();
    }

    // CREAR FABRICACIÓN
    // Registra una cantidad fabricada de una pieza en una fecha, semana y turno concreto.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pieza_id' => ['required', 'exists:piezas,id'],
            'molde_id' => ['nullable', 'exists:moldes,id'],
            'fecha' => ['required', 'date'],
            'turno' => ['required', 'in:mañana,tarde,noche'],
            'anio' => ['required', 'integer', 'min:2000'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $fabricacion = Fabricacion::create($validated);

        return response()->json(
            $fabricacion->load(['pieza', 'molde']),
            201
        );
    }

    // MOSTRAR FABRICACIÓN
    // Devuelve un registro concreto con su pieza y molde.
    public function show(string $id)
    {
        $fabricacion = Fabricacion::with(['pieza', 'molde'])->findOrFail($id);

        return response()->json($fabricacion);
    }

    // ACTUALIZAR FABRICACIÓN
    // Permite corregir una fabricación registrada.
    public function update(Request $request, string $id)
    {
        $fabricacion = Fabricacion::findOrFail($id);

        $validated = $request->validate([
            'pieza_id' => ['required', 'exists:piezas,id'],
            'molde_id' => ['nullable', 'exists:moldes,id'],
            'fecha' => ['required', 'date'],
            'turno' => ['required', 'in:mañana,tarde,noche'],
            'anio' => ['required', 'integer', 'min:2000'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $fabricacion->update($validated);

        return response()->json($fabricacion->load(['pieza', 'molde']));
    }

    // ELIMINAR FABRICACIÓN
    // Borra un registro de fabricación.
    public function destroy(string $id)
    {
        $fabricacion = Fabricacion::findOrFail($id);
        $fabricacion->delete();

        return response()->json([
            'message' => 'Fabricación eliminada correctamente'
        ]);
    }
}
