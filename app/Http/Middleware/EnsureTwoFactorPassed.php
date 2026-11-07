<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si la persona tiene 2FA activo y todavía no pasó el desafío en esta sesión,
 * la manda a ingresar el código. Aplica a TODOS los logins (formulario, Google, "recordarme").
 */
class EnsureTwoFactorPassed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Sin sesión iniciada o sin 2FA activo: no hay nada que controlar.
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Ya pasó el desafío (la marca guarda el id de usuario, así no sirve para otra cuenta).
        if ((int) $request->session()->get('two_factor_passed_for') === (int) $user->id) {
            return $next($request);
        }

        // Estas rutas tienen que seguir accesibles para poder completar el desafío o salir.
        if ($request->routeIs('two-factor.challenge', 'two-factor.challenge.store', 'logout')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(403, 'Falta verificar el segundo factor de autenticación.');
        }

        return redirect()->route('two-factor.challenge');
    }
}