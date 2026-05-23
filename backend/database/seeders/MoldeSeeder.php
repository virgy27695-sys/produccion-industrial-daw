<?php

namespace Database\Seeders;

use App\Models\Molde;
use Illuminate\Database\Seeder;

class MoldeSeeder extends Seeder
{
    public function run(): void
    {
        Molde::insert([

            [
                'codigo' => 'M-AUDI-270',
                'descripcion' => 'Molde Audi AU270 DRL',
                'cavidades' => 2,
                'tipo_configuracion' => 'izquierda_derecha',
                'maquina' => 'Inyectora 1',
                'tiempo_ciclo_segundos' => 45,
                'stock_seguridad_dias' => 5,
                'activo' => true,
            ],

            [
                'codigo' => 'M-BMW-F22',
                'descripcion' => 'Molde BMW F22 reflector',
                'cavidades' => 1,
                'tipo_configuracion' => 'simple',
                'maquina' => 'Inyectora 2',
                'tiempo_ciclo_segundos' => 50,
                'stock_seguridad_dias' => 5,
                'activo' => true,
            ],

            [
                'codigo' => 'M-BMW-G20',
                'descripcion' => 'Molde BMW G20 reflector',
                'cavidades' => 1,
                'tipo_configuracion' => 'simple',
                'maquina' => 'Inyectora 3',
                'tiempo_ciclo_segundos' => 52,
                'stock_seguridad_dias' => 5,
                'activo' => true,
            ],

            [
                'codigo' => 'M-GOLF-A8',
                'descripcion' => 'Molde VW Golf A8 DRL',
                'cavidades' => 2,
                'tipo_configuracion' => 'izquierda_derecha',
                'maquina' => 'Inyectora 4',
                'tiempo_ciclo_segundos' => 48,
                'stock_seguridad_dias' => 5,
                'activo' => true,
            ],

            [
                'codigo' => 'M-LEON',
                'descripcion' => 'Molde Seat Leon DRL',
                'cavidades' => 2,
                'tipo_configuracion' => 'izquierda_derecha',
                'maquina' => 'Inyectora 5',
                'tiempo_ciclo_segundos' => 46,
                'stock_seguridad_dias' => 5,
                'activo' => true,
            ],

        ]);
    }
}
