<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TraduccionController extends Controller
{
    public function cambiarIdioma(Request $request)
    {
        $idioma = $request->idioma;
        $idiomasPermitidos = ['es', 'en', 'pt'];

        if (!in_array($idioma, $idiomasPermitidos)) {
            return back()->with('error', 'Idioma no soportado.');
        }

        if (Auth::check()) {
            Auth::user()->update(['idioma' => $idioma]);
        }

        session(['idioma' => $idioma]);

        return back()->with('success', 'Idioma actualizado.');
    }

    public function traducirTexto(Request $request)
    {
        $texto = $request->input('q', '');
        $target = $request->input('target', 'en');

        if (empty($texto) || $target === 'es') {
            return response()->json(['translatedText' => $texto]);
        }

        $cacheKey = 'trad_' . md5($texto . $target);

        $traducido = Cache::remember($cacheKey, now()->addDays(30), function () use ($texto, $target) {
            $instancias = [
                'https://libretranslate.de/translate',
                'https://translate.argosopentech.com/translate',
                'https://libretranslate.com/translate',
            ];

            foreach ($instancias as $url) {
                try {
                    $response = Http::timeout(8)->post($url, [
                        'q'       => $texto,
                        'source'  => 'es',
                        'target'  => $target,
                        'format'  => 'text',
                        'api_key' => env('LIBRETRANSLATE_API_KEY', ''),
                    ]);

                    if ($response->successful() && isset($response->json()['translatedText'])) {
                        return $response->json()['translatedText'];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $texto;
        });

        return response()->json(['translatedText' => $traducido]);
    }

    public static function traducir(string $texto, string $idiomaDestino): string
    {
        if ($idiomaDestino === 'es' || empty($texto)) {
            return $texto;
        }

        $cacheKey = 'traduccion_' . md5($texto . $idiomaDestino);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($texto, $idiomaDestino) {
            $instancias = [
                'https://libretranslate.de/translate',
                'https://translate.argosopentech.com/translate',
                'https://libretranslate.com/translate',
            ];

            foreach ($instancias as $url) {
                try {
                    $response = Http::timeout(8)->post($url, [
                        'q'       => $texto,
                        'source'  => 'es',
                        'target'  => $idiomaDestino,
                        'format'  => 'text',
                        'api_key' => '',
                    ]);

                    if ($response->successful() && $response->json('translatedText')) {
                        return $response->json('translatedText');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $texto;
        });
    }
}