<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moldes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 255);
            $table->unsignedInteger('cavidades')->default(1);
            $table->enum('tipo_configuracion', [
                'simple',
                'izquierda_derecha',
                'multiple_referencias',
            ])->default('simple');
            $table->string('maquina', 100)->nullable();
            $table->unsignedInteger('tiempo_ciclo_segundos')->nullable();
            $table->unsignedInteger('stock_seguridad_dias')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moldes');
    }
};