<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Pieza;
use App\Models\ProgramaNecesidad;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $programa = ProgramaNecesidad::first();
        $pieza = Pieza::first();

        $pedido = Pedido::create([
            'programa_id' => $programa->id,
            'fecha_pedido' => now(),
            'estado' => 'produccion'
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'pieza_id' => $pieza->id,
            'cantidad' => 500
        ]);
    }
}