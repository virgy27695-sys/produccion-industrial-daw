<?php

namespace App\Http\Controllers;

use App\Models\Pieza;
use Illuminate\Http\Request;

class PiezaController extends Controller
{
    public function index()
    {
        return Pieza::with(['modelo.cliente', 'molde'])
            ->orderBy('codigo')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:piezas,codigo'],
            'denominacion' => ['required', 'string', 'max:255'],
            'modelo_id' => ['required', 'exists:modelos,id'],
            'molde_id' => ['nullable', 'exists:moldes,id'],
            'lado_pieza' => ['nullable', 'in:izquierda,derecha,neutra'],
            'mercado' => ['nullable', 'string', 'max:20'],
            'categoria_funcional' => ['nullable', 'string', 'max:50'],
        ]);

        $pieza = Pieza::create($validated);

        return response()->json($pieza, 201);
    }

    public function show(Pieza $pieza)
    {
        return $pieza->load(['modelo.cliente', 'molde']);
    }

    public function update(Request $request, Pieza $pieza)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:piezas,codigo,' . $pieza->id],
            'denominacion' => ['required', 'string', 'max:255'],
            'modelo_id' => ['required', 'exists:modelos,id'],
            'molde_id' => ['nullable', 'exists:moldes,id'],
            'lado_pieza' => ['nullable', 'in:izquierda,derecha,neutra'],
            'mercado' => ['nullable', 'string', 'max:20'],
            'categoria_funcional' => ['nullable', 'string', 'max:50'],
        ]);

        $pieza->update($validated);

        return response()->json($pieza);
    }

    public function destroy(Pieza $pieza)
    {
        $pieza->delete();

        return response()->json([
            'message' => 'Pieza eliminada'
        ]);
    }
}
