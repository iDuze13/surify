<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Moderación de eventos sugeridos por usuarios.
 *
 * Estados de una sugerencia (eventos con sugerido_por):
 *   - Pendiente: activo = false y rechazado_at = null
 *   - Aprobada:  activo = true
 *   - Rechazada: rechazado_at con fecha (queda guardada, no se publica)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Cada columna se crea solo si todavía no existe.
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'rechazado_at')) {
                $table->timestamp('rechazado_at')->nullable();
            }
            if (!Schema::hasColumn('eventos', 'motivo_rechazo')) {
                // Se le muestra a quien sugirió el evento
                $table->text('motivo_rechazo')->nullable();
            }
            if (!Schema::hasColumn('eventos', 'revisado_por')) {
                // Admin que aprobó o rechazó la sugerencia
                $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('eventos', 'revisado_at')) {
                $table->timestamp('revisado_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            if (Schema::hasColumn('eventos', 'revisado_por')) {
                $table->dropConstrainedForeignId('revisado_por');
            }
        });

        $existentes = array_values(array_filter(
            ['rechazado_at', 'motivo_rechazo', 'revisado_at'],
            fn(string $columna) => Schema::hasColumn('eventos', $columna)
        ));

        if (!empty($existentes)) {
            Schema::table('eventos', function (Blueprint $table) use ($existentes) {
                $table->dropColumn($existentes);
            });
        }
    }
};