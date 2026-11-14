<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
        'rechazado_at',
        'motivo_rechazo',
        'revisado_por',
        'revisado_at',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'rechazado_at' => 'datetime',
        'revisado_at' => 'datetime',
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
     * Usuario que sugirió el evento (null si lo cargó el equipo).
     */
    public function autorSugerencia(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sugerido_por');
    }

    /**
     * Admin que aprobó o rechazó la sugerencia.
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    /**
     * Eventos sugeridos por usuarios, filtrados por estado de moderación:
     * 'pendiente', 'aprobada' o 'rechazada' (null = todos).
     */
    public function scopeSugerencias(Builder $query, ?string $estado = null): Builder
    {
        $query->whereNotNull('sugerido_por');

        return match ($estado) {
            'pendiente' => $query->where('activo', false)->whereNull('rechazado_at'),
            'aprobada'  => $query->where('activo', true),
            'rechazada' => $query->whereNotNull('rechazado_at'),
            default     => $query,
        };
    }

    /**
     * Estado de moderación: 'pendiente', 'aprobada', 'rechazada' o null si no es una sugerencia.
     */
    public function getEstadoSugerenciaAttribute(): ?string
    {
        if (!$this->sugerido_por) {
            return null;
        }
        if ($this->activo) {
            return 'aprobada';
        }

        return $this->rechazado_at ? 'rechazada' : 'pendiente';
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