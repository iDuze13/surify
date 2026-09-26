@extends('layouts.app')

@section('title', 'Surify - Calculadora de Combustible')

@section('content')

<div class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-[#28628f] mb-1">Herramientas</p>
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Resumen de Viaje</h1>
    <p class="text-slate-500 mt-1 text-lg">Calculadora de ruta, costos y logística para tu aventura en Argentina.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Columna Izquierda --}}
    <div class="lg:col-span-7 flex flex-col gap-6">

        {{-- Ruta Principal --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">route</span>
                Ruta Principal
            </h2>
            <div class="flex flex-col gap-3">

                {{-- Origen --}}
                <div class="flex items-center bg-slate-50 rounded-lg p-3 border border-slate-200 focus-within:border-[#28628f] transition-colors relative">
                    <span class="material-symbols-outlined text-slate-400 mr-3">trip_origin</span>
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-0.5">Origen</label>
                        <input id="input-origen" type="text" placeholder="Detectando tu ubicación..." autocomplete="off"
                            class="w-full bg-transparent border-none p-0 focus:ring-0 text-base text-slate-800 font-medium placeholder:text-slate-300">
                    </div>
                    <button onclick="detectarUbicacion()" title="Usar mi ubicación"
                        class="ml-2 text-[#28628f] hover:text-[#1a4669] transition-colors">
                        <span class="material-symbols-outlined text-[22px]" id="icono-gps">my_location</span>
                    </button>
                    <div id="sugerencias-origen" class="hidden absolute top-full left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-50 mt-1 overflow-hidden max-h-48 overflow-y-auto"></div>
                </div>

                {{-- Destino --}}
                <div class="flex items-center bg-slate-50 rounded-lg p-3 border border-slate-200 focus-within:border-[#28628f] transition-colors relative">
                    <span class="material-symbols-outlined text-rose-400 mr-3">location_on</span>
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-0.5">Destino</label>
                        <input id="input-destino" type="text" placeholder="Ej: Cataratas del Iguazú" autocomplete="off"
                            class="w-full bg-transparent border-none p-0 focus:ring-0 text-base text-slate-800 font-medium placeholder:text-slate-300">
                    </div>
                    <div id="sugerencias-destino" class="hidden absolute top-full left-0 right-0 bg-white border border-slate-200 rounded-xl shadow-xl z-50 mt-1 overflow-hidden max-h-48 overflow-y-auto"></div>
                </div>

                {{-- Distancia --}}
                <div class="flex items-center bg-slate-50 rounded-lg p-3 border border-slate-200 gap-3">
                    <span class="material-symbols-outlined text-slate-400">straighten</span>
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-0.5">Distancia (km)</label>
                        <input id="input-distancia" type="number" min="1" placeholder="Se calcula automáticamente"
                            class="w-full bg-transparent border-none p-0 focus:ring-0 text-base text-slate-800 font-medium placeholder:text-slate-300">
                    </div>
                </div>

                {{-- Resumen ruta --}}
                <div id="ruta-resumen" class="hidden items-center justify-between p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Ruta ingresada</span>
                        <span id="ruta-texto" class="text-sm text-slate-700 font-medium mt-0.5"></span>
                    </div>
                    <div class="text-right">
                        <span id="ruta-km" class="block text-xl font-black text-[#28628f]"></span>
                        <span class="text-xs font-bold text-slate-400">distancia</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calculadora + Nota --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Calculadora --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-500">local_gas_station</span>
                    Calculadora de Consumo
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Tipo de Vehículo</label>
                        <select id="select-vehiculo" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                            <option value="sedan">Sedán (~8 L/100km)</option>
                            <option value="suv">SUV (~11 L/100km)</option>
                            <option value="camioneta">Camioneta (~13 L/100km)</option>
                            <option value="moto">Moto (~4 L/100km)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Tipo de Combustible</label>
                        <select id="select-combustible" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                            <option value="1150">Nafta Súper (~$1.150/L)</option>
                            <option value="1380">Nafta Premium (~$1.380/L)</option>
                            <option value="1050">Diesel (~$1.050/L)</option>
                            <option value="400">GNC (~$400/m³)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Consumo (L/100km)</label>
                        <input id="input-consumo" type="number" value="8" min="1" max="50"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">Precio del combustible ($/L)</label>
                        <input id="input-precio" type="number" value="1150" min="1"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Litros (solo ida)</span>
                        <span id="resultado-litros-ida" class="text-lg font-bold text-slate-700">—</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Litros (ida y vuelta)</span>
                        <span id="resultado-litros-total" class="text-lg font-bold text-[#28628f]">—</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                        <span class="text-sm font-bold text-slate-600">Costo estimado (i/v)</span>
                        <span id="resultado-costo" class="text-lg font-black text-[#28628f]">—</span>
                    </div>
                </div>

                <button id="btn-calcular"
                    class="w-full mt-4 bg-[#28628f] hover:bg-[#1a4669] text-white text-sm font-bold py-2.5 rounded-full transition-all shadow-sm active:scale-95">
                    Calcular
                </button>
            </div>

            {{-- Nota Regional --}}
            <div class="bg-[#28628f] text-white rounded-xl shadow-sm p-6 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-6 -top-6 opacity-10">
                    <span class="material-symbols-outlined text-[120px]">price_change</span>
                </div>
                <div class="relative z-10">
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-200 block mb-2">Nota Regional</span>
                    <h3 class="text-lg font-bold mb-3">Precios de Combustible</h3>
                    <p class="text-sm text-blue-100 leading-relaxed">Misiones y Corrientes suelen tener precios más altos que Formosa. Se recomienda cargar combustible antes de ingresar a las zonas de la RN12.</p>
                </div>
                <div class="relative z-10 mt-6 bg-white/10 rounded-lg p-3 border border-white/20">
                    <span class="text-xs font-bold text-blue-200 uppercase tracking-wide block mb-1">⚠️ Antes de salir</span>
                    <p class="text-xs text-blue-100">Verificá los precios actuales en tu estación de servicio local.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Columna Derecha --}}
    <div class="lg:col-span-5 flex flex-col gap-6">

        {{-- Mapa Leaflet --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" style="height:280px;">
            <div id="mapa-ruta" class="w-full h-full"></div>
        </div>

        {{-- Requisitos y Consejos --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Requisitos y Consejos</h3>
            <ul class="flex flex-col gap-3">
                <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined text-rose-400 mt-0.5" style="font-variation-settings:'FILL' 1;">description</span>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700">Documentación Obligatoria</h4>
                        <p class="text-xs text-slate-500 mt-1">DNI, cédula del vehículo y seguro vigente. Si cruzás a Brasil o Paraguay, llevá también la Carta Verde.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined text-amber-500 mt-0.5" style="font-variation-settings:'FILL' 1;">car_repair</span>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700">Mantenimiento Vehicular</h4>
                        <p class="text-xs text-slate-500 mt-1">Revisá frenos y refrigeración si vas hacia Misiones — las pendientes de la RN12 son exigentes.</p>
                    </div>
                </li>
                <li class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                    <span class="material-symbols-outlined text-slate-400 mt-0.5" style="font-variation-settings:'FILL' 1;">toll</span>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700">Peajes en Ruta</h4>
                        <p class="text-xs text-slate-500 mt-1">Hay varios peajes en la RN12 por Corrientes y Misiones. Llevá efectivo o tené el telepeaje activo.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
var destinosSurify = @json($destinos);
var coordOrigenSeleccionado = null;
var coordDestinoSeleccionado = null;

var mapaRuta = L.map('mapa-ruta', { zoomControl: true }).setView([-38, -63], 4);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png?key={{ env("CARTO_KEY") }}', {
    attribution: '© OpenStreetMap, © CartoDB', subdomains: 'abcd', maxZoom: 19
}).addTo(mapaRuta);

var marcadorOrigen = null, marcadorDestino = null, lineaRuta = null;

var iconOrigen = L.divIcon({
    html: '<div style="background:#28628f;width:14px;height:14px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
    className: '', iconSize: [14,14], iconAnchor: [7,7]
});
var iconDestino = L.divIcon({
    html: '<div style="background:#e11d48;width:14px;height:14px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
    className: '', iconSize: [14,14], iconAnchor: [7,7]
});

function calcularDistanciaHaversine(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = (lat2-lat1) * Math.PI/180;
    const dLng = (lng2-lng1) * Math.PI/180;
    const a = Math.sin(dLat/2)*Math.sin(dLat/2) +
              Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)*Math.sin(dLng/2);
    return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
}

function actualizarMapa(origen, destino) {
    if (marcadorOrigen) mapaRuta.removeLayer(marcadorOrigen);
    if (marcadorDestino) mapaRuta.removeLayer(marcadorDestino);
    if (lineaRuta) mapaRuta.removeLayer(lineaRuta);

    marcadorOrigen = L.marker([origen.lat, origen.lng], {icon: iconOrigen}).addTo(mapaRuta).bindPopup('Origen');
    marcadorDestino = L.marker([destino.lat, destino.lng], {icon: iconDestino}).addTo(mapaRuta).bindPopup('Destino');

    const centroAR = { lat: -34.6, lng: -64.0 };
    const necesitaIntermedio = (
        (origen.lat > -25 && destino.lat < -35) ||
        (origen.lat < -35 && destino.lat > -25) ||
        (origen.lng < -65 && destino.lng > -58) ||
        (origen.lng > -58 && destino.lng < -65)
    );

    const waypoints = necesitaIntermedio
        ? `${origen.lng},${origen.lat};${centroAR.lng},${centroAR.lat};${destino.lng},${destino.lat}`
        : `${origen.lng},${origen.lat};${destino.lng},${destino.lat}`;

    const url = `https://router.project-osrm.org/route/v1/driving/${waypoints}?overview=full&geometries=geojson`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.code === 'Ok') {
                const coordenadas = data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
                lineaRuta = L.polyline(coordenadas, {
                    color: '#28628f', weight: 4, opacity: 0.8
                }).addTo(mapaRuta);
                mapaRuta.fitBounds(lineaRuta.getBounds(), {padding: [30,30]});

                const distanciaKm = Math.round(data.routes[0].distance / 1000);
                const tiempoSeg = data.routes[0].duration;
                const horas = Math.floor(tiempoSeg / 3600);
                const minutos = Math.floor((tiempoSeg % 3600) / 60);
                const tiempoTexto = horas > 0 ? `${horas}h ${minutos}min` : `${minutos}min`;

                document.getElementById('input-distancia').value = distanciaKm;
                document.getElementById('ruta-km').textContent = distanciaKm + ' km';
                document.getElementById('ruta-texto').textContent =
                    document.getElementById('input-origen').value + ' → ' + document.getElementById('input-destino').value;

                const tiempoEl = document.getElementById('ruta-tiempo');
                if (tiempoEl) tiempoEl.textContent = '⏱ ' + tiempoTexto + ' de viaje';

                const resumen = document.getElementById('ruta-resumen');
                resumen.classList.remove('hidden');
                resumen.classList.add('flex');
            } else {
                lineaRuta = L.polyline([[origen.lat,origen.lng],[destino.lat,destino.lng]], {
                    color:'#28628f', weight:3, opacity:0.7, dashArray:'8,6'
                }).addTo(mapaRuta);
                mapaRuta.fitBounds([[origen.lat,origen.lng],[destino.lat,destino.lng]], {padding:[30,30]});
            }
        })
        .catch(() => {
            lineaRuta = L.polyline([[origen.lat,origen.lng],[destino.lat,destino.lng]], {
                color:'#28628f', weight:3, opacity:0.7, dashArray:'8,6'
            }).addTo(mapaRuta);
            mapaRuta.fitBounds([[origen.lat,origen.lng],[destino.lat,destino.lng]], {padding:[30,30]});
        });
}

