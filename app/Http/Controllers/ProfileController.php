<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $user = $request->user();
        $avatarAnterior = $user->avatar;

        $url = $this->cloudinary->subirImagen(
            $request->file('avatar')->getRealPath(),
            'surify/avatars'
        );

        $user->update(['avatar' => $url]);

        // Borramos el avatar anterior de Cloudinary, si existía
        if ($avatarAnterior) {
            $publicId = $this->extraerPublicId($avatarAnterior);
            if ($publicId) {
                $this->cloudinary->eliminarImagen($publicId);
            }
        }

        return Redirect::route('profile.edit')->with('success', 'Foto de perfil actualizada');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Extrae el public_id de Cloudinary a partir de la URL guardada,
     * necesario para poder borrar la imagen del storage remoto.
     */
    private function extraerPublicId(string $url): ?string
    {
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}