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
        Schema::table('users', function (Blueprint $table) {

            // Evita crear la columna dos veces
            if (!Schema::hasColumn('users', 'role')) {

                /*
                 * ROLES DEL SISTEMA
                 *
                 * admin
                 * planificador
                 * encargado
                 * almacen
                 *
                 * encargado queda por defecto
                 * como usuario operativo base.
                 */

                $table->string('role')
                    ->default('encargado')
                    ->after('password');
            }
        });
    }


    /**
     * REVERTIR MIGRACIÓN
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
