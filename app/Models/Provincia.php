<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProvinciaImagen;
class Provincia extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_url',
        'region',
    ];

    /**
     * Obtiene los destinos turísticos asociados a esta provincia.
     */
    public function destinos(): HasMany
    {
        return $this->hasMany(Destino::class);
    }

    /**
     * Obtiene los eventos asociados a esta provincia.
     */
    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class)->where('activo', true);
    }

    /**
     * Obtiene los platos o establecimientos gastronómicos de la provincia.
     */
    public function gastronomia(): HasMany
    {
        return $this->hasMany(Gastronomia::class);
    }

    public function imagenes(): HasMany
{
    return $this->hasMany(ProvinciaImagen::class)->orderBy('orden');
}
}
