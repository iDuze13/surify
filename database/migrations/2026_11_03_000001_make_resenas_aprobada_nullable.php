<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * RF2: las reseñas nuevas deben quedar PENDIENTES (aprobada = NULL)
     * hasta que un administrador las apruebe (true) o rechace (false).
     * Antes la columna era NOT NULL con default true, por eso se publicaban solas.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE resenas ALTER COLUMN aprobada DROP NOT NULL');
        DB::statement('ALTER TABLE resenas ALTER COLUMN aprobada DROP DEFAULT');
    }

    public function down(): void
    {
        DB::statement('UPDATE resenas SET aprobada = false WHERE aprobada IS NULL');
        DB::statement('ALTER TABLE resenas ALTER COLUMN aprobada SET DEFAULT true');
        DB::statement('ALTER TABLE resenas ALTER COLUMN aprobada SET NOT NULL');
    }
};