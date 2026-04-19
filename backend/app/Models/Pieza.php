<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pieza extends Model
{
    protected $table = 'piezas';

    protected $fillable = [
        'codigo',
        'denominacion',
        'modelo_id',
        'molde_id',
        'lado_pieza',
        'mercado',
        'categoria_funcional',
    ];

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function molde()
    {
        return $this->belongsTo(Molde::class);
    }
}
