<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
    ];

    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }

    public function programasNecesidades()
    {
        return $this->hasMany(ProgramaNecesidad::class);
    }
}