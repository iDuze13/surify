<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'provincia_id',
        'destino_id',
        'nombre',
        'descripcion',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'rango_precio',
        'imagen_url',
        'activo',
        'sugerido_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];
    /**
     * Obtiene la provincia a la que pertenece este evento.
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Obtiene el destino al que pertenece este evento (opcional).
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }

    /**
     * Obtiene las reseñas APROBADAS asociadas a este evento (RF2).
     */
    public function resenas(): HasMany
    {
        return $this->hasMany(Resena::class)->where('aprobada', true);
    }

    /**
     * Obtiene los favoritos asociados a este evento.
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }
    /**
     * Determina si el evento ya pasó.
     */
    public function getPasadoAttribute(): bool
    {
        $fecha = $this->fecha_fin ?? $this->fecha_inicio;

        return Carbon::parse($fecha)->isPast();
    }
}