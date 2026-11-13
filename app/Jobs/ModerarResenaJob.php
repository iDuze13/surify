<?php

namespace App\Jobs;

use App\Models\Resena;
use App\Notifications\ResenaModerada;
use App\Services\ModeracionResenaService;
use App\Services\NotificacionService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

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

        // Si la IA decidió, se le avisa al autor con el mismo aviso que usa la moderación manual.
        // Si el aviso falla (por ejemplo el correo), no se pierde la decisión ya guardada.
        if ($resena->aprobada !== null && $resena->user) {
            try {
                NotificacionService::enviar(
                    $resena->user,
                    new ResenaModerada($resena->load(['destino', 'evento']), (bool) $resena->aprobada)
                );
            } catch (Throwable $e) {
                Log::warning('No se pudo avisar al autor de la reseña: ' . $e->getMessage());
            }
        }
    }
}