function actualizarDistanciaAuto() {
    if (coordOrigenSeleccionado && coordDestinoSeleccionado) {
        actualizarMapa(coordOrigenSeleccionado, coordDestinoSeleccionado);
    }
}

function mostrarSugerenciasOrigen() {
    const input = document.getElementById('input-origen');
    const dropdown = document.getElementById('sugerencias-origen');

    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        if (query.length < 1) { dropdown.classList.add('hidden'); return; }

        const filtrados = destinosSurify.filter(d =>
            d.nombre.toLowerCase().includes(query) || d.provincia.toLowerCase().includes(query)
        ).slice(0, 6);

        if (filtrados.length === 0) { dropdown.classList.add('hidden'); return; }

        filtrados.forEach(function(d) {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0';
            item.innerHTML = `
                <span class="material-symbols-outlined text-[#28628f] text-[18px]">location_on</span>
                <div>
                    <p class="text-sm font-bold text-slate-800">${d.nombre}</p>
                    <p class="text-xs text-slate-400">${d.provincia}</p>
                </div>
            `;
            item.addEventListener('click', function() {
                input.value = d.nombre + ', ' + d.provincia;
                dropdown.classList.add('hidden');
                coordOrigenSeleccionado = { lat: d.lat, lng: d.lng };
                actualizarDistanciaAuto();
            });
            dropdown.appendChild(item);
        });
        dropdown.classList.remove('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden');
    });
}

