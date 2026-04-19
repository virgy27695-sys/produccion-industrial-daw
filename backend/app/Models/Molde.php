<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Molde extends Model
{
    protected $table = 'moldes';

    protected $fillable = [
        'codigo',
        'descripcion',
        'cavidades',
        'tipo_configuracion',
        'maquina',
        'tiempo_ciclo_segundos',
        'stock_seguridad_dias',
        'activo',
    ];

    public function piezas()
    {
        return $this->hasMany(Pieza::class);
    }
}