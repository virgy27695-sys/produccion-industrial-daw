<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $table = 'modelos';

    protected $fillable = [
        'cliente_id',
        'nombre',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function piezas()
    {
        return $this->hasMany(Pieza::class);
    }
}