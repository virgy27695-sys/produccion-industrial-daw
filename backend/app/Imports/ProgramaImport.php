<?php

namespace App\Imports;

// IMPORTS
use App\Models\Pieza;
use App\Models\ProgramaDetalle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProgramaImport implements ToCollection
{
    private int $programaId;

    public function __construct(int $programaId)
    {
        $this->programaId = $programaId;
    }

    // LECTURA DEL EXCEL
    // Recorre el archivo del cliente y actualiza semanas del programa.
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Saltar filas iniciales del Excel.
            if ($index < 3) {
                continue;
            }

            $familia = trim((string) ($row[0] ?? ""));
            $codigo = trim((string) ($row[1] ?? ""));
            $denominacion = trim((string) ($row[2] ?? ""));

            if (!$codigo || !$denominacion) {
                continue;
            }

            // Buscar pieza existente.
            // No se crean piezas nuevas desde Excel para evitar errores por archivos mal formateados.
            $pieza = Pieza::where('codigo', $codigo)->first();

            if (!$pieza) {
                continue;
            }

            // Primeras 4 semanas del programa.
            // En el Excel real empiezan desde la columna D.
            $semanas = [
                8 => $row[3] ?? 0,
                9 => $row[4] ?? 0,
                10 => $row[5] ?? 0,
                11 => $row[6] ?? 0,
            ];

            foreach ($semanas as $semana => $cantidad) {
                $cantidad = (int) $cantidad;

                if ($cantidad <= 0) {
                    continue;
                }

                ProgramaDetalle::updateOrCreate(
                    [
                        'programa_id' => $this->programaId,
                        'pieza_id' => $pieza->id,
                        'anio' => 2026,
                        'semana' => $semana,
                    ],
                    [
                        'cantidad' => $cantidad,
                        'familia_texto' => $familia,
                        'comentarios' => 'Importado desde Excel',
                    ]
                );
            }
        }
    }
}
