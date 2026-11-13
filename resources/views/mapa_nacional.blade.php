@extends('layouts.app')

@section('title', 'Surify - Mapa Nacional')
@section('main_class', 'p-0')

@section('content')

<style>
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(225, 29, 72, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(225, 29, 72, 0);
        }
    }

    .surify-map-dashboard {
        position: relative;
        width: 100%;
        height: calc(100vh - 4rem); /* valor inicial: el JS lo ajusta al alto real del header */
        overflow: hidden;
        background-color: #f8f9fa;
        /* Encierra los z-index del mapa (Leaflet usa 400-1000) para que
           nada del mapa se ponga por encima del header ni de los menús */
        isolation: isolate;
        z-index: 0;
    }

    #mapa-container {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }

    .ui-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 30;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 24px;
        box-sizing: border-box;
    }

    /* ===== Distribución de los paneles sobre el mapa =====
       Arriba: explorador (izq.) y botones de zoom (der.).
       Abajo: tarjetas de provincia/destino y el botón del explorador (celular).
       El explorador se achica solo para dejarle lugar a las tarjetas,
       así nunca se pisan. */
    .mapa-fila-superior {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex: 1 1 auto;
        min-height: 0;
    }

    #mobile-sidebar-container {
        display: flex;
        align-items: flex-start;
        height: 100%;
        min-height: 0;
    }

    .panel-explorador {
        border-radius: 16px;
        padding: 16px;
        width: 230px;
        max-height: min(450px, 100%);
        box-sizing: border-box;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    .mapa-tarjetas {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        flex-shrink: 0;
    }

    .mapa-tarjetas>.light-panel {
        width: 230px;
    }

    /* Botón "Explorar" (solo celular): arriba a la izquierda, a la altura del zoom */
    #btn-toggle-sidebar {
        display: none;
        flex-shrink: 0;
        height: 40px;
        border-radius: 10px;
        padding: 0 14px;
        font-weight: 700;
        font-size: 13px;
        color: #28628f !important;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .cabecera-explorador {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .cabecera-explorador .menu-label {
        margin-bottom: 0;
    }

    /* X para cerrar el explorador (solo celular) */
    .cerrar-explorador {
        display: none;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        border: none;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    /* Créditos del mapa: chiquitos y pegados al borde de abajo, en la franja
       libre que queda debajo del reproductor de música */
    .surify-map-dashboard .leaflet-control-attribution {
        font-size: 9px;
        line-height: 1.4;
        padding: 0 5px;
        background: rgba(255, 255, 255, 0.6) !important;
        border-top-left-radius: 6px;
    }

    /* Tablet y celular: el botón de accesibilidad y el reproductor quedan
       encima del mapa, así que los paneles se corren hacia arriba */
    @media (max-width: 1023px) {
        .ui-overlay {
            padding-bottom: 84px;
        }
    }

    /* Celular: explorador oculto hasta que se toca el botón, tarjetas a todo el ancho */
    @media (max-width: 767px) {
        .ui-overlay {
            padding: 12px 12px 84px;
        }

        #mobile-sidebar-container {
            display: none;
        }

        #mobile-sidebar-container.abierto {
            display: flex;
        }

        .panel-explorador {
            width: min(260px, calc(100vw - 88px));
            max-height: 100%;
        }

        .mapa-tarjetas {
            align-items: stretch;
        }

        .mapa-tarjetas>.light-panel {
            width: auto;
        }

        #card-img {
            height: 110px !important;
        }

        #btn-toggle-sidebar {
            display: flex;
        }

        /* Con el explorador abierto, el botón se esconde y aparece la X */
        .ui-overlay.explorador-abierto #btn-toggle-sidebar {
            display: none;
        }

        .cerrar-explorador {
            display: flex;
        }
    }

    .interactuable {
        pointer-events: auto !important;
    }

    .light-panel {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        color: #1e293b !important;
    }

    .menu-list a {
        color: #475569 !important;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .menu-list a:hover {
        background-color: rgba(40, 98, 143, 0.06) !important;
        color: #28628f !important;
    }

    .menu-list a.is-active-menu {
        background-color: rgba(40, 98, 143, 0.1) !important;
        color: #28628f !important;
        font-weight: 700;
    }

    .custom-zoom-controls button {
        background: #fff;
        color: #475569;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        cursor: pointer;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .custom-zoom-controls button:hover {
        background: #f8fafc;
        color: #28628f;
        border-color: #28628f;
    }

    .menu-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin-bottom: 12px;
        padding-left: 4px;
    }

    .card-info {
        padding: 14px;
    }

    .card-info h2 {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 4px 0 12px;
        letter-spacing: -0.02em;
    }

    .card-info p {
        font-size: 10px;
        color: #e11d48;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .btn-ver {
        background: #28628f;
        color: #fff;
        border: none;
        width: 100%;
        padding: 10px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.2s;
    }

    .btn-ver:hover {
        background: #1a4669;
    }

    #card-destino {
        display: none;
        transition: all 0.3s ease;
    }

    #card-destino.visible {
        display: block;
    }

    /* Evita las líneas blancas entre los tiles del mapa */
    .leaflet-tile-container img.leaflet-tile {
        mix-blend-mode: plus-lighter;
        outline: 1px solid transparent;
    }
