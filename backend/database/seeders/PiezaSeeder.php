<?php

namespace Database\Seeders;

use App\Models\Modelo;
use App\Models\Molde;
use App\Models\Pieza;
use Illuminate\Database\Seeder;

class PiezaSeeder extends Seeder
{
    public function run(): void
    {
        $a1 = Modelo::where('nombre', 'A1')->first();
        $f22 = Modelo::where('nombre', 'F22')->first();
        $g20 = Modelo::where('nombre', 'G20')->first();
        $golf = Modelo::where('nombre', 'Golf A8')->first();
        $leon = Modelo::where('nombre', 'Leon')->first();

        $moldeAudi = Molde::where('codigo', 'M-AUDI-270')->first();
        $moldeF22 = Molde::where('codigo', 'M-BMW-F22')->first();
        $moldeG20 = Molde::where('codigo', 'M-BMW-G20')->first();
        $moldeGolf = Molde::where('codigo', 'M-GOLF-A8')->first();
        $moldeLeon = Molde::where('codigo', 'M-LEON')->first();

        Pieza::insert([
            [
                'codigo' => '90112502',
                'denominacion' => 'SOPORTE INF DRL AUDI AU270 LED I',
                'modelo_id' => $a1->id,
                'molde_id' => $moldeAudi->id,
            ],
            [
                'codigo' => '90112503',
                'denominacion' => 'SOPORTE SUP DRL AUDI AU270 LED D',
                'modelo_id' => $a1->id,
                'molde_id' => $moldeAudi->id,
            ],
            [
                'codigo' => '90137358',
                'denominacion' => 'LB REFLECTOR INY BMW F22 LCI EST/DIN TI',
                'modelo_id' => $f22->id,
                'molde_id' => $moldeF22->id,
            ],
            [
                'codigo' => '90137360',
                'denominacion' => 'LB REFLECTOR INY BMW G20 LCI DIN TI',
                'modelo_id' => $g20->id,
                'molde_id' => $moldeG20->id,
            ],
            [
                'codigo' => '90221451',
                'denominacion' => 'SOPORTE DRL VW GOLF A8 IZQ',
                'modelo_id' => $golf->id,
                'molde_id' => $moldeGolf->id,
            ],
            [
                'codigo' => '90221452',
                'denominacion' => 'SOPORTE DRL VW GOLF A8 DER',
                'modelo_id' => $golf->id,
                'molde_id' => $moldeGolf->id,
            ],
            [
                'codigo' => '90311510',
                'denominacion' => 'SOPORTE DRL SEAT LEON IZQ',
                'modelo_id' => $leon->id,
                'molde_id' => $moldeLeon->id,
            ],
            [
                'codigo' => '90311511',
                'denominacion' => 'SOPORTE DRL SEAT LEON DER',
                'modelo_id' => $leon->id,
                'molde_id' => $moldeLeon->id,
            ],
        ]);
    }
}
