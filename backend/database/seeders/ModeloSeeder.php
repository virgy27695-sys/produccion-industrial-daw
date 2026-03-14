<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modelo;
use App\Models\Cliente;

class ModeloSeeder extends Seeder
{
    public function run(): void
    {
        $audi = Cliente::where('nombre','Audi')->first();
        $bmw = Cliente::where('nombre','BMW')->first();
        $vw = Cliente::where('nombre','Volkswagen')->first();
        $seat = Cliente::where('nombre','Seat')->first();

        Modelo::insert([
            ['cliente_id'=>$audi->id,'nombre'=>'A1'],
            ['cliente_id'=>$audi->id,'nombre'=>'Q8 RL'],

            ['cliente_id'=>$bmw->id,'nombre'=>'F22'],
            ['cliente_id'=>$bmw->id,'nombre'=>'G20'],

            ['cliente_id'=>$vw->id,'nombre'=>'Golf A8'],
            ['cliente_id'=>$vw->id,'nombre'=>'Polo'],

            ['cliente_id'=>$seat->id,'nombre'=>'Leon'],
        ]);
    }
}