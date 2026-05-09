<?php

namespace App\Http\Controllers;

// IMPORTS
use App\Models\Entrega;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    // LISTAR ENTREGAS
    // Devuelve todas las entregas con la pieza asociada.
    public function index()
    {
        return Entrega::with('pieza')
            ->orderByDesc('fecha')
            ->get();
    }

    // CREAR ENTREGA
    // Registra una cantidad entregada al cliente en una fecha y semana concreta.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pieza_id' => ['required', 'exists:piezas,id'],
            'fecha' => ['required', 'date'],
            'anio' => ['required', 'integer', 'min:2000'],
            'semana' => ['required', 'integer', 'min:1', 'max:53'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $entrega = Entrega::create($validated);

        return response()->json(
            $entrega->load('pieza'),
            201
        );
    }

    // MOSTRAR ENTREGA
    // Devuelve una entrega concreta con su pieza.
    public function show(string $id)
    {
        $entrega = Entrega::with('pieza')->findOrFail($id);

        return response()->json($entrega);
    }

    // ACTUALIZAR ENTREGA
    // Permite corregir una entrega registrada.
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

        $entrega->update($validated);

        return response()->json($entrega->load('pieza'));
    }

    // ELIMINAR ENTREGA
    // Borra un registro de entrega.
    public function destroy(string $id)
    {
        $entrega = Entrega::findOrFail($id);
        $entrega->delete();

        return response()->json([
            'message' => 'Entrega eliminada correctamente'
        ]);
    }
}
