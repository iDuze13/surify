<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Pantalla de configuración: QR para activar, o estado + opciones si ya está activo.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $datos = [
            'activado' => $user->hasTwoFactorEnabled(),
            'codigosRestantes' => count($user->two_factor_recovery_codes ?? []),
            'codigosNuevos' => session('recovery_codes'),
            'qr' => null,
            'secreto' => null,
        ];

        if (!$datos['activado']) {
            $google2fa = new Google2FA();

            // El secreto pendiente vive en la sesión hasta que se confirme con un código.
            $secreto = $request->session()->get('two_factor_pending_secret');
            if (!$secreto) {
                $secreto = $google2fa->generateSecretKey(32);
                $request->session()->put('two_factor_pending_secret', $secreto);
            }

            $url = $google2fa->getQRCodeUrl('Surify', $user->email, $secreto);

            // QR en SVG (no necesita imagick ni gd) incrustado como data URI
            $svg = (new Writer(new ImageRenderer(new RendererStyle(220), new SvgImageBackEnd())))
                ->writeString($url);

            $datos['qr'] = 'data:image/svg+xml;base64,' . base64_encode($svg);
            $datos['secreto'] = $secreto;
        }

        return view('profile.two-factor', $datos);
    }

    /**
     * Confirma el primer código y activa el 2FA.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();
        $secreto = $request->session()->get('two_factor_pending_secret');

        if (!$secreto) {
            return redirect()->route('two-factor.setup');
        }

        if (!(new Google2FA())->verifyKey($secreto, $request->input('code'), 1)) {
            throw ValidationException::withMessages([
                'code' => 'El código no coincide. Revisá que la hora de tu celular esté en automático e intentá de nuevo.',
            ]);
        }

        $codigos = $user->generarCodigosDeRecuperacion();
        $user->two_factor_secret = $secreto;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('two_factor_pending_secret');
        // Recién lo configuró: no hace falta pedirle el código de nuevo en esta sesión.
        $request->session()->put('two_factor_passed_for', $user->id);

        return redirect()->route('two-factor.setup')
            ->with('recovery_codes', $codigos)
            ->with('status', 'Autenticación de dos factores activada. Guardá tus códigos de recuperación.');
    }

    /**
     * Genera un juego nuevo de códigos de recuperación (invalida los anteriores).
     */
    public function regenerate(Request $request): RedirectResponse
    {
        $request->validateWithBag('regenerar', [
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('two-factor.setup');
        }

        $codigos = $user->generarCodigosDeRecuperacion();
        $user->save();

        return redirect()->route('two-factor.setup')
            ->with('recovery_codes', $codigos)
            ->with('status', 'Generamos códigos de recuperación nuevos. Los anteriores ya no sirven.');
    }

    /**
     * Desactiva el 2FA (pide la contraseña actual).
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validateWithBag('desactivar', [
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $request->session()->forget(['two_factor_passed_for', 'two_factor_pending_secret']);

        return redirect()->route('two-factor.setup')
            ->with('status', 'Autenticación de dos factores desactivada.');
    }
}