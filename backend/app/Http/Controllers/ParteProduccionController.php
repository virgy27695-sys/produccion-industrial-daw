<?php

namespace App\Http\Controllers;

use App\Models\ParteProduccion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ParteProduccionController extends Controller
{
    /**
     * LISTAR PARTES
     */
    public function index()
    {
        return ParteProduccion::with([
            'user',
            'pieza',
            'molde',
        ])
            ->latest()
            ->get();
    }


    /**
     * CREAR PARTE
     */
    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);

        $parte = ParteProduccion::create($data);

        return response()->json(
            $parte->load([
                'user',
                'pieza',
                'molde',
            ]),
            201
        );
    }


    /**
     * MOSTRAR PARTE
     */
    public function show(
        ParteProduccion $partesProduccion
    ) {
        return $partesProduccion->load([
            'user',
            'pieza',
            'molde',
        ]);
    }


    /**
     * ACTUALIZAR PARTE
     *
     * Solo datos operativos
     */
    public function update(
        Request $request,
        ParteProduccion $partesProduccion
    ) {
        $data = $this->validateData(
            $request,
            false
        );

        // seguridad adicional
        unset($data['estado']);

        $partesProduccion->update(
            $data
        );

        return $partesProduccion->load([
            'user',
            'pieza',
            'molde',
        ]);
    }


    /**
     * VALIDAR PRODUCCIÓN
     */
    public function validar(
        Request $request,
        ParteProduccion $partesProduccion
    ) {
        if (!$this->canValidate($request)) {
            return response()->json([
                'message' => 'Sin permisos',
            ], 403);
        }

        $partesProduccion->update([
            'estado' => 'validado',
        ]);

        return $partesProduccion->load([
            'user',
            'pieza',
            'molde',
        ]);
    }


    /**
     * CORREGIR PRODUCCIÓN
     */
    public function corregir(
        Request $request,
        ParteProduccion $partesProduccion
    ) {
        if (!$this->canValidate($request)) {
            return response()->json([
                'message' => 'Sin permisos',
            ], 403);
        }

        $partesProduccion->update([
            'estado' => 'corregido',
        ]);

        return $partesProduccion->load([
            'user',
            'pieza',
            'molde',
        ]);
    }


    /**
     * ELIMINAR
     */
    public function destroy(
        ParteProduccion $partesProduccion
    ): JsonResponse {
        $partesProduccion->delete();

        return response()->json([
            'message' => 'Parte eliminado correctamente',
        ]);
    }


    /**
     * VALIDAR PERMISOS
     */
    private function canValidate(
        Request $request
    ): bool {
        $user = $request->user();

        if (!$user) {
            return false;
        }

        return in_array(
            $user->role,
            [
                'planificador',
                'admin',
            ],
            true
        );
    }


    /**
     * VALIDACIÓN
     */
    private function validateData(
        Request $request,
        bool $includeEstado = true
    ): array {
        $rules = [

            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'pieza_id' => [
                'required',
                'exists:piezas,id',
            ],

            'molde_id' => [
                'nullable',
                'exists:moldes,id',
            ],

            'fecha' => [
                'required',
                'date',
            ],

            'turno' => [
                'required',
                'in:mañana,tarde,noche',
            ],

            'maquina' => [
                'nullable',
                'string',
                'max:100',
            ],

            'cantidad_fabricada' => [
                'required',
                'integer',
                'min:0',
            ],

            'cantidad_buena' => [
                'required',
                'integer',
                'min:0',
            ],

            'cantidad_rechazada' => [
                'required',
                'integer',
                'min:0',
            ],

            'minutos_paro' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'motivo_paro' => [
                'nullable',
                'string',
                'max:255',
            ],

            'motivo_rechazo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'averia' => [
                'nullable',
                'string',
                'max:255',
            ],

            'observaciones' => [
                'nullable',
                'string',
            ],
        ];

        if ($includeEstado) {

            $rules['estado'] = [
                'nullable',
                'in:pendiente,validado,corregido',
            ];
        }

        return $request->validate($rules);
    }
}
