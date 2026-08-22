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

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'biografia', 'idioma'])]
#[Hidden(['password', 'remember_token'])]
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
}
