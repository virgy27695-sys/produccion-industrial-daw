<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fabricacion extends Model
{
    // NOMBRE REAL DE LA TABLA
    // Laravel no pluraliza correctamente "Fabricacion", por eso se indica manualmente.
    protected $table = 'fabricaciones';

    // CAMPOS ASIGNABLES
    protected $fillable = [
        'pieza_id',
        'fecha',
        'anio',
        'semana',
        'cantidad',
    ];

    // RELACIÓN CON PIEZA
    // Cada registro de fabricación pertenece a una pieza.
    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }
}
