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
        'molde_id',
        'fecha',
        'turno',
        'anio',
        'semana',
        'cantidad',
        'observaciones',
    ];

    // RELACIÓN CON PIEZA
    // Cada fabricación pertenece a una pieza.
    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }

    // RELACIÓN CON MOLDE
    // Permite saber qué molde estuvo trabajando en ese turno.
    public function molde()
    {
        return $this->belongsTo(Molde::class);
    }
}
