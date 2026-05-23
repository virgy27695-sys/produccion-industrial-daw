<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->enum('lado_pieza', ['izquierda', 'derecha', 'neutra'])
                ->nullable();

            $table->string('mercado', 20)
                ->nullable();

            $table->string('categoria_funcional', 50)
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->dropColumn([
                'lado_pieza',
                'mercado',
                'categoria_funcional',
            ]);
        });
    }
};