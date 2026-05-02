<?php

namespace App\Http\Controllers;

// IMPORTS
use App\Models\ProgramaNecesidad;
use App\Imports\ProgramaImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProgramaNecesidadController extends Controller
{
    // LISTAR PROGRAMAS
    // Devuelve los programas con cliente, detalles, piezas, moldes y modelos.
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

    // MOSTRAR UN PROGRAMA
    // Devuelve un programa concreto con todo su detalle productivo.
    public function show(string $id)
    {
        $programa = ProgramaNecesidad::with([
            'cliente',
            'detalles.pieza.molde',
            'detalles.pieza.modelo.cliente',
        ])->findOrFail($id);

        return response()->json($programa);
    }

    // CREAR PROGRAMA
    // Crea la cabecera del programa de necesidades.
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

    // ACTUALIZAR PROGRAMA
    // Modifica los datos principales de la cabecera del programa.
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

    // IMPORTAR PROGRAMA DESDE EXCEL
    // Actualiza las semanas del programa usando referencias ya existentes.
    // Si una referencia no existe en el maestro de piezas, se ignora para evitar crear datos incorrectos.
    public function importar(Request $request, string $id)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        Excel::import(new ProgramaImport((int) $id), $request->file('file'));

        return response()->json([
            'message' => 'Programa importado correctamente'
        ]);
    }

    // ELIMINAR PROGRAMA
    // Borra el programa y sus detalles asociados por la relación en base de datos.
    public function destroy(string $id)
    {
        $programa = ProgramaNecesidad::findOrFail($id);
        $programa->delete();

        return response()->json([
            'message' => 'Programa eliminado correctamente'
        ]);
    }
}
