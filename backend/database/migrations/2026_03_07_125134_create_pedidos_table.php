<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_id')
                ->constrained('programas_necesidades')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('fecha_pedido')->nullable();
            $table->enum('estado', ['pendiente', 'produccion', 'enviado', 'entregado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
