<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'biografia', 'idioma', 'perfil_publico'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'perfil_publico' => 'boolean',
        ];
    }

    /**
     * Obtiene los roles asignados al usuario.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Obtiene las reseñas escritas por el usuario.
     */
    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class);
    }

    /**
     * Obtiene los favoritos del usuario.
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    /**
     * Obtiene los destinos visitados por el usuario.
     */
    public function destinosVisitados(): HasMany
    {
        return $this->hasMany(DestinoVisitado::class);
    }

    /**
     * Verifica si el usuario tiene un rol específico por su nombre.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('nombre', $roleName)->exists();
    }

    public function hasPermiso(string $permiso): bool
    {
        return $this->roles()
            ->whereHas('permisos', fn($q) => $q->where('nombre', $permiso))
            ->exists();
    }

    public function permisos()
    {
        return Permiso::whereHas('roles', fn($q) => $q->whereIn('roles.id', $this->roles->pluck('id')))->get();
    }

    public function eventosVisitados(): HasMany
    {
        return $this->hasMany(EventoVisitado::class);
    }

    /**
     * Ruta a la que va el usuario después de iniciar sesión (y de pasar el 2FA).
     * Es la misma lógica que antes estaba en AuthenticatedSessionController.
     */
    public function rutaInicial(): string
    {
        if ($this->hasRole('Admin')) {
            return 'dashboard';
        }

        if ($this->hasPermiso('crear_evento') && $this->hasPermiso('modificar_evento')) {
            return 'admin.eventos.index';
        }

        if ($this->hasPermiso('crear_destino') && $this->hasPermiso('modificar_destino')) {
            return 'admin.destinos.index';
        }

        return 'home';
    }

    // ==================== AUTENTICACIÓN DE DOS FACTORES ====================

    /**
     * El 2FA está activo solo cuando hay secreto y la persona confirmó un primer código.
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    /**
     * Hash de un código de recuperación (HMAC con APP_KEY).
     * Nunca se guardan los códigos en texto plano.
     */
    public static function hashCodigoRecuperacion(string $codigo): string
    {
        return hash_hmac('sha256', Str::lower(trim($codigo)), (string) config('app.key'));
    }

    /**
     * Genera 8 códigos nuevos, guarda sus hashes en el modelo (sin hacer save())
     * y devuelve los códigos en texto plano para mostrarlos una única vez.
     *
     * @return array<int, string>
     */
    public function generarCodigosDeRecuperacion(): array
    {
        $planos = [];
        for ($i = 0; $i < 8; $i++) {
            $planos[] = Str::lower(Str::random(5)) . '-' . Str::lower(Str::random(5));
        }

        $this->two_factor_recovery_codes = array_map(
            fn(string $codigo) => self::hashCodigoRecuperacion($codigo),
            $planos
        );

        return $planos;
    }

    /**
     * Si el código es válido lo consume (un solo uso) y devuelve true.
     */
    public function consumirCodigoDeRecuperacion(string $codigo): bool
    {
        $hash = self::hashCodigoRecuperacion($codigo);
        $guardados = $this->two_factor_recovery_codes ?? [];

        foreach ($guardados as $i => $guardado) {
            if (hash_equals($guardado, $hash)) {
                unset($guardados[$i]);
                $this->two_factor_recovery_codes = array_values($guardados);
                $this->save();

                return true;
            }
        }

        return false;
    }
}