<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programa_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('programa_id')
                ->constrained('programas_necesidades')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('pieza_id')
                ->constrained('piezas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('semana');
            $table->unsignedInteger('cantidad')->default(0);

            $table->string('familia_texto', 100)->nullable();
            $table->text('comentarios')->nullable();

            $table->timestamps();

            $table->unique(['programa_id', 'pieza_id', 'anio', 'semana'], 'programa_pieza_semana_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programa_detalles');
    }
};