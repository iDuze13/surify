<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destino extends Model
{
    use HasFactory;

    protected $fillable = [
        'provincia_id',
        'nombre',
        'descripcion',
        'rango_precio', // Bajo, Medio, Alto
        'categoria', // Aventura, Cultural, Gastronómico, etc
        'ubicacion', // PostGIS POINT
        'imagen_url',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Obtiene la provincia a la que pertenece el destino.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Obtiene los eventos asociados a este destino.
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class)->where('activo', true);
    }

    /**
     * Obtiene las reseñas APROBADAS asociadas a este destino (RF2).
     */
    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class)->where('aprobada', true);
    }

    /**
     * Obtiene los favoritos asociados a este destino.
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    /**
     * Obtiene los registros de visitas de usuarios a este destino.
     */
    public function visitas(): HasMany
    {
        return $this->hasMany(DestinoVisitado::class);
    }
}