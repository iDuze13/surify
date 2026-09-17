<?php

use App\Http\Controllers\DestinoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\CombustibleController;
use App\Http\Controllers\EventoVisitadoController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Admin\DestinoController as AdminDestinoController;
use App\Http\Controllers\Admin\ResenaController as AdminResenaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\EventoController as AdminEventoController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GastronomiaController as AdminGastronomiaController;
use App\Http\Controllers\Admin\ProvinciaController as AdminProvinciaController;


// Solo esta línea limpia tiene que quedar en tu web.php:
Route::post('/cambiar-modo-vista', [App\Http\Controllers\Admin\RolController::class, 'cambiarModoVista'])->name('admin.cambiar_vista');
Route::post('/idioma', [\App\Http\Controllers\TraduccionController::class, 'cambiarIdioma'])->name('idioma.cambiar');
Route::post('/traducir', [\App\Http\Controllers\TraduccionController::class, 'traducirTexto'])->name('traducir');
Route::put('/resenas/{resena}', [ResenaController::class, 'update'])->name('resenas.update');
Route::delete('/resenas/{resena}', [ResenaController::class, 'destroy'])->name('resenas.destroy');
Route::view('/terminos-y-condiciones', 'legal.terminos')->name('terminos');
Route::post('/admin/backup', [\App\Http\Controllers\Admin\BackupController::class, 'generar'])->name('admin.backup');

// ==================== ADMIN ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RolController::class);
    Route::resource('usuarios', UsuarioController::class)->only(['index', 'edit', 'update']);
});

Route::middleware(['auth', 'permiso:crear_evento,modificar_evento,eliminar_evento,administrar_eventos_sugeridos'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/eventos', [AdminEventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/create', [AdminEventoController::class, 'create'])->name('eventos.create');
    Route::post('/eventos', [AdminEventoController::class, 'store'])->name('eventos.store');
    Route::get('/eventos/{evento}/edit', [AdminEventoController::class, 'edit'])->name('eventos.edit');
    Route::put('/eventos/{evento}', [AdminEventoController::class, 'update'])->name('eventos.update');
    Route::delete('/eventos/{evento}', [AdminEventoController::class, 'destroy'])->name('eventos.destroy');
});

Route::middleware(['auth', 'permiso:crear_destino,modificar_destino,eliminar_destino,administrar_destinos_sugeridos'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('destinos', AdminDestinoController::class);
});

Route::middleware(['auth', 'permiso:administrar_resenas'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/resenas', [AdminResenaController::class, 'index'])->name('resenas.index');
    Route::patch('/resenas/{resena}/aprobar', [AdminResenaController::class, 'aprobar'])->name('resenas.aprobar');
    Route::patch('/resenas/{resena}/rechazar', [AdminResenaController::class, 'rechazar'])->name('resenas.rechazar');
    Route::delete('/resenas/{resena}', [AdminResenaController::class, 'destroy'])->name('resenas.destroy');
});

Route::middleware(['auth', 'permiso:gestionar_gastronomia'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/gastronomia', [AdminGastronomiaController::class, 'index'])->name('gastronomia.index');
    Route::post('/gastronomia', [AdminGastronomiaController::class, 'store'])->name('gastronomia.store');
    Route::put('/gastronomia/{gastronomia}', [AdminGastronomiaController::class, 'update'])->name('gastronomia.update');
    Route::delete('/gastronomia/{gastronomia}', [AdminGastronomiaController::class, 'destroy'])->name('gastronomia.destroy');
    Route::put('/provincias/{provincia}', [AdminProvinciaController::class, 'update'])->name('provincias.update');
    Route::delete('/provincias/imagenes/{imagen}', [AdminProvinciaController::class, 'destroyImagen'])->name('provincias.imagenes.destroy');
});

