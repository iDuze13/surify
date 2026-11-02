<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    //	Redirige a Google, y en el callback busca o crea el usuario por su google_id
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja el callback de Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscar si ya existe un usuario con ese google_id o con ese email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Si el usuario existe pero no tenía google_id, se lo asociamos y actualizamos avatar
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Crear nuevo usuario si no existe
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null, // null ya que entra por OAuth
                ]);

                // Asignar el rol de 'Turista' por defecto
                $turistaRole = Role::where('nombre', 'Turista')->first();
                if ($turistaRole) {
                    $user->roles()->attach($turistaRole->id);
                }
            }

            // Marcar email como verificado ya que viene de Google
            if (!$user->email_verified_at) {
                $user->update([
                    'email_verified_at' => now(),
                ]);
            }

            // Iniciar sesión
            Auth::login($user);

            return redirect()->route('home')->with('success', '¡Sesión iniciada con Google correctamente!');

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Hubo un problema al iniciar sesión con Google: ' . $e->getMessage());
        }
    }
}
