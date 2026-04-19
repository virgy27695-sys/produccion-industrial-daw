<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'pedido_id',
        'pieza_id',
        'cantidad',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }
}