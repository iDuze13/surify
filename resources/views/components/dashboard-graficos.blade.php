@php
    $totalResenas = array_sum($resenas);
    $porcentaje = fn (int $n) => $totalResenas > 0 ? round($n * 100 / $totalResenas) : 0;
@endphp

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 my-8">

    {{-- ===== Reseñas por estado ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[#28628f]" style="font-variation-settings: 'FILL' 1;">rate_review</span>
            Reseñas
        </h3>
        <p class="text-xs text-slate-400 mb-4">Estado de las {{ $totalResenas }} reseñas recibidas</p>

        @if($totalResenas > 0)
        <div class="relative h-56">
            <canvas id="grafico-resenas" aria-label="Gráfico de reseñas aprobadas, rechazadas y a revisión manual" role="img"></canvas>
        </div>

        <ul class="mt-4 space-y-1.5 text-sm">
            <li class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Aprobadas</span>
                <span class="font-bold text-slate-800">{{ $resenas['aprobadas'] }} <span class="text-xs font-semibold text-slate-400">({{ $porcentaje($resenas['aprobadas']) }}%)</span></span>
            </li>
            <li class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>Rechazadas</span>
                <span class="font-bold text-slate-800">{{ $resenas['rechazadas'] }} <span class="text-xs font-semibold text-slate-400">({{ $porcentaje($resenas['rechazadas']) }}%)</span></span>
            </li>
            <li class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>A revisión manual</span>
                <span class="font-bold text-slate-800">{{ $resenas['revision'] }} <span class="text-xs font-semibold text-slate-400">({{ $porcentaje($resenas['revision']) }}%)</span></span>
            </li>
        </ul>

        @if($moderacion)
        <p class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400 leading-relaxed">
            Moderadas por IA: <strong class="text-slate-600">{{ $moderacion['ia'] }}</strong>
            · Manualmente: <strong class="text-slate-600">{{ $moderacion['manual'] }}</strong>
            · Sin moderar: <strong class="text-slate-600">{{ $moderacion['sin'] }}</strong>
        </p>
        @endif
        @else
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">chat_bubble_outline</span>
            <p class="text-sm">Todavía no hay reseñas.</p>
        </div>
        @endif
    </div>

    {{-- ===== Provincias más visitadas ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[#28628f]" style="font-variation-settings: 'FILL' 1;">landscape</span>
            Provincias más visitadas
        </h3>
        <p class="text-xs text-slate-400 mb-4">Destinos marcados como visitados, por provincia</p>

        @if(count($provincias['valores']) > 0)
        <div class="relative h-72">
            <canvas id="grafico-provincias" aria-label="Gráfico de las provincias más visitadas" role="img"></canvas>
        </div>
        @else
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">explore</span>
            <p class="text-sm">Todavía nadie marcó destinos como visitados.</p>
        </div>
        @endif
    </div>

    {{-- ===== Eventos más visitados ===== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[#28628f]" style="font-variation-settings: 'FILL' 1;">celebration</span>
            Eventos más visitados
        </h3>
        <p class="text-xs text-slate-400 mb-4">Eventos marcados como visitados</p>

        @if(count($eventos['valores']) > 0)
        <div class="relative h-72">
            <canvas id="grafico-eventos" aria-label="Gráfico de los eventos más visitados" role="img"></canvas>
        </div>
        @else
        <div class="flex-1 flex flex-col items-center justify-center text-center py-10 text-slate-400">
            <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
            <p class="text-sm">Todavía nadie marcó eventos como visitados.</p>
        </div>
        @endif
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        if (typeof Chart === 'undefined') return;

        const datos = @json(['resenas' => $resenas, 'provincias' => $provincias, 'eventos' => $eventos]);

        // Los colores del texto y la grilla cambian con el modo oscuro
        const esOscuro = () => document.documentElement.classList.contains('dark');
        const colorTexto = () => (esOscuro() ? '#cbd5e1' : '#475569');
        const colorGrilla = () => (esOscuro() ? 'rgba(148,163,184,0.15)' : 'rgba(148,163,184,0.25)');

        const graficos = [];

        // ----- Reseñas: dona -----
        const lienzoResenas = document.getElementById('grafico-resenas');
        if (lienzoResenas) {
            graficos.push(new Chart(lienzoResenas, {
                type: 'doughnut',
                data: {
                    labels: ['Aprobadas', 'Rechazadas', 'A revisión manual'],
                    datasets: [{
                        data: [datos.resenas.aprobadas, datos.resenas.rechazadas, datos.resenas.revision],
                        backgroundColor: ['#10b981', '#f43f5e', '#f59e0b'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: { legend: { display: false } },
                },
            }));
        }

        // ----- Barras horizontales (provincias y eventos) -----
        function barras(id, etiquetas, valores, color, nombreSerie) {
            const lienzo = document.getElementById(id);
            if (!lienzo) return;

            graficos.push(new Chart(lienzo, {
                type: 'bar',
                data: {
                    labels: etiquetas,
                    datasets: [{
                        label: nombreSerie,
                        data: valores,
                        backgroundColor: color,
                        borderRadius: 6,
                        maxBarThickness: 22,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: colorTexto() },
                            grid: { color: colorGrilla() },
                        },
                        y: {
                            ticks: { color: colorTexto() },
                            grid: { display: false },
                        },
                    },
                },
            }));
        }

        barras('grafico-provincias', datos.provincias.etiquetas, datos.provincias.valores, '#28628f', 'Visitas');
        barras('grafico-eventos', datos.eventos.etiquetas, datos.eventos.valores, '#f59e0b', 'Visitas');

        // Si se cambia entre modo claro y oscuro, se actualizan los colores
        new MutationObserver(function () {
            graficos.forEach(function (grafico) {
                const escalas = grafico.options.scales;
                if (escalas && escalas.x) {
                    escalas.x.ticks.color = colorTexto();
                    escalas.x.grid.color = colorGrilla();
                    escalas.y.ticks.color = colorTexto();
                }
                grafico.update();
            });
        }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    })();
</script>