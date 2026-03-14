<?php

namespace App\Http\Controllers;

use App\Models\Pedido;

class PedidoController extends Controller
{
    public function index()
    {
        return Pedido::with([
            'programa.cliente',
            'detalles.pieza'
        ])->get();
    }
}