<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Perfil de un usuario visto por otras personas (/usuarios/{id}).
 * Reutiliza la misma vista del perfil propio (profile.edit), sin las partes
 * privadas: actividad reciente, configuración, email, cambiar foto/portada.
 */
class PerfilPublicoController extends Controller
{
    public function show(Request $request, User $user)
    {
        $viewer  = auth()->user();
        $esDueno = $viewer && $viewer->id === $user->id;

        // ?vista=publica: el dueño puede ver su perfil como lo ven los demás
        $vistaPrevia = $esDueno && $request->query('vista') === 'publica';

        // Si el dueño entra a su propio perfil público, va a su perfil normal
        if ($esDueno && !$vistaPrevia) {
            return redirect()->route('profile.edit');
        }

        // Los admins ven completos también los perfiles privados (salvo en vista previa)
        $esAdmin = $viewer && !$vistaPrevia
            && ($viewer->hasRole('admin') || $viewer->hasRole('Admin'));

        return view('profile.edit', [
            'usuario'     => $user,
            'esPropio'    => false,
            'privado'     => !$user->perfil_publico && !$esAdmin,
            'vistaPrevia' => $vistaPrevia,
            'avisoAdmin'  => !$user->perfil_publico && $esAdmin,
        ]);
    }
}