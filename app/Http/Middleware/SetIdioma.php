<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetIdioma
{
    public function handle(Request $request, Closure $next): Response
    {
        $idioma = 'es'; // por defecto

        if (Auth::check()) {
            $usuario = Auth::user();

            if ($usuario && !empty($usuario->idioma)) {
                $idioma = $usuario->idioma;
            }
        } elseif (session()->has('idioma')) {
            $idioma = session('idioma');
        }

        app()->setLocale($idioma);
        session(['idioma' => $idioma]);

        return $next($request);
    }
}