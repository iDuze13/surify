@extends('layouts.app')

@section('title', 'Surify - Mapa Nacional')

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
        height: calc(100vh - 4rem);
        overflow: hidden;
        background-color: #f8f9fa;
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
        justify-content: space-between;
        padding: 24px;
        box-sizing: border-box;
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
</style>

<div class="surify-map-dashboard">
    <div id="mapa-container"></div>

    <div class="ui-overlay">
        <div style="display: flex; justify-content: space-between; width: 100%;">

            <div class="interactuable" id="mobile-sidebar-container">
                <aside class="light-panel" style="border-radius: 16px; padding: 16px; width: 230px; max-height: 450px; overflow-y: auto;">
                    <p class="menu-label">Explorador</p>
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

            <div class="interactuable custom-zoom-controls" style="display: flex; flex-direction: column; padding-right: 4px;">
                <button onclick="mapaInstance.zoomIn()" title="Acercar"><i class="fa-solid fa-plus"></i></button>
                <button onclick="mapaInstance.zoomOut()" title="Alejar"><i class="fa-solid fa-minus"></i></button>
                <button onclick="centrarEnArgentina()" title="Recentrar"><i class="fa-solid fa-location-crosshairs"></i></button>
                <button onclick="miUbicacion()" title="Mi ubicación" id="btn-geolocalizacion"><i class="fa-solid fa-location-dot"></i></button>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 12px; width: 100%;">
            <div id="card-provincia-mapa" class="interactuable light-panel" style="display:none; border-radius: 16px; padding: 14px 16px; width: 230px;">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 8px;">
                    <span style="display:flex; align-items:center; gap:6px; font-weight:800; font-size:14px; color:#0f172a;">
                        <i class="fa-solid fa-map-pin" style="color:#28628f;"></i>
                        <span id="card-provincia-nombre"></span>
                    </span>
                    <button onclick="document.getElementById('card-provincia-mapa').style.display='none';" style="background:none; border:none; cursor:pointer; color:#94a3b8; font-size:14px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <a id="card-provincia-link" href="#" class="btn-ver" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                    Ir a la página de la provincia <i class="fa-solid fa-arrow-right" style="margin-left:4px;"></i>
                </a>
            </div>

            <div id="card-destino" class="interactuable light-panel" style="border-radius: 16px; overflow: hidden; width: 230px;">
                <div style="position: relative;">
                    <img id="card-img" src="" alt="" style="width: 100%; height: 135px; object-fit: cover; display: block;">
                    <span style="position: absolute; top: 10px; right: 10px; background: #28628f; color: white; padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 800;">
                        <i class="fa-solid fa-star" style="color: #fbbf24; margin-right: 2px;"></i>
                        <span id="card-categoria"></span>
                    </span>
                </div>
                <div class="card-info">
                    <p id="card-provincia"></p>
                    <h2 id="card-nombre"></h2>
                    <p id="card-desc" style="color: #64748b; font-size: 11px; text-transform: none; font-weight: 400; margin-bottom: 10px;"></p>
                    <a id="card-link" href="#" class="btn-ver" style="display:block; text-align:center; text-decoration:none;">Ver detalles</a>
                </div>
            </div>
        </div>

        <!-- Botón flotante solo para celulares -->
        <button id="btn-toggle-sidebar" class="md:hidden interactuable light-panel" style="position: absolute; bottom: 24px; left: 50%; transform: translateX(-50%); border-radius: 99px; padding: 10px 20px; font-weight: 800; font-size: 13px; color: #28628f !important; display: flex; align-items: center; gap: 8px; cursor: pointer; border: 2px solid #28628f; z-index: 50;">
            <i class="fa-solid fa-eye-slash"></i> Ocultar Explorador
        </button>
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

    function inicializarMapa() {
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

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            subdomains: 'abc',
            maxZoom: 19
        }).addTo(mapaInstance);

        // Agregar marcadores desde la BD
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

        // Volar (hacer zoom) hacia el destino
        if (mapaInstance && destino.lat && destino.lng) {
            mapaInstance.flyTo([destino.lat, destino.lng], 12, {
                duration: 1.5
            });
        }
    }

    // Aplica el filtro de categoría a los marcadores, sin tocar el resaltado del menú
    // (se separó de filtrarCategoria para poder reutilizarla desde irAProvincia).
    function aplicarFiltroCategoria(categoria) {
        // Comparación normalizada (sin importar mayúsculas/minúsculas ni espacios extra),
        // para que coincida con lo que se haya guardado realmente en la categoría del destino.
        var categoriaNormalizada = categoria.trim().toLowerCase();

        todosLosMarkers.forEach(function(marker) {
            var categoriaDestino = (marker._destinoCategoria || '').trim().toLowerCase();
            if (categoria === 'todos' || categoriaDestino === categoriaNormalizada) {
                if (!mapaInstance.hasLayer(marker)) mapaInstance.addLayer(marker);
            } else {
                if (mapaInstance.hasLayer(marker)) mapaInstance.removeLayer(marker);
            }
        });
    }

    function filtrarCategoria(categoria) {
        // Actualizar menú activo
        document.querySelectorAll('.menu-list a').forEach(a => a.classList.remove('is-active-menu'));
        event.target.closest('a').classList.add('is-active-menu');
        aplicarFiltroCategoria(categoria);
    }

    function irAProvincia(nombre) {
        // Mostramos todos los destinos (por si había un filtro de categoría activo)
        // y resaltamos "Todos" en el menú, ya que dejamos de filtrar por categoría.
        aplicarFiltroCategoria('todos');
        document.querySelectorAll('.menu-list a').forEach(a => a.classList.remove('is-active-menu'));
        var btnTodos = document.querySelector('.menu-list a[onclick*="filtrarCategoria(\'todos\')"]');
        if (btnTodos) btnTodos.classList.add('is-active-menu');

        // Hacemos zoom hacia la provincia elegida usando sus coordenadas precargadas
        var coords = coordenadasProvincias[nombre];
        if (coords && mapaInstance) {
            mapaInstance.flyTo([coords.lat, coords.lng], coords.zoom, {
                duration: 1.5
            });
        }

        // Mostramos el cartelito con el link a la página completa de la provincia
        document.getElementById('card-provincia-nombre').textContent = nombre;
        document.getElementById('card-provincia-link').href = '/provincia/' + encodeURIComponent(nombre);
        document.getElementById('card-provincia-mapa').style.display = 'block';

        // Si había una tarjeta de destino abierta, la cerramos para no amontonar
        document.getElementById('card-destino').classList.remove('visible');
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

            // Centrar mapa en la ubicación del usuario
            mapaInstance.flyTo([lat, lng], 10, {
                duration: 1.5
            });

            // Agregar marcador de posición
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

            // Encontrar destinos cercanos (menos de 300km)
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

        // Limpiar anteriores
        markersServicios.forEach(m => mapaInstance.removeLayer(m));
        markersServicios = [];

        // Estilos activos
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

        // Configurar los parámetros de búsqueda de la nueva Places API
        const request = {
            fields: ['displayName', 'formattedAddress', 'location', 'rating'],
            locationRestriction: {
                center: { lat: lat, lng: lng },
                radius: 5000,
            },
            includedPrimaryTypes: [tipo === 'restaurant' ? 'restaurant' : (tipo === 'gas_station' ? 'gas_station' : 'lodging')],
            maxResultCount: 15,
        };

        try {
            const { Place } = await google.maps.importLibrary("places");
            const { places } = await Place.searchNearby(request);

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

                    const m = L.marker([plat, plng], { icon: customIcon })
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
    document.addEventListener('DOMContentLoaded', function() {
        const btnToggle = document.getElementById('btn-toggle-sidebar');
        const sidebar = document.getElementById('mobile-sidebar-container');
        let sidebarVisible = true;

        if (btnToggle && sidebar) {
            btnToggle.addEventListener('click', function() {
                sidebarVisible = !sidebarVisible;
                if (sidebarVisible) {
                    sidebar.style.display = 'block';
                    btnToggle.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Ocultar Explorador';
                } else {
                    sidebar.style.display = 'none';
                    btnToggle.innerHTML = '<i class="fa-solid fa-list"></i> Ver Explorador';
                }
            });
        }
    });
</script>

@endsection