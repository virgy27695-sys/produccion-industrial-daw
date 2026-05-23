<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            if (!Schema::hasColumn('piezas', 'molde_id')) {
                $table->foreignId('molde_id')
                    ->nullable()
                    ->after('modelo_id')
                    ->constrained('moldes')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('piezas', function (Blueprint $table) {
            if (Schema::hasColumn('piezas', 'molde_id')) {
                $table->dropConstrainedForeignId('molde_id');
            }
        });
    }
};
