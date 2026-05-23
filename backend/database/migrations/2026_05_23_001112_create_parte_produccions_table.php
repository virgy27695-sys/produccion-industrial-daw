<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * APLICAR MIGRACIÓN
     */
    public function up(): void
    {
        Schema::create('parte_produccions', function (Blueprint $table) {
            $table->id();

            // QUIÉN REGISTRA EL PARTE
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // DATOS INDUSTRIALES
            $table->foreignId('pieza_id')
                ->constrained('piezas')
                ->cascadeOnDelete();

            $table->foreignId('molde_id')
                ->nullable()
                ->constrained('moldes')
                ->nullOnDelete();

            // FECHA Y TURNO
            $table->date('fecha');

            $table->enum('turno', [
                'mañana',
                'tarde',
                'noche',
            ]);

            // MÁQUINA
            $table->string('maquina', 100)
                ->nullable();

            // CANTIDADES
            $table->unsignedInteger('cantidad_fabricada')
                ->default(0);

            $table->unsignedInteger('cantidad_buena')
                ->default(0);

            $table->unsignedInteger('cantidad_rechazada')
                ->default(0);

            // INCIDENCIAS / PAROS
            $table->unsignedInteger('minutos_paro')
                ->default(0);

            $table->string('motivo_paro')
                ->nullable();

            $table->string('motivo_rechazo')
                ->nullable();

            $table->string('averia')
                ->nullable();

            // ESTADO DE VALIDACIÓN
            $table->enum('estado', [
                'pendiente',
                'validado',
                'corregido',
            ])->default('pendiente');

            // OBSERVACIONES
            $table->text('observaciones')
                ->nullable();

            $table->timestamps();
        });
    }


    /**
     * REVERTIR MIGRACIÓN
     */
    public function down(): void
    {
        Schema::dropIfExists('parte_produccions');
    }
};
