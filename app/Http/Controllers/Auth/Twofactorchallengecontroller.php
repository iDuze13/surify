<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    /** Intentos fallidos permitidos y tiempo de bloqueo (segundos). */
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_SEGUNDOS = 900;

    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        $yaPaso = (int) $request->session()->get('two_factor_passed_for') === (int) $user->id;

        if (!$user->hasTwoFactorEnabled() || $yaPaso) {
            return redirect()->route($user->rutaInicial());
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $user = $request->user();
        $clave = 'two-factor:' . $user->id;

        // El límite es por usuario y se guarda en cache: reiniciar sesión no lo reinicia.
        if (RateLimiter::tooManyAttempts($clave, self::MAX_INTENTOS)) {
            $minutos = (int) ceil(RateLimiter::availableIn($clave) / 60);

            throw ValidationException::withMessages([
                'code' => "Demasiados intentos fallidos. Probá de nuevo en {$minutos} minuto(s).",
            ]);
        }

        $codigo = trim($request->input('code'));

        if (preg_match('/^\d{3}\s?\d{3}$/', $codigo)) {
            // Código de 6 dígitos de la app autenticadora (tolera ±30 segundos de desfasaje)
            $valido = (bool) (new Google2FA())->verifyKey(
                $user->two_factor_secret,
                preg_replace('/\s+/', '', $codigo),
                1
            );
        } else {
            // Si no son 6 dígitos, se interpreta como código de recuperación (un solo uso)
            $valido = $user->consumirCodigoDeRecuperacion($codigo);
        }

        if (!$valido) {
            RateLimiter::hit($clave, self::BLOQUEO_SEGUNDOS);

            throw ValidationException::withMessages([
                'code' => 'El código no es válido.',
            ]);
        }

        RateLimiter::clear($clave);

        // Cambio de privilegio: renovamos el id de sesión y marcamos el desafío como superado.
        $request->session()->regenerate();
        $request->session()->put('two_factor_passed_for', $user->id);

        return redirect()->route($user->rutaInicial());
    }
}