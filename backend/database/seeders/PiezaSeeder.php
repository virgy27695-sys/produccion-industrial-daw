<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pieza;
use App\Models\Modelo;

class PiezaSeeder extends Seeder
{
    public function run(): void
    {
        $a1 = Modelo::where('nombre','A1')->first();
        $f22 = Modelo::where('nombre','F22')->first();
        $g20 = Modelo::where('nombre','G20')->first();
        $golf = Modelo::where('nombre','Golf A8')->first();
        $leon = Modelo::where('nombre','Leon')->first();

        Pieza::insert([
            [
                'codigo'=>'90112502',
                'denominacion'=>'SOPORTE INF DRL AUDI AU270 LED I',
                'modelo_id'=>$a1->id
            ],
            [
                'codigo'=>'90112503',
                'denominacion'=>'SOPORTE SUP DRL AUDI AU270 LED D',
                'modelo_id'=>$a1->id
            ],
            [
                'codigo'=>'90137358',
                'denominacion'=>'LB REFLECTOR INY BMW F22 LCI EST/DIN TI',
                'modelo_id'=>$f22->id
            ],
            [
                'codigo'=>'90137360',
                'denominacion'=>'LB REFLECTOR INY BMW G20 LCI DIN TI',
                'modelo_id'=>$g20->id
            ],
            [
                'codigo'=>'90221451',
                'denominacion'=>'SOPORTE DRL VW GOLF A8 IZQ',
                'modelo_id'=>$golf->id
            ],
            [
                'codigo'=>'90221452',
                'denominacion'=>'SOPORTE DRL VW GOLF A8 DER',
                'modelo_id'=>$golf->id
            ],
            [
                'codigo'=>'90311510',
                'denominacion'=>'SOPORTE DRL SEAT LEON IZQ',
                'modelo_id'=>$leon->id
            ],
            [
                'codigo'=>'90311511',
                'denominacion'=>'SOPORTE DRL SEAT LEON DER',
                'modelo_id'=>$leon->id
            ]
        ]);
    }
}