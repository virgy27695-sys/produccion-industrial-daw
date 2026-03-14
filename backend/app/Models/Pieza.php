<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pieza extends Model
{
    protected $table = 'piezas';

    protected $fillable = [
        'codigo',
        'denominacion',
        'modelo_id'
    ];

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }
}