function mostrarSugerenciasDestino() {
    const input = document.getElementById('input-destino');
    const dropdown = document.getElementById('sugerencias-destino');

    input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        dropdown.innerHTML = '';
        if (query.length < 2) { dropdown.classList.add('hidden'); return; }

        const locales = destinosSurify.filter(d =>
            d.nombre.toLowerCase().includes(query) || d.provincia.toLowerCase().includes(query)
        ).slice(0, 3);

        locales.forEach(function(d) {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100';
            item.innerHTML = `
                <span class="material-symbols-outlined text-[#28628f] text-[18px]">landscape</span>
                <div>
                    <p class="text-sm font-bold text-slate-800">${d.nombre}</p>
                    <p class="text-xs text-slate-400">${d.provincia} • Destino Surify</p>
                </div>
            `;
            item.addEventListener('click', function() {
                input.value = d.nombre + ', ' + d.provincia;
                dropdown.classList.add('hidden');
                coordDestinoSeleccionado = { lat: d.lat, lng: d.lng };
                actualizarDistanciaAuto();
            });
            dropdown.appendChild(item);
        });

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ' Argentina')}&format=json&limit=4&accept-language=es&countrycodes=ar`)
            .then(r => r.json())
            .then(results => {
                results.forEach(function(r) {
                    const item = document.createElement('div');
                    item.className = 'flex items-center gap-3 px-4 py-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0';
                    item.innerHTML = `
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">location_on</span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">${r.display_name.split(',')[0]}</p>
                            <p class="text-xs text-slate-400">${r.display_name.split(',').slice(1,3).join(',').trim()}</p>
                        </div>
                    `;
                    item.addEventListener('click', function() {
                        input.value = r.display_name.split(',')[0];
                        dropdown.classList.add('hidden');
                        coordDestinoSeleccionado = { lat: parseFloat(r.lat), lng: parseFloat(r.lon) };
                        actualizarDistanciaAuto();
                    });
                    dropdown.appendChild(item);
                });
                if (dropdown.children.length > 0) dropdown.classList.remove('hidden');
            })
            .catch(() => {
                if (dropdown.children.length > 0) dropdown.classList.remove('hidden');
            });

        if (locales.length > 0) dropdown.classList.remove('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.add('hidden');
    });
}

mostrarSugerenciasOrigen();
mostrarSugerenciasDestino();

document.getElementById('select-vehiculo').addEventListener('change', function() {
    const consumos = { sedan: 8, suv: 11, camioneta: 13, moto: 4 };
    document.getElementById('input-consumo').value = consumos[this.value] || 8;
});

document.getElementById('select-combustible').addEventListener('change', function() {
    document.getElementById('input-precio').value = this.value;
});

document.getElementById('btn-calcular').addEventListener('click', function() {
    const distancia = parseFloat(document.getElementById('input-distancia').value);
    const consumo = parseFloat(document.getElementById('input-consumo').value);
    const precio = parseFloat(document.getElementById('input-precio').value);
    const origen = document.getElementById('input-origen').value.trim();
    const destino = document.getElementById('input-destino').value.trim();

    if (!distancia || !consumo) {
        Swal.fire({
            icon: 'warning',
            title: 'Faltan datos',
            text: 'Ingresá al menos la distancia y el consumo del vehículo.',
            confirmButtonColor: '#28628f'
        });
        return;
    }

    const litrosIda = (distancia * consumo) / 100;
    const litrosTotal = litrosIda * 2;

    document.getElementById('resultado-litros-ida').textContent = '~' + litrosIda.toFixed(1) + ' L';
    document.getElementById('resultado-litros-total').textContent = '~' + litrosTotal.toFixed(1) + ' L';

    if (precio) {
        const costoTotal = litrosTotal * precio;
        document.getElementById('resultado-costo').textContent = '$' + costoTotal.toLocaleString('es-AR', { maximumFractionDigits: 0 });
    } else {
        document.getElementById('resultado-costo').textContent = 'Ingresá precio/L';
    }

    if (origen && destino) {
        document.getElementById('ruta-texto').textContent = origen + ' → ' + destino;
        document.getElementById('ruta-km').textContent = distancia + ' km';
        const resumen = document.getElementById('ruta-resumen');
        resumen.classList.remove('hidden');
        resumen.classList.add('flex');
    }

    if (coordOrigenSeleccionado && coordDestinoSeleccionado) {
        actualizarMapa(coordOrigenSeleccionado, coordDestinoSeleccionado);
    }
});

function detectarUbicacion() {
    const icono = document.getElementById('icono-gps');
    const input = document.getElementById('input-origen');
    icono.classList.add('animate-spin');

    if (!navigator.geolocation) {
        icono.classList.remove('animate-spin');
        input.placeholder = 'Ej: Formosa';
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        coordOrigenSeleccionado = { lat, lng };

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=es`)
            .then(r => r.json())
            .then(data => {
                const ciudad = data.address.city || data.address.town || data.address.village || data.address.county || 'Mi ubicación';
                const provincia = data.address.state || '';
                input.value = ciudad + (provincia ? ', ' + provincia : '');
                icono.classList.remove('animate-spin');
            })
            .catch(() => {
                input.value = 'Mi ubicación actual';
                icono.classList.remove('animate-spin');
            });

        if (marcadorOrigen) mapaRuta.removeLayer(marcadorOrigen);
        marcadorOrigen = L.marker([lat, lng], {icon: iconOrigen}).addTo(mapaRuta).bindPopup('Tu ubicación').openPopup();
        mapaRuta.setView([lat, lng], 8);
        actualizarDistanciaAuto();

    }, function() {
        icono.classList.remove('animate-spin');
        input.placeholder = 'Ej: Formosa';
    }, { timeout: 8000, maximumAge: 60000 });
}

document.addEventListener('DOMContentLoaded', function() {
    detectarUbicacion();
});
</script>

@endsection