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
                ->nullable()
                ->after('molde_id');

            $table->string('mercado', 20)
                ->nullable()
                ->after('lado_pieza');

            $table->string('categoria_funcional', 50)
                ->nullable()
                ->after('mercado');
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
