<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    // LISTAR MODELOS
    public function index()
    {
        return Modelo::with('cliente')
            ->orderBy('nombre')
            ->get();
    }


    // CREAR MODELO
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'cliente_id' => [
                'required',
                'exists:clientes,id',
            ],
        ]);

        $modelo = Modelo::create($data);

        return response()->json(
            $modelo->load('cliente'),
            201
        );
    }


    // MOSTRAR MODELO
    public function show(Modelo $modelo)
    {
        return $modelo->load('cliente');
    }


    // ACTUALIZAR MODELO
    public function update(Request $request, Modelo $modelo)
    {
        $data = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'cliente_id' => [
                'required',
                'exists:clientes,id',
            ],
        ]);

        $modelo->update($data);

        return response()->json(
            $modelo->load('cliente')
        );
    }


    // ELIMINAR MODELO
    public function destroy(Modelo $modelo)
    {
        $modelo->delete();

        return response()->json([
            'message' => 'Modelo eliminado correctamente',
        ]);
    }
}
