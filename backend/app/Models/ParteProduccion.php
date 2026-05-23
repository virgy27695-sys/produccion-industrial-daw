<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParteProduccion extends Model
{
    protected $fillable = [
        'user_id',
        'pieza_id',
        'molde_id',
        'fecha',
        'turno',
        'maquina',
        'cantidad_fabricada',
        'cantidad_buena',
        'cantidad_rechazada',
        'minutos_paro',
        'motivo_paro',
        'motivo_rechazo',
        'averia',
        'estado',
        'observaciones',
    ];


    // USUARIO QUE CREA EL PARTE
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    // PIEZA FABRICADA
    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }


    // MOLDE USADO
    public function molde()
    {
        return $this->belongsTo(Molde::class);
    }
}
