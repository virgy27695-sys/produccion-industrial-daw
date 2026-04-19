<?php

namespace App\Console\Commands;

// IMPORTS
use Illuminate\Console\Command;
use App\Models\Pieza;

// COMANDO PARA AUTOCOMPLETAR CAMPOS PRODUCTIVOS DE PIEZAS
class AutocompletarCamposPiezas extends Command
{
    // NOMBRE DEL COMANDO EN TERMINAL
    protected $signature = 'piezas:autocompletar-campos';

    // DESCRIPCIÓN
    protected $description = 'Autocompleta lado, mercado y categoría funcional en piezas';

    public function handle()
    {
        $this->info('Iniciando autocompletado de piezas...');

        // Obtener todas las piezas
        $piezas = Pieza::all();

        foreach ($piezas as $pieza) {

            $denominacion = strtoupper($pieza->denominacion);

            // DETECCIÓN DE LADO
            // Se usa regex para evitar falsos positivos (ej: AUDI contiene "I")
            $lado = null;

            if (preg_match('/\b(IZQ|I)\b/', $denominacion)) {
                $lado = 'izquierda';
            }

            if (preg_match('/\b(DER|D)\b/', $denominacion)) {
                $lado = 'derecha';
            }

            // =========================
            // DETECCIÓN DE MERCADO
            // =========================
            $mercado = null;

            if (str_contains($denominacion, 'TI')) {
                $mercado = 'trafico_izquierdo';
            }

            if (str_contains($denominacion, 'TD')) {
                $mercado = 'trafico_derecho';
            }

            // =========================
            // DETECCIÓN DE CATEGORÍA
            // =========================
            $categoria = null;

            if (str_contains($denominacion, 'SOPORTE')) {
                $categoria = 'soporte';
            }

            if (str_contains($denominacion, 'REFLECTOR')) {
                $categoria = 'reflector';
            }

            if (str_contains($denominacion, 'DRL')) {
                $categoria = 'drl';
            }

            // =========================
            // ACTUALIZAR PIEZA
            // =========================
            $pieza->update([
                'lado_pieza' => $lado,
                'mercado' => $mercado,
                'categoria_funcional' => $categoria,
            ]);

            $this->line("Pieza {$pieza->codigo} actualizada");
        }

        $this->info('Autocompletado finalizado');
    }
}