// ==================== PÚBLICO ====================
Route::get('/', function () {
    $destinos = \App\Models\Destino::where('activo', true)
        ->with('provincia')
        ->get()
        ->map(function ($d) {
            $coords = DB::select('SELECT ST_Y(ubicacion::geometry) as lat, ST_X(ubicacion::geometry) as lng FROM destinos WHERE id = ?', [$d->id])[0];
            return [
                'id' => $d->id,
                'nombre' => $d->nombre,
                'descripcion' => $d->descripcion,
                'categoria' => $d->categoria,
                'imagen_url' => $d->imagen_url,
                'provincia' => $d->provincia->nombre ?? '',
                'lat' => $coords->lat,
                'lng' => $coords->lng,
            ];
        });

    $ciudadesRepresentativas = [
        ['provincia' => 'Patagonia', 'ciudad' => 'Bariloche', 'lat' => -41.1335, 'lng' => -71.3103],
        ['provincia' => 'Cuyo', 'ciudad' => 'Mendoza', 'lat' => -32.8908, 'lng' => -68.8458],
        ['provincia' => 'Norte', 'ciudad' => 'Salta', 'lat' => -24.7821, 'lng' => -65.4117],
        ['provincia' => 'Pampeana', 'ciudad' => 'Buenos Aires', 'lat' => -34.6037, 'lng' => -58.3816],
        ['provincia' => 'Litoral', 'ciudad' => 'Iguazú', 'lat' => -25.5991, 'lng' => -54.5736],
    ];

    $weatherService = app(\App\Services\WeatherService::class);
    $climaData = [];
    foreach ($ciudadesRepresentativas as $c) {
        $data = null;
        try {
            $data = $weatherService->getCurrentWeather($c['lat'], $c['lng']);
        } catch (\Exception $e) {
            // Ignorar errores de conexión/API
        }

        if ($data && isset($data['main']['temp']) && isset($data['weather'][0]['icon'])) {
            $climaData[] = [
                'provincia' => $c['provincia'],
                'ciudad' => $c['ciudad'],
                'temp' => $data['main']['temp'],
                'icono' => $data['weather'][0]['icon']
            ];
        } else {
            // Fallback en caso de falla
            $mockTemps = [
                'Bariloche' => 2,
                'Mendoza' => 24,
                'Salta' => 28,
                'Buenos Aires' => 18,
                'Iguazú' => 30
            ];
            $mockIcons = [
                'Bariloche' => '13d',
                'Mendoza' => '01d',
                'Salta' => '02d',
                'Buenos Aires' => '03d',
                'Iguazú' => '10d'
            ];
            $climaData[] = [
                'provincia' => $c['provincia'],
                'ciudad' => $c['ciudad'],
                'temp' => $mockTemps[$c['ciudad']] ?? 15,
                'icono' => $mockIcons[$c['ciudad']] ?? '01d'
            ];
        }
    }

    return view('home', compact('destinos', 'climaData'));
})->name('home');

Route::get('/mapa', function () {
    $provincias = \App\Models\Provincia::all();
    $destinos = \App\Models\Destino::where('activo', true)->whereNotNull('ubicacion')->with('provincia')->get()->map(function ($d) {
        $coords = DB::select('SELECT ST_Y(ubicacion::geometry) as lat, ST_X(ubicacion::geometry) as lng FROM destinos WHERE id = ?', [$d->id])[0];
        return [
            'id' => $d->id,
            'nombre' => $d->nombre,
            'descripcion' => $d->descripcion,
            'categoria' => $d->categoria,
            'imagen_url' => $d->imagen_url,
            'provincia' => $d->provincia->nombre ?? '',
            'lat' => $coords->lat,
            'lng' => $coords->lng,
        ];
    });
    return view('mapa_nacional', compact('provincias', 'destinos'));
})->name('mapa.nacional');

Route::get('/provincia/{nombre}', [DestinoController::class, 'porProvincia'])->name('provincia.show');
Route::get('/destinos/{id}', [DestinoController::class, 'show'])->name('destinos.show');

Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [EventoController::class, 'show'])->name('eventos.show');

Route::get('/buscar', [\App\Http\Controllers\BusquedaController::class, 'buscar'])->name('busqueda.buscar');

// ==================== AUTH ====================
Route::middleware('auth')->group(function () {
    Route::post('/resenas', [ResenaController::class, 'store'])->name('resenas.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    Route::get('/herramientas/combustible', [CombustibleController::class, 'index'])->name('combustible.index');
    Route::post('/eventos/sugerir', [\App\Http\Controllers\EventoSugerenciaController::class, 'store'])->name('eventos.sugerir');

    Route::post('/eventos/visitados/toggle', [EventoVisitadoController::class, 'toggle'])->name('eventos.visitados.toggle');

});

// ==================== DASHBOARD ====================
Route::get('/dashboard', function () {
    $provincias = \App\Models\Provincia::withCount('destinos')->with('imagenes')->get();
    $countDestinos = \App\Models\Destino::count();
    $countUsuarios = \App\Models\User::count();
    $countEventos = \App\Models\Evento::count();
    $countRoles = \App\Models\Role::count();
    $countPermisos = \App\Models\Permiso::count();

    return view('dashboard', compact(
        'provincias',
        'countDestinos',
        'countUsuarios',
        'countEventos',
        'countRoles',
        'countPermisos'
    ));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // ... las rutas que ya tenés
    Route::post('/favoritos/toggle', [\App\Http\Controllers\FavoritoController::class, 'toggle'])->name('favoritos.toggle');
});

Route::post('/visitados/toggle', [\App\Http\Controllers\DestinoVisitadoController::class, 'toggle'])->name('visitados.toggle');
require __DIR__ . '/auth.php';

