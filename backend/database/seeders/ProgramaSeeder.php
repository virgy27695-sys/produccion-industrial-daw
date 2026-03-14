<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProgramaNecesidad;
use App\Models\Cliente;

class ProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $audi = Cliente::where('nombre','Audi')->first();
        $bmw = Cliente::where('nombre','BMW')->first();

        ProgramaNecesidad::insert([
            [
                'cliente_id'=>$audi->id,
                'fecha_solicitud'=>now(),
                'fecha_entrega'=>now()->addDays(30),
                'estado'=>'pendiente',
                'observaciones'=>'Producción inicial'
            ],
            [
                'cliente_id'=>$bmw->id,
                'fecha_solicitud'=>now(),
                'fecha_entrega'=>now()->addDays(45),
                'estado'=>'pendiente',
                'observaciones'=>'Programa trimestral'
            ]
        ]);
    }
}