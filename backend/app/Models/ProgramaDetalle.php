<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaDetalle extends Model
{
    protected $table = 'programa_detalles';

    protected $fillable = [
        'programa_id',
        'pieza_id',
        'anio',
        'semana',
        'cantidad',
        'familia_texto',
        'comentarios',
    ];

    public function programa()
    {
        return $this->belongsTo(ProgramaNecesidad::class, 'programa_id');
    }

    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }
}