<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabricaciones', function (Blueprint $table) {
            $table->id();

            // PIEZA FABRICADA
            $table->foreignId('pieza_id')
                ->constrained('piezas')
                ->cascadeOnDelete();

            // FECHA REAL DE FABRICACIÓN
            $table->date('fecha');

            // AÑO Y SEMANA PARA AGRUPAR LA PRODUCCIÓN
            $table->integer('anio');
            $table->integer('semana');

            // CANTIDAD FABRICADA
            $table->integer('cantidad');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabricaciones');
    }
};
