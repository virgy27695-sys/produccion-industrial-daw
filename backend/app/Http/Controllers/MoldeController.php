<?php

namespace App\Http\Controllers;

use App\Models\Molde;
use Illuminate\Http\Request;

class MoldeController extends Controller
{
    public function index()
    {
        return response()->json(
            Molde::with('piezas')->orderBy('codigo')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:moldes,codigo'],
            'descripcion' => ['required', 'string', 'max:255'],
            'cavidades' => ['required', 'integer', 'min:1'],
            'tipo_configuracion' => ['required', 'in:simple,izquierda_derecha,multiple_referencias'],
            'maquina' => ['nullable', 'string', 'max:100'],
            'tiempo_ciclo_segundos' => ['nullable', 'integer', 'min:1'],
            'stock_seguridad_dias' => ['required', 'integer', 'min:0'],
            'activo' => ['required', 'boolean'],
        ]);

        $molde = Molde::create($validated);

        return response()->json($molde, 201);
    }

    public function show(string $id)
    {
        $molde = Molde::with('piezas')->findOrFail($id);

        return response()->json($molde);
    }

    public function update(Request $request, string $id)
    {
        $molde = Molde::findOrFail($id);

        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:moldes,codigo,' . $molde->id],
            'descripcion' => ['required', 'string', 'max:255'],
            'cavidades' => ['required', 'integer', 'min:1'],
            'tipo_configuracion' => ['required', 'in:simple,izquierda_derecha,multiple_referencias'],
            'maquina' => ['nullable', 'string', 'max:100'],
            'tiempo_ciclo_segundos' => ['nullable', 'integer', 'min:1'],
            'stock_seguridad_dias' => ['required', 'integer', 'min:0'],
            'activo' => ['required', 'boolean'],
        ]);

        $molde->update($validated);

        return response()->json($molde);
    }

    public function destroy(string $id)
    {
        $molde = Molde::findOrFail($id);
        $molde->delete();

        return response()->json([
            'message' => 'Molde eliminado correctamente'
        ]);
    }
}