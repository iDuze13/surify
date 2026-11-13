<?php

namespace App\Jobs;

use App\Models\Resena;
use App\Services\ModeracionResenaService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

/**
 * Se ejecuta con ModerarResenaJob::dispatchAfterResponse($id): corre apenas se le
 * respondió a la persona, así no hace falta un worker de colas en Railway.
 * (Si más adelante usan colas, alcanza con agregar ShouldQueue y llamar a dispatch().)
 */
class ModerarResenaJob
{
    use Dispatchable;

    public function __construct(public int $resenaId) {}

    public function handle(ModeracionResenaService $servicio): void
    {
        $resena = Resena::with(['imagenes', 'destino.provincia', 'evento.provincia'])->find($this->resenaId);

        // Si la borraron, o si una persona ya la aprobó o rechazó, no se toca.
        if (!$resena || $resena->aprobada !== null) {
            return;
        }

        $resultado = $servicio->moderarResena($resena);

        $resena->moderacion_motivo = Str::limit($resultado['motivo'], 250);
        $resena->moderada_por = 'ia';
        $resena->moderada_at = now();

        if ($resultado['decision'] === 'aprobar') {
            $resena->aprobada = true;
        } elseif ($resultado['decision'] === 'rechazar') {
            $resena->aprobada = false;
        }
        // 'revisar': aprobada sigue en null, o sea pendiente para el panel de administración.

        $resena->save();
    }
}