<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    // NOMBRE REAL DE LA TABLA
    // Laravel no pluraliza correctamente "Entrega", por eso se indica manualmente.
    protected $table = 'entregas';

    // CAMPOS ASIGNABLES
    protected $fillable = [
        'pieza_id',
        'fecha',
        'anio',
        'semana',
        'cantidad',
    ];

    // RELACIÓN CON PIEZA
    // Cada entrega pertenece a una pieza.
    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }
}
