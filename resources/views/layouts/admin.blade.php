<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Surify - Panel de Administración')</title>

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
        :root {
            --bg-body: #f8fafc;
            --text-body: #1e293b;
        }

        .dark {
            --bg-body: #0f172a;
            --text-body: #e2e8f0;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-body);
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
        }

        /* ===== Modo oscuro global para componentes repetidos (cards, paneles, etc.) ===== */
        .dark .bg-white {
            background-color: #1e293b !important;
        }

        .dark .bg-slate-50 {
            background-color: #0f172a !important;
        }

        .dark .bg-slate-100 {
            background-color: #1e293b !important;
        }

        .dark .bg-slate-200 {
            background-color: #334155 !important;
        }

        .dark .text-slate-700,
        .dark .text-slate-800,
        .dark .text-slate-900 {
            color: #e2e8f0 !important;
        }

        .dark .text-slate-400,
        .dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        .dark .text-slate-600 {
            color: #cbd5e1 !important;
        }

        .dark .text-slate-300 {
            color: #64748b !important;
        }

        .dark .border-slate-100,
        .dark .border-slate-200 {
            border-color: #334155 !important;
        }

        .dark .border-slate-300 {
            border-color: #475569 !important;
        }

        .dark .shadow-sm,
        .dark .shadow-xl,
        .dark .shadow-lg {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
        }

        .dark .shadow-2xl {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6) !important;
        }

        .dark .hover\:bg-slate-50:hover,
        .dark .hover\:bg-slate-100:hover {
            background-color: #334155 !important;
        }

        .dark input,
        .dark select,
        .dark textarea {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #64748b !important;
        }

        .logo-pill,
        .dark .logo-pill {
            background-color: #f8fafc !important;
        }

        /* ===== Sidebar ===== */
        #admin-sidebar {
            width: 260px;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }

        #admin-sidebar.open {
            transform: translateX(0);
        }

        @media (min-width: 1024px) {
            #admin-sidebar {
                transform: translateX(0);
                position: sticky;
                top: 0;
                height: 100vh;
            }
        }

        #sidebar-overlay {
            display: none;
        }

        #sidebar-overlay.open {
            display: block;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s;
        }

        .side-link:hover {
            background: #f1f5f9;
            color: #28628f;
        }

        .side-link.active {
            background: #e8f3fb;
            color: #28628f;
        }

        .dark .side-link {
            color: #cbd5e1;
        }

        .dark .side-link:hover {
            background: #334155;
            color: #97ccfe;
        }

        .dark .side-link.active {
            background: #1e3a52;
            color: #97ccfe;
        }

        /* ===== Notificaciones ===== */
        #notif-dropdown {
            display: none;
        }

        #notif-dropdown.open {
            display: block;
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="antialiased">

    <div id="top-loading-bar"></div>

    <div class="flex min-h-screen">

        {{-- ========== SIDEBAR ========== --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-30 lg:hidden" onclick="toggleSidebar(false)"></div>

        <aside id="admin-sidebar" class="fixed lg:sticky inset-y-0 left-0 z-40 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700 flex flex-col shrink-0">
            <div class="h-16 flex items-center px-4 border-b border-slate-200 dark:border-slate-700 shrink-0">
                <a href="{{ route('home') }}" class="logo-pill flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm select-none hover:opacity-95 transition-opacity text-decoration-none">
                    <span class="material-symbols-outlined text-[22px] text-[#28628f]" style="font-variation-settings: 'FILL' 1;">explore</span>
                    <span class="text-lg font-black text-[#191c1d] tracking-tighter" style="font-family: 'Inter', sans-serif;">Surify</span>
                </a>
                <button onclick="toggleSidebar(false)" class="ml-auto lg:hidden w-8 h-8 flex items-center justify-center text-slate-500 hover:text-[#28628f]">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 flex flex-col gap-1">
                @auth
                @if(!session('modo_usuario'))

                @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('admin'))
                <a href="{{ route('dashboard') }}" class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span> Dashboard
                </a>
                @endif

                @can('administrar_roles')
                <a href="{{ route('admin.roles.index') }}" class="side-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span> Roles
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="side-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">group</span> Usuarios
                </a>
                <a href="{{ route('admin.anuncios.create') }}" class="side-link {{ request()->routeIs('admin.anuncios.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">campaign</span> Anuncios
                </a>
                <a href="{{ route('admin.reportes.index') }}" class="side-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">summarize</span> Reportes
                </a>
                @endcan

                @if(auth()->user()->can('crear_destino') || auth()->user()->can('modificar_destino') || auth()->user()->can('eliminar_destino') || auth()->user()->can('administrar_destinos_sugeridos'))
                <a href="{{ route('admin.destinos.index') }}" class="side-link {{ request()->routeIs('admin.destinos.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">landscape</span> Destinos
                </a>
                @endif

                @if(auth()->user()->can('crear_evento') || auth()->user()->can('modificar_evento') || auth()->user()->can('eliminar_evento') || auth()->user()->can('administrar_eventos_sugeridos'))
                <a href="{{ route('admin.eventos.index') }}" data-native-link="true" class="side-link {{ request()->routeIs('admin.eventos.*') && !request()->routeIs('admin.eventos.sugerencias') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">celebration</span> Eventos
                </a>
                @endif

                @can('administrar_eventos_sugeridos')
                @php $sugerenciasPendientes = \App\Models\Evento::sugerencias('pendiente')->count(); @endphp
                <a href="{{ route('admin.eventos.sugerencias') }}" class="side-link {{ request()->routeIs('admin.eventos.sugerencias') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span> Sugerencias
                    @if($sugerenciasPendientes > 0)
                    <span class="ml-auto min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $sugerenciasPendientes }}</span>
                    @endif
                </a>
                @endcan

                @if(auth()->user()->can('administrar_resenas') || auth()->user()->can('administrar_reseñas') || auth()->user()->can('eliminar_comentario'))
                <a href="{{ route('admin.resenas.index') }}" class="side-link {{ request()->routeIs('admin.resenas.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">chat_bubble</span> Reseñas
                </a>
                @endif

                @if(auth()->user()->can('gestionar_gastronomia'))
                <a href="{{ route('admin.gastronomia.index') }}" class="side-link {{ request()->routeIs('admin.gastronomia.*') ? 'active' : '' }}">
                    <span class="material-symbols-outlined text-[18px]">restaurant</span> Gastronomía
                </a>
                @endif

                @else

                {{-- Modo usuario: links públicos para no dejar el sidebar vacío --}}
                <a href="{{ route('home') }}" class="side-link">
                    <span class="material-symbols-outlined text-[18px]">home</span> Inicio
                </a>
                <a href="{{ route('mapa.nacional') }}" class="side-link">
                    <span class="material-symbols-outlined text-[18px]">map</span> Mapa
                </a>
                <a href="{{ route('eventos.index') }}" class="side-link">
                    <span class="material-symbols-outlined text-[18px]">event</span> Eventos
                </a>
                <a href="{{ route('combustible.index') }}" class="side-link">
                    <span class="material-symbols-outlined text-[18px]">local_gas_station</span> Combustible
                </a>

                @endif
                @endauth
            </nav>

            @auth
            @php
            $authUser = auth()->user();
            $puedeCambiarVista = $authUser->hasRole('admin')
            || $authUser->hasRole('Admin')
            || $authUser->hasRole('AdministradorFestivales')
            || count($authUser->permisos()->pluck('nombre')->toArray()) > 0;
            @endphp
            @if($puedeCambiarVista)
            <div class="p-3 border-t border-slate-200 dark:border-slate-700 shrink-0">
                <form method="POST" action="{{ route('admin.cambiar_vista') }}">
                    @csrf
                    @if(session('modo_usuario'))
                    <input type="hidden" name="modo" value="admin">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-2.5 py-2 rounded-lg transition-all border-none cursor-pointer shadow-sm">
                        <span class="material-symbols-outlined text-[14px]">admin_panel_settings</span>
                        Vista Admin
                    </button>
                    @else
                    <input type="hidden" name="modo" value="usuario">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 text-xs font-bold px-2.5 py-2 rounded-lg transition-all border-none cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                        Ver como usuario
                    </button>
                    @endif
                </form>
            </div>
            @endif
            @endauth
        </aside>

        {{-- ========== COLUMNA PRINCIPAL ========== --}}
        <div class="flex-1 flex flex-col min-w-0">

            <header class="border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-20 shadow-sm dark:border-slate-700 dark:bg-slate-900/90">
                <div class="px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="toggleSidebar(true)" class="lg:hidden w-9 h-9 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-[#28628f] bg-slate-100 dark:bg-slate-800 rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-[20px]">menu</span>
                        </button>
                    </div>

                    <div class="relative flex-grow max-w-md hidden md:block" id="search-container">
                        <div class="flex items-center bg-slate-100 border border-transparent rounded-full px-4 py-2 gap-2 focus-within:border-[#28628f] focus-within:bg-white transition-all dark:bg-slate-800">
                            <span class="material-symbols-outlined text-slate-400 text-[18px]">search</span>
                            <input
                                id="search-input"
                                type="text"
                                placeholder="Destinos, festivales, provincias..."
                                class="bg-transparent border-none p-0 focus:ring-0 text-sm text-slate-700 placeholder:text-slate-400 w-full dark:text-slate-200"
                                autocomplete="off">
                        </div>
                        <div id="search-dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-slate-200 z-50 overflow-hidden dark:bg-slate-800 dark:border-slate-700">
                            <div id="search-results" class="max-h-80 overflow-y-auto"></div>
                            <div id="search-empty" class="hidden px-4 py-6 text-center text-sm text-slate-400">
                                No se encontraron resultados
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 ml-auto">
                        <button onclick="toggleTema()" id="theme-btn" class="flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-600 hover:border-[#28628f] text-slate-600 dark:text-slate-300 hover:text-[#28628f] transition-all bg-white dark:bg-slate-800">
                            <span class="material-symbols-outlined text-[18px]" id="theme-icon">dark_mode</span>
                        </button>

                        @auth
                        <div class="relative" id="notif-container">
                            <button onclick="toggleNotifDropdown()" id="notif-btn" class="relative flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-600 hover:border-[#28628f] text-slate-600 dark:text-slate-300 hover:text-[#28628f] transition-all bg-white dark:bg-slate-800">
                                <span class="material-symbols-outlined text-[18px]">notifications</span>
                                <span id="notif-badge" class="hidden absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">0</span>
                            </button>
                            <div id="notif-dropdown" class="absolute top-full right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 z-50 overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Notificaciones</span>
                                    <button onclick="marcarTodasLeidas()" class="text-xs font-semibold text-[#28628f] hover:underline bg-none border-none cursor-pointer">Marcar todas leídas</button>
                                </div>
                                <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
                                <div id="notif-empty" class="hidden px-4 py-6 text-center text-sm text-slate-400">No tenés notificaciones</div>
                            </div>
                        </div>
                        @endauth

                        @auth
                        <div class="flex items-center gap-3">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition-all text-decoration-none">
                                @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full border border-[#28628f]">
                                @else
                                <div class="w-8 h-8 rounded-full bg-[#28628f] text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                @endif
                                <span class="text-sm font-medium text-slate-700 hidden lg:block">{{ auth()->user()->name }}</span>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-200 hover:border-[#28628f] text-slate-500 hover:text-[#28628f] transition-all bg-white cursor-pointer">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-4 py-2 rounded-full border border-slate-200 hover:border-[#28628f] text-slate-600 hover:text-[#28628f] transition-all bg-white text-decoration-none">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="text-xs font-semibold px-4 py-2 rounded-full bg-[#28628f] hover:bg-[#1a4669] text-white transition-all shadow-sm text-decoration-none">Registrarse</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="flex-grow w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-7xl">
                @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-sm">
                    ✅ {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 text-sm">
                    ⚠️ {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white py-8 mt-12 dark:border-slate-700 dark:bg-slate-900">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-400 dark:text-slate-500">
                    <p>&copy; {{ date('Y') }} Surify. Todos los derechos reservados. Desarrollado con 💖 y Laravel.</p>
                </div>
            </footer>
        </div>
    </div>

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
        function toggleSidebar(open) {
            document.getElementById('admin-sidebar').classList.toggle('open', open);
            document.getElementById('sidebar-overlay').classList.toggle('open', open);
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const audio = document.getElementById('global-surify-song');
            const btn = document.getElementById('global-music-btn');
            const icon = document.getElementById('global-music-icon');
            const status = document.getElementById('global-music-status');

            setInterval(function() {
                if (audio && !audio.paused) {
                    localStorage.setItem('surify-music-time', audio.currentTime);
                    localStorage.setItem('surify-music-playing', 'true');
                }
            }, 1000);

            audio.addEventListener('loadedmetadata', function() {
                const savedTime = parseFloat(localStorage.getItem('surify-music-time') || '0');
                const wasPlaying = localStorage.getItem('surify-music-playing') === 'true';

                if (savedTime > 0) {
                    audio.currentTime = savedTime;
                }

                if (wasPlaying) {
                    audio.play().then(() => {
                        icon.textContent = 'pause';
                        status.textContent = 'Reproduciendo';
                        status.className = "text-[10px] text-emerald-500 font-bold animate-pulse tracking-wide";
                    }).catch(() => {
                        icon.textContent = 'play_arrow';
                        status.textContent = 'Continuar Himno';
                        status.className = "text-[10px] text-amber-500 font-bold tracking-wide animate-pulse";
                    });
                }
            }, {
                once: true
            });

            btn.addEventListener('click', function() {
                if (audio.paused) {
                    audio.play().then(() => {
                        icon.textContent = 'pause';
                        status.textContent = 'Reproduciendo';
                        status.className = "text-[10px] text-emerald-500 font-bold animate-pulse tracking-wide";
                        localStorage.setItem('surify-music-playing', 'true');
                    });
                } else {
                    audio.pause();
                    icon.textContent = 'play_arrow';
                    status.textContent = 'Pausado';
                    status.className = "text-[10px] text-slate-400 font-semibold tracking-wide";
                    localStorage.setItem('surify-music-playing', 'false');
                    localStorage.removeItem('surify-music-time');
                }
            });
        });
    </script>

    {{-- SISTEMA DE NOTIFICACIONES (campanita) --}}
    @auth
    <script>
        (function() {
            const container = document.getElementById('notif-container');
            if (!container) return;
            const dropdown = document.getElementById('notif-dropdown');
            const badge = document.getElementById('notif-badge');
            const list = document.getElementById('notif-list');
            const empty = document.getElementById('notif-empty');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            let abierto = false;

            window.toggleNotifDropdown = function() {
                abierto = !abierto;
                dropdown.classList.toggle('open', abierto);
                if (abierto) cargarNotificaciones();
            };

            document.addEventListener('click', function(e) {
                if (!container.contains(e.target)) {
                    dropdown.classList.remove('open');
                    abierto = false;
                }
            });

            function cargarNotificaciones() {
                fetch('{{ route("notificaciones.index") }}').then(r => r.json()).then(data => {
                    actualizarBadge(data.no_leidas);
                    list.innerHTML = '';
                    if (data.notificaciones.length === 0) {
                        empty.classList.remove('hidden');
                        return;
                    }
                    empty.classList.add('hidden');
                    data.notificaciones.forEach(n => {
                        const a = document.createElement('a');
                        a.href = n.url;
                        a.className = 'flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors border-b border-slate-100 dark:border-slate-700 last:border-0 text-decoration-none' + (n.leida ? '' : ' bg-blue-50/50 dark:bg-slate-700/40');
                        a.onclick = function() {
                            marcarLeida(n.id);
                        };
                        a.innerHTML = `<span class="material-symbols-outlined text-[#28628f] text-[20px] mt-0.5">${n.icono}</span><div class="min-w-0"><p class="text-sm font-bold text-slate-700 dark:text-slate-200">${n.titulo}</p><p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">${n.mensaje}</p><p class="text-[10px] text-slate-400 mt-1">${n.fecha}</p></div>`;
                        list.appendChild(a);
                    });
                }).catch(() => {});
            }

            function actualizarBadge(count) {
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            window.marcarLeida = function(id) {
                fetch(`/notificaciones/${id}/leer`, {
                    method: 'POST',
                    keepalive: true,
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                }).catch(() => {});
            };

            window.marcarTodasLeidas = function() {
                fetch('{{ route("notificaciones.leer_todas") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                }).then(() => cargarNotificaciones()).catch(() => {});
            };

            cargarNotificaciones();
            setInterval(cargarNotificaciones, 30000);
        })();
    </script>
    @endauth

    <script>
        var esAdmin = {
            {
                auth() - > check() && (auth() - > user() - > hasRole('Admin') || auth() - > user() - > hasRole('AdministradorFestivales')) ? 'true' : 'false'
            }
        };

        function navegarSinRecarga(url) {
            fetch(url)
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newMain = doc.querySelector('main');
                    if (newMain) {
                        document.querySelector('main').innerHTML = newMain.innerHTML;
                        window.history.pushState({}, '', url);
                        document.title = doc.title;

                        document.querySelector('main').querySelectorAll('script').forEach(function(scriptViejo) {
                            const scriptNuevo = document.createElement('script');
                            scriptNuevo.textContent = scriptViejo.textContent;
                            document.body.appendChild(scriptNuevo);
                        });
                    }
                });
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const url = link.href;

            if (!url.startsWith(window.location.origin)) return;
            if (url.includes('logout')) return;
            if (url.includes('#')) return;

            if (
                url.includes('login') ||
                url.includes('register') ||
                url.includes('profile') ||
                url.includes('admin') ||
                url.includes('dashboard') ||
                window.location.pathname.startsWith('/admin') ||
                window.location.pathname.startsWith('/dashboard')
            ) {
                return;
            }

            e.preventDefault();
            navegarSinRecarga(url);
        });

        window.addEventListener('popstate', function() {
            window.location.reload();
        });
        // Interceptor global para formularios de eliminación con SweetAlert2
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('submit', function(e) {
                if (e.target && e.target.classList.contains('form-eliminar')) {
                    e.preventDefault();
                    const form = e.target;
                    const titleText = form.dataset.title || '¿Estás seguro?';
                    const warningText = form.dataset.text || '¡No vas a poder revertir esto!';

                    Swal.fire({
                        title: titleText,
                        text: warningText,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>

    <script>
        (function() {
            function actualizarIcono() {
                const icon = document.getElementById('theme-icon');
                if (!icon) return;
                icon.textContent = document.documentElement.classList.contains('dark') ? 'light_mode' : 'dark_mode';
            }
            window.toggleTema = function() {
                const esOscuro = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', esOscuro ? 'dark' : 'light');
                actualizarIcono();
            };
            document.addEventListener('DOMContentLoaded', actualizarIcono);
        })();
    </script>

    {{-- Fechas: la de fin no puede ser anterior a la de inicio (en cualquier formulario con fecha_inicio / fecha_fin) --}}
    <script>
        (function() {
            const MENSAJE = 'La fecha de fin no puede ser anterior a la fecha de inicio.';

            function sincronizar(form) {
                const inicio = form.querySelector('input[name="fecha_inicio"]');
                const fin = form.querySelector('input[name="fecha_fin"]');
                if (!inicio || !fin) return;

                // El calendario de "fin" no deja elegir días anteriores al inicio
                fin.min = inicio.value || '';

                if (inicio.value && fin.value && fin.value < inicio.value) {
                    fin.setCustomValidity(MENSAJE);
                } else {
                    fin.setCustomValidity('');
                }
            }

            document.addEventListener('input', function(e) {
                if (e.target.matches('input[name="fecha_inicio"], input[name="fecha_fin"]') && e.target.form) {
                    sincronizar(e.target.form);
                    if (e.target.name === 'fecha_fin') e.target.reportValidity();
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form').forEach(sincronizar);
            });
        })();
    </script>

</body>

</html>