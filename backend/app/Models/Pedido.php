<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'programa_id',
        'fecha_pedido',
        'estado'
    ];

    public function programa()
    {
        return $this->belongsTo(ProgramaNecesidad::class,'programa_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}