</style>

<div class="surify-map-dashboard">
    <div id="mapa-container"></div>

    <div class="ui-overlay">
        <div class="mapa-fila-superior">

            <!-- Solo celular: abre el explorador -->
            <button id="btn-toggle-sidebar" type="button" class="interactuable light-panel" onclick="toggleExplorador(true)">
                <i class="fa-solid fa-list"></i> Explorar
            </button>

            <div id="mobile-sidebar-container">
                <aside class="interactuable light-panel panel-explorador">
                    <div class="cabecera-explorador">
                        <p class="menu-label">Explorador</p>
                        <button type="button" class="cerrar-explorador" onclick="toggleExplorador(false)" title="Cerrar explorador">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <ul class="menu-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                        <li><a class="is-active-menu" onclick="filtrarCategoria('todos')" style="cursor:pointer;"><i class="fa-solid fa-compass" style="margin-right:10px; font-size: 14px;"></i> Todos</a></li>
                        <li><a onclick="filtrarCategoria('Patrimonio Natural')" style="cursor:pointer;"><i class="fa-solid fa-tree" style="margin-right:10px; font-size: 14px;"></i> Naturaleza</a></li>
                        <li><a onclick="filtrarCategoria('Ciudad')" style="cursor:pointer;"><i class="fa-solid fa-city" style="margin-right:10px; font-size: 14px;"></i> Ciudades</a></li>
                        <li><a onclick="filtrarCategoria('Cultural')" style="cursor:pointer;"><i class="fa-solid fa-landmark" style="margin-right:10px; font-size: 14px;"></i> Cultura</a></li>
                        <li><a onclick="filtrarCategoria('Aventura')" style="cursor:pointer;"><i class="fa-solid fa-mountain" style="margin-right:10px; font-size: 14px;"></i> Aventura</a></li>
                        <li><a onclick="filtrarCategoria('Playa')" style="cursor:pointer;"><i class="fa-solid fa-umbrella-beach" style="margin-right:10px; font-size: 14px;"></i> Playas</a></li>
                        <li><a onclick="filtrarCategoria('Gastronomía')" style="cursor:pointer;"><i class="fa-solid fa-utensils" style="margin-right:10px; font-size: 14px;"></i> Gastronomía</a></li>
                        <li><a onclick="filtrarCategoria('Familiar')" style="cursor:pointer;"><i class="fa-solid fa-people-roof" style="margin-right:10px; font-size: 14px;"></i> Familiar</a></li>
                        <li><a onclick="filtrarCategoria('Nieve')" style="cursor:pointer;"><i class="fa-solid fa-snowflake" style="margin-right:10px; font-size: 14px;"></i> Nieve</a></li>
                    </ul>

                    <hr style="margin: 12px 0; border-color: #e2e8f0;">
                    <p class="menu-label">Servicios Cercanos</p>
                    <ul class="menu-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px; margin-bottom: 8px;">
                        <li><a onclick="buscarServicios('restaurant')" id="btn-servicio-restaurant" style="cursor:pointer;"><i class="fa-solid fa-utensils" style="margin-right:10px; font-size: 14px; color: #f97316;"></i> Restaurantes</a></li>
                        <li><a onclick="buscarServicios('gas_station')" id="btn-servicio-gas_station" style="cursor:pointer;"><i class="fa-solid fa-gas-pump" style="margin-right:10px; font-size: 14px; color: #06b6d4;"></i> Estaciones de Servicio</a></li>
                        <li><a onclick="buscarServicios('lodging')" id="btn-servicio-lodging" style="cursor:pointer;"><i class="fa-solid fa-hotel" style="margin-right:10px; font-size: 14px; color: #8b5cf6;"></i> Alojamiento</a></li>
                    </ul>

                    <hr style="margin: 12px 0; border-color: #e2e8f0;">
                    <p class="menu-label">Provincias</p>
                    <ul class="menu-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 2px;">
                        @foreach($provincias as $provincia)
                        <li>
                            <a onclick="irAProvincia('{{ $provincia->nombre }}')" style="cursor:pointer;">
                                <i class="fa-solid fa-map-pin" style="margin-right:10px; font-size: 12px;"></i>
                                {{ $provincia->nombre }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </aside>
            </div>

            <div class="interactuable custom-zoom-controls" style="display: flex; flex-direction: column; padding-right: 4px; margin-left: auto;">
                <button onclick="mapaInstance.zoomIn()" title="Acercar"><i class="fa-solid fa-plus"></i></button>
                <button onclick="mapaInstance.zoomOut()" title="Alejar"><i class="fa-solid fa-minus"></i></button>
                <button onclick="centrarEnArgentina()" title="Recentrar"><i class="fa-solid fa-location-crosshairs"></i></button>
                <button onclick="miUbicacion()" title="Mi ubicación" id="btn-geolocalizacion"><i class="fa-solid fa-location-dot"></i></button>
            </div>
        </div>

        <div class="mapa-tarjetas">
            <div id="card-provincia-mapa" class="interactuable light-panel" style="display:none; border-radius: 16px; padding: 14px 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 8px;">
                    <span style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:14px; color:#0f172a;">
                        <i class="fa-solid fa-map-pin" style="color:#28628f;"></i>
                        <span id="card-provincia-nombre"></span>
                    </span>
                    <button onclick="document.getElementById('card-provincia-mapa').style.display='none';" title="Cerrar" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:14px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <a id="card-provincia-link" href="#" class="btn-ver" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                    Ir a la página de la provincia <i class="fa-solid fa-arrow-right" style="margin-left:4px;"></i>
                </a>
            </div>

            <div id="card-destino" class="interactuable light-panel" style="border-radius: 16px; overflow: hidden;">
                <div style="position: relative;">
                    <img id="card-img" src="" alt="" style="width: 100%; height: 135px; object-fit: cover; display: block;">
                    <button onclick="document.getElementById('card-destino').classList.remove('visible');" title="Cerrar"
                        style="position: absolute; top: 10px; left: 10px; width: 28px; height: 28px; border-radius: 50%; border: none; background: rgba(0,0,0,0.45); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <span style="position: absolute; top: 10px; right: 10px; background: #28628f; color: white; padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 800;">
                        <i class="fa-solid fa-star" style="color: #fbbf24; margin-right: 2px;"></i>
                        <span id="card-categoria"></span>
                    </span>
                </div>
                <div class="card-info">
                    <p id="card-provincia"></p>
                    <h2 id="card-nombre"></h2>
                    <p id="card-desc" style="color: #64748b; font-size: 11px; text-transform: none; font-weight: 400; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"></p>
                    <a id="card-link" href="#" class="btn-ver" style="display:block; text-align:center; text-decoration:none;">Ver detalles</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var mapaInstance = null;
    var todosLosMarkers = [];
    var destinosData = @json($destinos);

    var coordenadasProvincias = {
        'Buenos Aires': {
            lat: -36.6769,
            lng: -60.5583,
            zoom: 7
        },
        'Catamarca': {
            lat: -27.3357,
            lng: -66.9477,
            zoom: 7
        },
        'Chaco': {
            lat: -26.9478,
            lng: -60.1658,
            zoom: 7
        },
        'Chubut': {
            lat: -43.2934,
            lng: -65.1078,
            zoom: 6
        },
        'Córdoba': {
            lat: -31.4135,
            lng: -64.1811,
            zoom: 7
        },
        'Corrientes': {
            lat: -27.4692,
            lng: -58.8306,
            zoom: 7
        },
        'Entre Ríos': {
            lat: -31.7748,
            lng: -60.4956,
            zoom: 7
        },
        'Formosa': {
            lat: -24.8948,
            lng: -59.8901,
            zoom: 7
        },
        'Jujuy': {
            lat: -23.1897,
            lng: -65.9997,
            zoom: 7
        },
        'La Pampa': {
            lat: -36.6148,
            lng: -64.2839,
            zoom: 7
        },
        'La Rioja': {
            lat: -29.4127,
            lng: -66.8552,
            zoom: 7
        },
        'Mendoza': {
            lat: -32.8908,
            lng: -68.8458,
            zoom: 7
        },
        'Misiones': {
            lat: -26.9478,
            lng: -54.6964,
            zoom: 7
        },
        'Neuquén': {
            lat: -38.9516,
            lng: -68.0591,
            zoom: 7
        },
        'Río Negro': {
            lat: -40.8135,
            lng: -63.0154,
            zoom: 7
        },
        'Salta': {
            lat: -24.7821,
            lng: -65.4117,
            zoom: 7
        },
        'San Juan': {
            lat: -30.8653,
            lng: -68.8894,
            zoom: 7
        },
        'San Luis': {
            lat: -33.2950,
            lng: -66.3356,
            zoom: 7
        },
        'Santa Cruz': {
            lat: -51.6230,
            lng: -69.2168,
            zoom: 6
        },
        'Santa Fe': {
            lat: -30.7069,
            lng: -60.9498,
            zoom: 7
        },
        'Santiago del Estero': {
            lat: -27.7824,
            lng: -64.2661,
            zoom: 7
        },
        'Tierra del Fuego': {
            lat: -54.0000,
            lng: -67.0000,
            zoom: 7
        },
        'Tucumán': {
            lat: -26.8083,
            lng: -65.2176,
            zoom: 8
        },
        'Ciudad Autónoma de Buenos Aires': {
            lat: -34.6037,
            lng: -58.3816,
            zoom: 12
        },
    };

    var icono = L.divIcon({
        html: '<div style="background:#28628f; width:14px; height:14px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
        className: '',
        iconSize: [14, 14],
        iconAnchor: [7, 7],
    });

    // Ajusta el alto del mapa al espacio que queda debajo del header,
    // así la página no scrollea y el mapa no se mete abajo del header
    function ajustarAltoMapa() {
        var dashboard = document.querySelector('.surify-map-dashboard');
        if (!dashboard) return;
        var top = dashboard.getBoundingClientRect().top + window.scrollY;
        dashboard.style.height = Math.max(window.innerHeight - top, 400) + 'px';
        if (mapaInstance) mapaInstance.invalidateSize();
    }
    window.addEventListener('resize', ajustarAltoMapa);

    function inicializarMapa() {
        ajustarAltoMapa();
        var contenedor = document.getElementById('mapa-container');
        if (!contenedor || mapaInstance !== null) return;

        if (contenedor.offsetWidth === 0 || contenedor.offsetHeight === 0) {
            setTimeout(inicializarMapa, 100);
            return;
        }

        mapaInstance = L.map('mapa-container', {
            zoomControl: false,
            minZoom: 4,
            maxZoom: 19,
            maxBounds: L.latLngBounds(L.latLng(-56.5, -76.0), L.latLng(-21.0, -53.0)),
            maxBoundsViscosity: 1.0
        }).setView([-38.416097, -63.616672], 4);

        // OpenStreetMap ya no recomienda los subdominios a/b/c: se usa la URL directa
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(mapaInstance);

        // Sin el "Leaflet |" adelante: queda solo el crédito obligatorio de OpenStreetMap
        mapaInstance.attributionControl.setPrefix(false);

        destinosData.forEach(function(destino) {
            var marker = L.marker([destino.lat, destino.lng], {
                    icon: icono
                })
                .addTo(mapaInstance)
                .on('click', function() {
                    mostrarCard(destino);
                });

            marker.bindTooltip(destino.nombre, {
                permanent: false,
                direction: 'top',
                className: 'leaflet-tooltip-surify'
            });

            marker._destinoCategoria = destino.categoria;
            todosLosMarkers.push(marker);
        });

        setTimeout(function() {
            mapaInstance.invalidateSize(true);
        }, 300);
    }

    function mostrarCard(destino) {
        document.getElementById('card-img').src = destino.imagen_url;
        document.getElementById('card-nombre').textContent = destino.nombre;
        document.getElementById('card-provincia').textContent = destino.provincia;
        document.getElementById('card-desc').textContent = destino.descripcion;
        document.getElementById('card-categoria').textContent = destino.categoria;
        document.getElementById('card-link').href = '/destinos/' + destino.id;
        document.getElementById('card-destino').classList.add('visible');
        document.getElementById('card-provincia-mapa').style.display = 'none';
        toggleExplorador(false);

        if (mapaInstance && destino.lat && destino.lng) {
            mapaInstance.flyTo([destino.lat, destino.lng], 12, {
                duration: 1.5
            });
        }
    }

    // Categorías elegidas. Vacío = "Todos"
    var categoriasActivas = new Set();

    // "Gastronomía" y "gastronomia" cuentan como la misma categoría
    function normalizarCategoria(texto) {
        return (texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim().toLowerCase();
    }

    function aplicarFiltroCategoria() {
        todosLosMarkers.forEach(function(marker) {
            var cat = normalizarCategoria(marker._destinoCategoria);
            var mostrar = categoriasActivas.size === 0 || categoriasActivas.has(cat);

            if (mostrar) {
                if (!mapaInstance.hasLayer(marker)) mapaInstance.addLayer(marker);
            } else {
                if (mapaInstance.hasLayer(marker)) mapaInstance.removeLayer(marker);
            }
        });
        actualizarBotonesCategoria();
    }

    // Marca como activos los botones de las categorías elegidas (o "Todos" si no hay ninguna)
    function actualizarBotonesCategoria() {
        document.querySelectorAll('.menu-list a[onclick*="filtrarCategoria("]').forEach(function(a) {
            var m = a.getAttribute('onclick').match(/filtrarCategoria\('([^']+)'\)/);
            if (!m) return;
            var cat = normalizarCategoria(m[1]);
            var activo = cat === 'todos' ? categoriasActivas.size === 0 : categoriasActivas.has(cat);
            a.classList.toggle('is-active-menu', activo);
        });
    }

    // Clic en una categoría: la agrega o la saca. "Todos" limpia la selección.
    function filtrarCategoria(categoria) {
        var cat = normalizarCategoria(categoria);

        if (cat === 'todos') {
            categoriasActivas.clear();
        } else if (categoriasActivas.has(cat)) {
            categoriasActivas.delete(cat);
        } else {
            categoriasActivas.add(cat);
        }

        aplicarFiltroCategoria();
    }

    function irAProvincia(nombre) {
        categoriasActivas.clear();
        aplicarFiltroCategoria();

        var coords = coordenadasProvincias[nombre];
        if (coords && mapaInstance) {
            mapaInstance.flyTo([coords.lat, coords.lng], coords.zoom, {
                duration: 1.5
            });
        }

        document.getElementById('card-provincia-nombre').textContent = nombre;
        document.getElementById('card-provincia-link').href = '/provincia/' + encodeURIComponent(nombre);
        document.getElementById('card-provincia-mapa').style.display = 'block';

        document.getElementById('card-destino').classList.remove('visible');
        toggleExplorador(false);
    }

    function centrarEnArgentina() {
        mapaInstance.flyTo([-38.416097, -63.616672], 4, {
            duration: 1.5
        });
    }

    inicializarMapa();
    setTimeout(inicializarMapa, 500);

    function miUbicacion() {
        const btn = document.getElementById('btn-geolocalizacion');
        btn.style.color = '#28628f';

        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'warning',
                title: 'Geolocalización no soportada',
                text: 'Tu navegador no soporta geolocalización.',
                confirmButtonColor: '#28628f'
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            mapaInstance.flyTo([lat, lng], 10, {
                duration: 1.5
            });

            const iconoUsuario = L.divIcon({
                html: '<div style="background:#e11d48; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 2px 8px rgba(225,29,72,0.5); animation: pulse 1.5s infinite;"></div>',
                className: '',
                iconSize: [16, 16],
                iconAnchor: [8, 8],
            });

            if (window.marcadorUsuario) {
                mapaInstance.removeLayer(window.marcadorUsuario);
            }

            window.marcadorUsuario = L.marker([lat, lng], {
                    icon: iconoUsuario
                })
                .addTo(mapaInstance)
                .bindPopup('<div style="font-family:sans-serif; font-size:13px;"><strong style="color:#e11d48;">📍 Tu ubicación</strong></div>')
                .openPopup();

            const destinosCercanos = destinosData.filter(function(destino) {
                const dist = calcularDistancia(lat, lng, destino.lat, destino.lng);
                return dist < 300;
            }).sort(function(a, b) {
                return calcularDistancia(lat, lng, a.lat, a.lng) - calcularDistancia(lat, lng, b.lat, b.lng);
            }).slice(0, 3);

            if (destinosCercanos.length > 0) {
                mostrarCard(destinosCercanos[0]);
            }

            btn.style.color = '#e11d48';

        }, function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de ubicación',
                text: 'No se pudo obtener tu ubicación. Verificá los permisos del navegador.',
                confirmButtonColor: '#28628f'
            });
            btn.style.color = '#475569';
        });
    }

    function calcularDistancia(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLng / 2) * Math.sin(dLng / 2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }
    var markersServicios = [];

    async function buscarServicios(tipo) {
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error de SDK',
                text: 'El SDK de Google Maps no está cargado. Verifica tu API Key en el archivo .env.',
                confirmButtonColor: '#28628f'
            });
            return;
        }

        const centro = mapaInstance.getCenter();
        const lat = centro.lat;
        const lng = centro.lng;

        markersServicios.forEach(m => mapaInstance.removeLayer(m));
        markersServicios = [];

        const tipos = ['restaurant', 'gas_station', 'lodging'];
        tipos.forEach(t => {
            const btn = document.getElementById('btn-servicio-' + t);
            if (btn) {
                if (t === tipo) {
                    btn.classList.add('is-active-menu');
                    btn.style.backgroundColor = 'rgba(40, 98, 143, 0.1)';
                    btn.style.color = '#28628f';
                } else {
                    btn.classList.remove('is-active-menu');
                    btn.style.backgroundColor = 'transparent';
                    btn.style.color = '#475569';
                }
            }
        });

        const request = {
            fields: ['displayName', 'formattedAddress', 'location', 'rating'],
            locationRestriction: {
                center: {
                    lat: lat,
                    lng: lng
                },
                radius: 5000,
            },
            includedPrimaryTypes: [tipo === 'restaurant' ? 'restaurant' : (tipo === 'gas_station' ? 'gas_station' : 'lodging')],
            maxResultCount: 15,
        };

        try {
            const {
                Place
            } = await google.maps.importLibrary("places");
            const {
                places
            } = await Place.searchNearby(request);

            if (places && places.length > 0) {
                places.forEach(function(place) {
                    const plat = place.location.lat();
                    const plng = place.location.lng();
                    const name = place.displayName;
                    const address = place.formattedAddress;
                    const rating = place.rating;

                    let color = '#f97316';
                    let iconName = 'restaurant';
                    if (tipo === 'gas_station') {
                        color = '#06b6d4';
                        iconName = 'local_gas_station';
                    } else if (tipo === 'lodging') {
                        color = '#8b5cf6';
                        iconName = 'hotel';
                    }

                    const htmlIcon = `<div style="background:${color}; width:32px; height:32px; border-radius:50%; border:2px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:white;"><span class="material-symbols-outlined text-[16px]" style="display:flex; justify-content:center; align-items:center; width:100%; height:100%; font-variation-settings: 'FILL' 1;">${iconName}</span></div>`;
                    const customIcon = L.divIcon({
                        html: htmlIcon,
                        className: '',
                        iconSize: [32, 32],
                        iconAnchor: [16, 16]
                    });

                    const m = L.marker([plat, plng], {
                            icon: customIcon
                        })
                        .addTo(mapaInstance)
                        .bindPopup(`
                            <div style="font-family:'Outfit',sans-serif; padding:4px; min-width: 140px;">
                                <strong style="color:${color}; font-size:13px; display:block; margin-bottom:2px;">${name}</strong>
                                <span style="font-size:11px; color:#64748b; display:block; margin-bottom:4px;">📍 ${address || 'Sin dirección'}</span>
                                ${rating ? `<span style="font-size:11px; font-weight:bold; color:#f59e0b; display:flex; align-items:center; gap:2px;"><span class="material-symbols-outlined text-[13px]" style="font-variation-settings: 'FILL' 1;">star</span> ${rating} / 5</span>` : ''}
                            </div>
                        `);

                    markersServicios.push(m);
                });

                mapaInstance.setView([lat, lng], 14);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin resultados',
                    text: 'No se encontraron servicios de este tipo en un radio de 5km de esta zona.',
                    confirmButtonColor: '#28628f'
                });
            }
        } catch (error) {
            console.error('Error al buscar servicios cercanos:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error de búsqueda',
                text: 'Error al buscar servicios cercanos: ' + error.message,
                confirmButtonColor: '#28628f'
            });
        }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places"></script>

<script>
    // Celular: abre/cierra el explorador. Sin argumento alterna; con true/false lo fuerza.
    // En compu y tablet el explorador siempre se ve (lo maneja el CSS).
    function toggleExplorador(abrir) {
        var panel = document.getElementById('mobile-sidebar-container');
        var overlay = document.querySelector('.ui-overlay');
        if (!panel || !overlay) return;
        var abierto = typeof abrir === 'boolean' ? abrir : !panel.classList.contains('abierto');
        panel.classList.toggle('abierto', abierto);
        overlay.classList.toggle('explorador-abierto', abierto);
    }
</script>

@endsection