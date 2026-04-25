<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            // STOCK ACTUAL DE LA PIEZA
            $table->integer('stock_actual')->default(0);

            // STOCK OBJETIVO (opcional, pero muy útil)
            $table->integer('stock_objetivo')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            $table->dropColumn(['stock_actual', 'stock_objetivo']);
        });
    }
};
