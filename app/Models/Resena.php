<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas'; // Pluralización manual

    protected $fillable = [
        'user_id',
        'destino_id',
        'evento_id',
        'calificacion', // Del 1 al 5
        'titulo',
        'comentario',
        'anonima',
        'aprobada',          // null = pendiente, true = aprobada, false = rechazada
        'moderacion_motivo', // explicación breve de la decisión
        'moderada_por',      // 'ia' o 'manual'
        'moderada_at',
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'anonima' => 'boolean',
        'aprobada' => 'boolean',
        'moderada_at' => 'datetime',
    ];

    /**
     * Obtiene el usuario que escribió la reseña.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el destino al que pertenece la reseña (opcional).
     */
    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class);
    }

    /**
     * Obtiene el evento al que pertenece la reseña (opcional).
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    /**
     * Obtiene las imágenes asociadas a esta reseña.
     */
    public function imagenes(): HasMany
    {
        return $this->hasMany(ImagenResena::class, 'resena_id');
    }
}