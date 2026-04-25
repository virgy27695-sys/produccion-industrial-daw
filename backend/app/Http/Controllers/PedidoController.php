<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with([
            'programa.cliente',
            'detalles.pieza.molde'
        ])->latest()->get();

        return response()->json($pedidos);
    }

    public function show(string $id)
    {
        $pedido = Pedido::with([
            'programa.cliente',
            'detalles.pieza.molde'
        ])->findOrFail($id);

        return response()->json($pedido);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas_necesidades,id'],
            'fecha_pedido' => ['nullable', 'date'],
            'estado' => ['required', 'in:pendiente,produccion,enviado,entregado'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.pieza_id' => ['required', 'exists:piezas,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $pedido = DB::transaction(function () use ($validated) {
            $pedido = Pedido::create([
                'programa_id' => $validated['programa_id'],
                'fecha_pedido' => $validated['fecha_pedido'] ?? null,
                'estado' => $validated['estado'],
            ]);

            foreach ($validated['detalles'] as $detalle) {
                $pedido->detalles()->create([
                    'pieza_id' => $detalle['pieza_id'],
                    'cantidad' => $detalle['cantidad'],
                ]);
            }

            return $pedido->load([
                'programa.cliente',
                'detalles.pieza.molde'
            ]);
        });

        return response()->json($pedido, 201);
    }

    public function update(Request $request, string $id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($id);

        $validated = $request->validate([
            'programa_id' => ['required', 'exists:programas_necesidades,id'],
            'fecha_pedido' => ['nullable', 'date'],
            'estado' => ['required', 'in:pendiente,produccion,enviado,entregado'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.pieza_id' => ['required', 'exists:piezas,id'],
            'detalles.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $pedido = DB::transaction(function () use ($pedido, $validated) {
            $pedido->update([
                'programa_id' => $validated['programa_id'],
                'fecha_pedido' => $validated['fecha_pedido'] ?? null,
                'estado' => $validated['estado'],
            ]);

            $pedido->detalles()->delete();

            foreach ($validated['detalles'] as $detalle) {
                $pedido->detalles()->create([
                    'pieza_id' => $detalle['pieza_id'],
                    'cantidad' => $detalle['cantidad'],
                ]);
            }

            return $pedido->load([
                'programa.cliente',
                'detalles.pieza.molde'
            ]);
        });

        return response()->json($pedido);
    }

    public function destroy(string $id)
    {
        $pedido = Pedido::findOrFail($id);

        DB::transaction(function () use ($pedido) {
            $pedido->detalles()->delete();
            $pedido->delete();
        });

        return response()->json([
            'message' => 'Pedido eliminado correctamente'
        ]);
    }
}