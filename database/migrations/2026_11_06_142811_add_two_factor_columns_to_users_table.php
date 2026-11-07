<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columnas = [
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    public function up(): void
    {
        // Cada columna se crea solo si todavía no existe, así no da error
        // si ya habías hecho una migración parecida antes.
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                // Secreto TOTP (se guarda cifrado con APP_KEY desde el modelo)
                $table->text('two_factor_secret')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                // Hashes de los códigos de recuperación (array cifrado)
                $table->text('two_factor_recovery_codes')->nullable();
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                // Fecha en que la persona confirmó el primer código; null = 2FA desactivado
                $table->timestamp('two_factor_confirmed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        $existentes = array_values(array_filter(
            $this->columnas,
            fn(string $columna) => Schema::hasColumn('users', $columna)
        ));

        if (!empty($existentes)) {
            Schema::table('users', function (Blueprint $table) use ($existentes) {
                $table->dropColumn($existentes);
            });
        }
    }
};