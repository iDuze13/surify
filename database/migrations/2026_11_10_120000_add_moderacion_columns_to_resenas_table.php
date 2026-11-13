<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columnas = ['moderacion_motivo', 'moderada_por', 'moderada_at'];

    public function up(): void
    {
        // Cada columna se crea solo si todavía no existe.
        Schema::table('resenas', function (Blueprint $table) {
            if (!Schema::hasColumn('resenas', 'moderacion_motivo')) {
                // Explicación breve de la decisión (de la IA o de la persona que modera)
                $table->text('moderacion_motivo')->nullable();
            }
            if (!Schema::hasColumn('resenas', 'moderada_por')) {
                // 'ia' o 'manual'; null = todavía sin moderar
                $table->string('moderada_por', 20)->nullable();
            }
            if (!Schema::hasColumn('resenas', 'moderada_at')) {
                $table->timestamp('moderada_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        $existentes = array_values(array_filter(
            $this->columnas,
            fn(string $columna) => Schema::hasColumn('resenas', $columna)
        ));

        if (!empty($existentes)) {
            Schema::table('resenas', function (Blueprint $table) use ($existentes) {
                $table->dropColumn($existentes);
            });
        }
    }
};