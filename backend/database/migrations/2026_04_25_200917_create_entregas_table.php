<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();

            // RELACIÓN CON PIEZA
            $table->foreignId('pieza_id')->constrained()->cascadeOnDelete();

            // FECHA REAL DE ENTREGA
            $table->date('fecha');

            // SEMANA (para agrupar)
            $table->integer('anio');
            $table->integer('semana');

            // CANTIDAD ENTREGADA
            $table->integer('cantidad');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
