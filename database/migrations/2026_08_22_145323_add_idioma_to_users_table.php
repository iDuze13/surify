<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('idioma')->default('es')->after('avatar');
            $table->boolean('modo_oscuro')->default(false)->after('idioma');
            $table->boolean('notificaciones')->default(true)->after('modo_oscuro');
            $table->string('telefono')->nullable()->after('notificaciones');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['idioma', 'modo_oscuro', 'notificaciones', 'telefono']);
        });
    }
};