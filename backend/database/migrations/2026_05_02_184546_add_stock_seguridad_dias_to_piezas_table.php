<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            // DÍAS DE STOCK DE SEGURIDAD
            // En el proyecto se trabaja con un mínimo de 3 días de cobertura.
            $table->integer('stock_seguridad_dias')
                ->default(3)
                ->after('stock_actual');
        });
    }

    public function down(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->dropColumn('stock_seguridad_dias');
        });
    }
};