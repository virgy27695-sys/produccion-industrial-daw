<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabricaciones', function (Blueprint $table) {
            // MOLDE UTILIZADO EN EL TURNO
            $table->foreignId('molde_id')
                ->nullable()
                ->after('pieza_id')
                ->constrained('moldes')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // TURNO DE FABRICACIÓN
            $table->enum('turno', ['mañana', 'tarde', 'noche'])
                ->after('fecha');

            // OBSERVACIONES DEL TURNO
            $table->text('observaciones')
                ->nullable()
                ->after('cantidad');
        });
    }

    public function down(): void
    {
        Schema::table('fabricaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('molde_id');
            $table->dropColumn(['turno', 'observaciones']);
        });
    }
};
