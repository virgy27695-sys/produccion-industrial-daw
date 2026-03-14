<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaNecesidad extends Model
{
    protected $table = 'programas_necesidades';

    protected $fillable = [
        'cliente_id',
        'fecha_solicitud',
        'fecha_entrega',
        'estado',
        'observaciones'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class,'programa_id');
    }
}