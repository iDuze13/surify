<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Surify - Turismo Nacional')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #f8fafc; color: #1e293b; font-family: 'Outfit', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; line-height: 1; }
        #loading-screen { position: fixed; inset: 0; z-index: 9999; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.5s ease; }
        #loading-screen.oculto { opacity: 0; pointer-events: none; }
        #lang-dropdown { display: none; position: absolute; top: calc(100% + 8px); right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.10); z-index: 100; min-width: 130px; overflow: hidden; }
        #lang-dropdown.open { display: block; }
        .lang-option { display: flex; align-items: center; gap: 8px; padding: 9px 14px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; transition: background 0.15s; border: none; background: none; width: 100%; text-align: left; }
        .lang-option:hover { background: #f0f7ff; color: #28628f; }
        .lang-option.active { background: #e8f3fb; color: #28628f; }
        #traduccion-indicator { display: none; position: fixed; bottom: 80px; right: 24px; background: #28628f; color: white; padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark') { document.documentElement.classList.add('dark'); }
        else { document.documentElement.classList.remove('dark'); }
    </script>
</head>

<style>
    .acc-fab { position: fixed; bottom: 24px; left: 24px; z-index: 9998; }
    .acc-btn { width: 48px; height: 48px; border-radius: 50%; background: #28628f; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.18); transition: background 0.2s; }
    .acc-btn:hover { background: #1a4669; }
    .acc-panel { position: fixed; bottom: 84px; left: 24px; z-index: 9997; background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; width: 260px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); display: none; flex-direction: column; gap: 16px; }
    .acc-panel.open { display: flex; }
    .acc-title { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin: 0; }
    .acc-section { display: flex; flex-direction: column; gap: 8px; }
    .acc-label { font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; }
    .acc-row { display: flex; align-items: center; gap: 8px; }
    .text-btn { flex: 1; padding: 6px 0; border: 1px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; font-weight: 700; color: #28628f; transition: all 0.15s; }
    .text-btn:hover { background: #f0f7ff; border-color: #28628f; }
    .text-btn.active { background: #28628f; color: white; border-color: #28628f; }
    .reader-btn { width: 100%; padding: 10px; border-radius: 10px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 13px; font-weight: 600; color: #28628f; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s; }
    .reader-btn:hover { background: #f0f7ff; border-color: #28628f; }
    .reader-btn.active { background: #28628f; color: white; }
    .tour-btn { width: 100%; padding: 10px; border-radius: 10px; border: none; background: #28628f; color: white; cursor: pointer; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.15s; }
    .tour-btn:hover { background: #1a4669; }
    .acc-divider { height: 1px; background: #f1f5f9; margin: 0; }
    .tour-highlight { position: fixed; border: 3px solid #28628f; border-radius: 12px; z-index: 9996; box-shadow: 0 0 0 4000px rgba(0,0,0,0.45); transition: all 0.4s ease; pointer-events: none; display: none; }
    .tour-tooltip { position: fixed; z-index: 9999; background: white; border-radius: 14px; padding: 18px 20px; width: 280px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); border: 1px solid #e2e8f0; display: none; }
    .tour-tooltip h3 { margin: 0 0 6px; font-size: 15px; font-weight: 700; color: #1e293b; }
    .tour-tooltip p { margin: 0 0 14px; font-size: 13px; color: #64748b; line-height: 1.5; }
    .tour-nav { display: flex; align-items: center; justify-content: space-between; }
    .tour-step-info { font-size: 12px; color: #94a3b8; font-weight: 600; }
    .tour-next { padding: 8px 16px; background: #28628f; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
    .tour-next:hover { background: #1a4669; }
    .tour-skip { font-size: 12px; color: #94a3b8; background: none; border: none; cursor: pointer; padding: 0; }
    .tour-skip:hover { color: #64748b; }
    .reader-status { font-size: 11px; color: #94a3b8; text-align: center; display: none; }
    .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
</style>

<div class="acc-fab">
    <div class="acc-panel" id="accPanel" role="dialog" aria-label="Panel de accesibilidad">
        <p class="acc-title">♿ Accesibilidad</p>
        <div class="acc-section">
            <span class="acc-label">Tamaño de texto</span>
            <div class="acc-row">
                <button class="text-btn" onclick="surifyAccTexto('small')" id="acc-btn-small" style="font-size:12px">A−</button>
                <button class="text-btn active" onclick="surifyAccTexto('normal')" id="acc-btn-normal" style="font-size:15px">A</button>
                <button class="text-btn" onclick="surifyAccTexto('large')" id="acc-btn-large" style="font-size:18px">A+</button>
                <button class="text-btn" onclick="surifyAccTexto('xlarge')" id="acc-btn-xlarge" style="font-size:21px">A++</button>
            </div>
        </div>
        <div class="acc-divider"></div>
        <div class="acc-section">
            <span class="acc-label">Lector de pantalla</span>
            <button class="reader-btn" id="accReaderBtn" onclick="surifyToggleLector()" aria-pressed="false">
                <span>🔊</span><span id="accReaderLabel">Activar lector</span>
            </button>
            <p class="reader-status" id="accReaderStatus">Hacé clic en cualquier texto para escucharlo</p>
        </div>
        <div class="acc-divider"></div>
        <div class="acc-section">
            <span class="acc-label">Tour guiado</span>
            <button class="tour-btn" onclick="surifyIniciarTour()"><span>🗺️</span><span>Recorrer la página</span></button>
        </div>
    </div>
    <button class="acc-btn" onclick="surifyTogglePanel()" title="Accesibilidad">♿</button>
</div>

<div class="tour-highlight" id="tourHighlight"></div>
<div class="tour-tooltip" id="tourTooltip">
    <h3 id="tourTitle"></h3>
    <p id="tourDesc"></p>
    <div class="tour-nav">
        <button class="tour-skip" onclick="surifyCerrarTour()">Saltar tour</button>
        <span class="tour-step-info" id="tourStepInfo"></span>
        <button class="tour-next" id="tourNextBtn" onclick="surifySiguientePaso()">Siguiente →</button>
    </div>
</div>

<div id="traduccion-indicator">🌐 Traduciendo...</div>

<script>
(function() {
    var panelAbierto = false, lectorActivo = false, pasoActual = 0;
    var tamanos = { small: '14px', normal: '16px', large: '19px', xlarge: '22px' };
    var pasosTour = [
        { selector: 'header', titulo: '🧭 Barra de navegación', desc: 'Desde acá podés acceder a todas las secciones: Inicio, Mapa interactivo, Eventos y Combustible.' },
        { selector: '#search-container', titulo: '🔍 Buscador', desc: 'Buscá destinos, festivales o provincias. Los resultados aparecen en tiempo real.' },
        { selector: 'main', titulo: '🌎 Contenido principal', desc: 'Acá encontrás los destinos destacados, clima en tiempo real y las regiones más icónicas de Argentina.' },
        { selector: 'footer', titulo: '📌 Pie de página', desc: 'Información sobre el proyecto Surify.' },
        { selector: '.fixed.bottom-6.right-6', titulo: '🎵 Reproductor de música', desc: 'Reproducí el himno de Surify mientras explorás la plataforma.' }
    ];
    window.surifyTogglePanel = function() { panelAbierto = !panelAbierto; document.getElementById('accPanel').classList.toggle('open', panelAbierto); };
    window.surifyAccTexto = function(size) {
        document.documentElement.style.fontSize = tamanos[size];
        ['small','normal','large','xlarge'].forEach(s => document.getElementById('acc-btn-'+s).classList.toggle('active', s===size));
        localStorage.setItem('surify-text-size', size);
    };
    window.surifyToggleLector = function() {
        lectorActivo = !lectorActivo;
        var btn = document.getElementById('accReaderBtn');
        var label = document.getElementById('accReaderLabel');
        var status = document.getElementById('accReaderStatus');
        btn.classList.toggle('active', lectorActivo);
        btn.setAttribute('aria-pressed', lectorActivo);
        label.textContent = lectorActivo ? 'Desactivar lector' : 'Activar lector';
        status.style.display = lectorActivo ? 'block' : 'none';
        document.body.style.cursor = lectorActivo ? 'text' : '';
        if(lectorActivo) { document.addEventListener('click', surifyLeerElemento, true); }
        else { document.removeEventListener('click', surifyLeerElemento, true); window.speechSynthesis && window.speechSynthesis.cancel(); }
    };
    function surifyLeerElemento(e) {
        if(!lectorActivo) return;
        if(e.target.closest('#accPanel') || e.target.closest('.acc-fab')) return;
        var texto = (e.target.innerText || e.target.textContent || '').trim();
        if(texto.length > 2) {
            e.preventDefault(); e.stopPropagation();
            if(window.speechSynthesis) { window.speechSynthesis.cancel(); var utt = new SpeechSynthesisUtterance(texto); utt.lang='es-ES'; utt.rate=0.9; window.speechSynthesis.speak(utt); }
        }
    }
    window.surifyIniciarTour = function() { pasoActual = 0; surifyTogglePanel(); surifyMostrarPaso(); };
    function surifyMostrarPaso() {
        if(pasoActual >= pasosTour.length) { surifyCerrarTour(); return; }
        var paso = pasosTour[pasoActual], el = document.querySelector(paso.selector);
        var highlight = document.getElementById('tourHighlight'), tooltip = document.getElementById('tourTooltip');
        document.getElementById('tourTitle').textContent = paso.titulo;
        document.getElementById('tourDesc').textContent = paso.desc;
        document.getElementById('tourStepInfo').textContent = (pasoActual+1)+' de '+pasosTour.length;
        document.getElementById('tourNextBtn').textContent = pasoActual===pasosTour.length-1 ? 'Finalizar ✓' : 'Siguiente →';
        if(el) {
            var rect = el.getBoundingClientRect();
            highlight.style.cssText = 'display:block;top:'+(rect.top-6)+'px;left:'+(rect.left-6)+'px;width:'+(rect.width+12)+'px;height:'+(rect.height+12)+'px;';
            var tt = rect.bottom+16; if(tt+200>window.innerHeight) tt=rect.top-210; if(tt<10) tt=10;
            tooltip.style.top=tt+'px'; tooltip.style.left=Math.min(Math.max(10,rect.left),window.innerWidth-300)+'px';
            window.scrollTo({top:Math.max(0,rect.top+window.scrollY-150),behavior:'smooth'});
        }
        tooltip.style.display = 'block';
    }
    window.surifySiguientePaso = function() { pasoActual++; surifyMostrarPaso(); };
    window.surifyCerrarTour = function() { document.getElementById('tourHighlight').style.display='none'; document.getElementById('tourTooltip').style.display='none'; };
    document.addEventListener('DOMContentLoaded', function() { var s=localStorage.getItem('surify-text-size'); if(s) surifyAccTexto(s); });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) { if(entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });
});
</script>

<body class="flex flex-col min-h-screen antialiased">

    <div id="loading-screen">
        <div style="display:flex;flex-direction:column;align-items:center;gap:24px">
            <div style="display:flex;align-items:center;gap:8px">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;font-size:40px;color:#28628f">explore</span>
                <span style="font-family:'Inter',sans-serif;font-size:2.5rem;font-weight:900;color:#191c1d;letter-spacing:-0.05em">Surify</span>
            </div>
            <div style="width:192px;height:4px;background:#f1f5f9;border-radius:9999px;overflow:hidden">
                <div id="loading-bar-screen" style="height:100%;background:#28628f;border-radius:9999px;width:0%;transition:width 0.3s"></div>
            </div>
            <p style="font-size:0.75rem;font-weight:600;color:#94a3b8;letter-spacing:0.1em;text-transform:uppercase">Explorando Argentina...</p>
        </div>
    </div>

    <header class="border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-sm select-none hover:opacity-95 transition-opacity text-decoration-none">
                    <span class="material-symbols-outlined text-[24px] text-[#28628f]" style="font-variation-settings: 'FILL' 1;">explore</span>
                    <span class="text-xl font-black text-[#191c1d] tracking-tighter" style="font-family: 'Inter', sans-serif;">Surify</span>
                </a>
            </div>

            <div class="relative flex-grow max-w-md hidden md:block" id="search-container">
                <div class="flex items-center bg-slate-100 border border-transparent rounded-full px-4 py-2 gap-2 focus-within:border-[#28628f] focus-within:bg-white transition-all">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                    <input id="search-input" type="text" placeholder="Destinos, festivales, provincias..."
                        class="bg-transparent border-none p-0 focus:ring-0 text-sm text-slate-700 placeholder:text-slate-400 w-full" autocomplete="off">
                </div>
                <div id="search-dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden">
                    <div id="search-results" class="max-h-80 overflow-y-auto"></div>
                    <div id="search-empty" class="hidden px-4 py-6 text-center text-sm text-slate-400">No se encontraron resultados</div>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-1 shrink-0">
                @auth
                @php
                $user = auth()->user();
                $modoUsuario = session('modo_usuario', false);
                $esAdmin = !$modoUsuario && ($user->hasRole('admin') || $user->hasRole('Admin'));
                $permisosUsuario = $user->permisos()->pluck('nombre')->toArray();
                $tieneAlguno = fn(array $perms) => !$modoUsuario && ($esAdmin || count(array_intersect($perms, $permisosUsuario)) > 0);
                $esOperador = !$modoUsuario && ($esAdmin || count($permisosUsuario) > 0);
                @endphp

                @if(!$esOperador || $modoUsuario)
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">home</span> Inicio</a>
                <a href="{{ route('mapa.nacional') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">map</span> Mapa</a>
                <a href="{{ route('eventos.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">event</span> Eventos</a>
                <a href="{{ route('combustible.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">local_gas_station</span> Combustible</a>
                @endif

                @if($esOperador)
                <span class="h-5 w-px bg-slate-200 mx-1"></span>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">dashboard</span> Dashboard</a>
                @endif

                @if($esAdmin)
                <a href="{{ route('admin.roles.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">admin_panel_settings</span> Roles</a>
                <a href="{{ route('admin.usuarios.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">group</span> Usuarios</a>
                @endif

                @if($tieneAlguno(['crear_destino', 'modificar_destino', 'eliminar_destino', 'administrar_destinos_sugeridos']))
                <a href="{{ route('admin.destinos.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">landscape</span> Destinos</a>
                @endif

                @if($tieneAlguno(['crear_evento', 'modificar_evento', 'eliminar_evento', 'administrar_eventos_sugeridos']))
                <a href="/admin/eventos" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">celebration</span> Festivales</a>
                @endif

                @if($tieneAlguno(['administrar_resena', 'administrar_resenas', 'eliminar_comentario']))
                <a href="{{ route('admin.resenas.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">chat_bubble</span> Reseñas</a>
                @endif

                @if($tieneAlguno(['gestionar_gastronomia']))
                <a href="{{ route('admin.gastronomia.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">restaurant</span> Gastronomía</a>
                @endif

                @else
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none">Inicio</a>
                <a href="{{ route('mapa.nacional') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none">Mapa</a>
                <a href="{{ route('eventos.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] hover:bg-slate-50 px-3 py-2 rounded-lg transition-all text-decoration-none">Eventos</a>
                @endauth
            </nav>

            <div class="flex items-center gap-3 shrink-0">

                <div class="relative" id="lang-selector-container">
                    <button onclick="toggleLangDropdown()" id="lang-btn" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-[#28628f] text-slate-600 hover:text-[#28628f] transition-all bg-white text-xs font-bold">
                        <span id="lang-flag">🇦🇷</span>
                        <span id="lang-label">ES</span>
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                    </button>
                    <div id="lang-dropdown">
                        <button class="lang-option active" onclick="cambiarIdioma('es', '🇦🇷', 'ES')">🇦🇷 Español</button>
                        <button class="lang-option" onclick="cambiarIdioma('en', '🇬🇧', 'EN')">🇬🇧 English</button>
                        <button class="lang-option" onclick="cambiarIdioma('pt', '🇧🇷', 'PT')">🇧🇷 Português</button>
                    </div>
                </div>

                @auth
                <div class="flex items-center gap-3">
                    @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('Admin') || count(auth()->user()->permisos()->pluck('nombre')->toArray()) > 0)
                    <form method="POST" action="{{ route('admin.cambiar_vista') }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 hover:border-[#28628f] text-slate-500 hover:text-[#28628f] transition-all bg-white cursor-pointer flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">{{ session('modo_usuario') ? 'admin_panel_settings' : 'visibility' }}</span>
                            {{ session('modo_usuario') ? 'Vista Admin' : 'Ver como usuario' }}
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition-all text-decoration-none">
                        @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full border border-[#28628f]">
                        @else
                        <div class="w-8 h-8 rounded-full bg-[#28628f] text-white flex items-center justify-center text-sm font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        @endif
                        <span class="text-sm font-medium text-slate-700 hidden lg:block">{{ auth()->user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 hover:border-[#28628f] text-slate-500 hover:text-[#28628f] transition-all bg-white cursor-pointer">Cerrar Sesión</button>
                    </form>
                </div>
                @else
                <a href="{{ route('login') }}" class="text-xs font-semibold px-4 py-2 rounded-full border border-slate-200 hover:border-[#28628f] text-slate-600 hover:text-[#28628f] transition-all bg-white text-decoration-none">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="text-xs font-semibold px-4 py-2 rounded-full bg-[#28628f] hover:bg-[#1a4669] text-white transition-all shadow-sm text-decoration-none">Registrarse</a>
                @endauth

                <button id="mobile-menu-btn" class="md:hidden w-8 h-8 flex items-center justify-center text-slate-600 hover:text-[#28628f] bg-slate-100 rounded-lg transition-colors ml-1">
                    <span class="material-symbols-outlined text-[20px]">menu</span>
                </button>
            </div>
        </div>

        <div id="mobile-menu-panel" class="hidden md:hidden border-t border-slate-200 bg-white">
            <div class="px-4 py-3 flex flex-col gap-2">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] px-3 py-2 rounded-lg bg-slate-50 text-decoration-none">Inicio</a>
                <a href="{{ route('mapa.nacional') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] px-3 py-2 rounded-lg bg-slate-50 text-decoration-none">Mapa</a>
                <a href="{{ route('eventos.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] px-3 py-2 rounded-lg bg-slate-50 text-decoration-none">Eventos</a>
                <a href="{{ route('combustible.index') }}" class="text-sm font-semibold text-slate-700 hover:text-[#28628f] px-3 py-2 rounded-lg bg-slate-50 text-decoration-none">Combustible</a>
                @if(isset($esOperador) && $esOperador)
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#28628f] px-3 py-2 rounded-lg bg-slate-50 text-decoration-none">Panel / Admin</a>
                @endif
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('status'))
        <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-[#28628f] text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">info</span>{{ session('status') }}
        </div>
        @endif
        @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-sm">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 text-sm">⚠️ {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} Surify. Todos los derechos reservados. Desarrollado con 💖 por Casasola, Gómez y Martínez para la UCP 🌸.</p>
        </div>
    </footer>

    <div class="fixed bottom-6 right-6 z-50 flex items-center bg-white border border-slate-200 p-3 rounded-full shadow-xl gap-3 max-w-xs transition-all duration-300 hover:border-[#28628f]">
        <button id="global-music-btn" class="w-10 h-10 rounded-full bg-[#28628f] text-white flex items-center justify-center hover:bg-[#1a4669] transition-transform active:scale-95 shadow-sm">
            <span id="global-music-icon" class="material-symbols-outlined">play_arrow</span>
        </button>
        <div class="flex flex-col pr-4 select-none">
            <span class="text-xs font-bold text-slate-800 leading-tight truncate max-w-[120px]">Himno Surify</span>
            <span id="global-music-status" class="text-[10px] text-slate-400 font-semibold tracking-wide">Pausado</span>
        </div>
        <audio id="global-surify-song" loop>
            <source src="{{ asset('audio/cancion_surify.mpeg') }}" type="audio/mpeg">
        </audio>
    </div>

    <script>
    (function() {
        const bar = document.getElementById('loading-bar-screen');
        const screen = document.getElementById('loading-screen');
        let progress = 0, oculto = false;
        const interval = setInterval(() => { progress += Math.random()*15; if(progress>85) progress=85; bar.style.width=progress+'%'; }, 100);
        function ocultarPantalla() {
            if(oculto) return; oculto=true; clearInterval(interval); bar.style.width='100%';
            setTimeout(() => { screen.classList.add('oculto'); setTimeout(() => { if(screen.parentNode) screen.remove(); }, 500); }, 200);
        }
        document.addEventListener('DOMContentLoaded', ocultarPantalla);
        setTimeout(ocultarPantalla, 5000);
    })();

    document.addEventListener('DOMContentLoaded', function() {
        const audio = document.getElementById('global-surify-song');
        const btn = document.getElementById('global-music-btn');
        const icon = document.getElementById('global-music-icon');
        const status = document.getElementById('global-music-status');
        setInterval(function() { if(audio && !audio.paused) { localStorage.setItem('surify-music-time', audio.currentTime); localStorage.setItem('surify-music-playing','true'); } }, 1000);
        audio.addEventListener('loadedmetadata', function() {
            const savedTime = parseFloat(localStorage.getItem('surify-music-time')||'0');
            const wasPlaying = localStorage.getItem('surify-music-playing')==='true';
            if(savedTime>0) audio.currentTime=savedTime;
            if(wasPlaying) { audio.play().then(()=>{ icon.textContent='pause'; status.textContent='Reproduciendo'; status.className="text-[10px] text-emerald-500 font-bold animate-pulse tracking-wide"; }).catch(()=>{ icon.textContent='play_arrow'; status.textContent='Continuar Himno'; status.className="text-[10px] text-amber-500 font-bold tracking-wide animate-pulse"; }); }
        }, {once:true});
        btn.addEventListener('click', function() {
            if(audio.paused) { audio.play().then(()=>{ icon.textContent='pause'; status.textContent='Reproduciendo'; status.className="text-[10px] text-emerald-500 font-bold animate-pulse tracking-wide"; localStorage.setItem('surify-music-playing','true'); }); }
            else { audio.pause(); icon.textContent='play_arrow'; status.textContent='Pausado'; status.className="text-[10px] text-slate-400 font-semibold tracking-wide"; localStorage.setItem('surify-music-playing','false'); localStorage.removeItem('surify-music-time'); }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('submit', function(e) {
            if(e.target && e.target.classList.contains('form-eliminar')) {
                e.preventDefault();
                const form = e.target;
                Swal.fire({ title: form.dataset.title||'¿Estás seguro?', text: form.dataset.text||'¡No vas a poder revertir esto!', icon:'warning', showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6', confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar' })
                .then(result => { if(result.isConfirmed) form.submit(); });
            }
        });
    });
    </script>

    <script>
    (function() {
        const input = document.getElementById('search-input');
        const dropdown = document.getElementById('search-dropdown');
        const resultsContainer = document.getElementById('search-results');
        const emptyMsg = document.getElementById('search-empty');
        let debounceTimer = null;
        if(!input) return;
        input.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);
            if(query.length<2) { dropdown.classList.add('hidden'); return; }
            debounceTimer = setTimeout(() => {
                fetch(`{{ route('busqueda.buscar') }}?q=${encodeURIComponent(query)}`).then(r=>r.json()).then(data => {
                    resultsContainer.innerHTML='';
                    if(data.resultados.length===0) { emptyMsg.classList.remove('hidden'); }
                    else {
                        emptyMsg.classList.add('hidden');
                        data.resultados.forEach(item => {
                            const a = document.createElement('a');
                            a.href=item.url; a.className='flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0 text-decoration-none';
                            a.innerHTML=`<span class="material-symbols-outlined text-[#28628f] text-[20px]">${item.icono}</span><div class="min-w-0"><p class="text-sm font-bold text-slate-700 truncate">${item.nombre}</p><p class="text-xs text-slate-400">${item.tipo}${item.subtitulo?' • '+item.subtitulo:''}</p></div>`;
                            resultsContainer.appendChild(a);
                        });
                    }
                    dropdown.classList.remove('hidden');
                }).catch(()=>dropdown.classList.add('hidden'));
            }, 300);
        });
        document.addEventListener('click', function(e) { if(!document.getElementById('search-container').contains(e.target)) dropdown.classList.add('hidden'); });
        input.addEventListener('keydown', function(e) { if(e.key==='Escape') { dropdown.classList.add('hidden'); input.blur(); } });
    })();
    </script>

    <script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu-panel').classList.toggle('hidden');
    });
    </script>

    {{-- SISTEMA DE TRADUCCIÓN CON MYMEMORY --}}
    <script>
    (function() {
        const SKIP_TAGS = ['SCRIPT','STYLE','NOSCRIPT','META','LINK','INPUT','TEXTAREA','SELECT','OPTION','CODE','PRE'];
        const SKIP_CLASSES = ['material-symbols-outlined','fa','fas','far'];

        let idiomaActual = localStorage.getItem('surify-idioma') || 'es';
        let textoOriginales = new Map();
        let traduccionCache = {};

        document.addEventListener('DOMContentLoaded', function() {
            const saved = localStorage.getItem('surify-idioma') || 'es';
            actualizarBotonIdioma(saved);
            if(saved !== 'es') traducirPagina(saved);
        });

        document.addEventListener('click', function(e) {
            if(!document.getElementById('lang-selector-container').contains(e.target)) {
                document.getElementById('lang-dropdown').classList.remove('open');
            }
        });

        window.toggleLangDropdown = function() {
            document.getElementById('lang-dropdown').classList.toggle('open');
        };

        window.cambiarIdioma = function(idioma, bandera, abrev) {
            document.getElementById('lang-dropdown').classList.remove('open');
            actualizarBotonIdioma(idioma, bandera, abrev);

            if(idioma === 'es') {
                textoOriginales.forEach((original, nodo) => { nodo.textContent = original; });
                textoOriginales.clear();
                localStorage.setItem('surify-idioma', 'es');
                idiomaActual = 'es';
                guardarIdiomaBackend('es');
                return;
            }

            localStorage.setItem('surify-idioma', idioma);
            idiomaActual = idioma;
            guardarIdiomaBackend(idioma);
            traducirPagina(idioma);
        };

        function actualizarBotonIdioma(idioma, bandera, abrev) {
            const flags = { es:'🇦🇷', en:'🇬🇧', pt:'🇧🇷' };
            const labels = { es:'ES', en:'EN', pt:'PT' };
            document.getElementById('lang-flag').textContent = bandera || flags[idioma] || '🌐';
            document.getElementById('lang-label').textContent = abrev || labels[idioma] || idioma.toUpperCase();
            document.querySelectorAll('.lang-option').forEach(btn => {
                btn.classList.remove('active');
                if(btn.getAttribute('onclick').includes("'"+idioma+"'")) btn.classList.add('active');
            });
        }

        async function traducirTexto(texto, target) {
            const cacheKey = texto + '_' + target;
            if(traduccionCache[cacheKey]) return traduccionCache[cacheKey];
            try {
                const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(texto)}&langpair=es|${target}`;
                const response = await fetch(url);
                if(response.ok) {
                    const data = await response.json();
                    const traducido = data.responseData?.translatedText || texto;
                    traduccionCache[cacheKey] = traducido;
                    return traducido;
                }
            } catch(e) {}
            return texto;
        }

        async function traducirPagina(idiomaDestino) {
            const indicator = document.getElementById('traduccion-indicator');
            indicator.style.display = 'block';

            const nodos = obtenerNodosTexto(document.body);
            const nodosParaTraducir = [];

            nodos.forEach(nodo => {
                const texto = nodo.textContent.trim();
                if(texto.length < 2) return;
                if(!textoOriginales.has(nodo)) textoOriginales.set(nodo, nodo.textContent);
                nodosParaTraducir.push({ nodo, texto });
            });

            // Traducir TODO primero en lotes de 10, sin aplicar cambios todavía
            const BATCH_SIZE = 10;
            const resultados = new Map();

            for(let i = 0; i < nodosParaTraducir.length; i += BATCH_SIZE) {
                const lote = nodosParaTraducir.slice(i, i + BATCH_SIZE);
                await Promise.all(lote.map(async ({ nodo, texto }) => {
                    const traducido = await traducirTexto(texto, idiomaDestino);
                    resultados.set(nodo, { texto, traducido });
                }));
            }

            // Aplicar TODOS los cambios de golpe al final
            resultados.forEach(({ texto, traducido }, nodo) => {
                nodo.textContent = nodo.textContent.replace(texto, traducido);
            });

            indicator.style.display = 'none';
        }

        function obtenerNodosTexto(elemento) {
            const nodos = [];
            const walker = document.createTreeWalker(elemento, NodeFilter.SHOW_TEXT, {
                acceptNode: function(node) {
                    const padre = node.parentElement;
                    if(!padre) return NodeFilter.FILTER_REJECT;
                    if(SKIP_TAGS.includes(padre.tagName)) return NodeFilter.FILTER_REJECT;
                    if(SKIP_CLASSES.some(cls => padre.classList.contains(cls))) return NodeFilter.FILTER_REJECT;
                    if(padre.closest('script, style, .material-symbols-outlined')) return NodeFilter.FILTER_REJECT;
                    if(node.textContent.trim().length < 2) return NodeFilter.FILTER_REJECT;
                    return NodeFilter.FILTER_ACCEPT;
                }
            });
            let nodo;
            while((nodo = walker.nextNode())) nodos.push(nodo);
            return nodos;
        }

        function guardarIdiomaBackend(idioma) {
            fetch('{{ route("idioma.cambiar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ idioma })
            }).catch(() => {});
        }
    })();
    </script>

</body>
</html>