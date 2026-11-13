<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte Surify</title>
    {{-- Estilos simples: DomPDF no soporta flex ni grid, se arma todo con tablas --}}
    <style>
        @page {
            margin: 90px 40px 60px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 50px;
            border-bottom: 2px solid #28628f;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }

        .marca {
            font-size: 20px;
            font-weight: bold;
            color: #28628f;
            letter-spacing: -0.5px;
        }

        .sub {
            color: #64748b;
            font-size: 9px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
            color: #0f172a;
        }

        h2 {
            font-size: 14px;
            color: #28628f;
            margin: 22px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        h3 {
            font-size: 11px;
            margin: 14px 0 6px;
            color: #334155;
        }

        .salto {
            page-break-before: always;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla th {
            background: #28628f;
            color: #fff;
            text-align: left;
            font-size: 9px;
            padding: 5px 6px;
        }

        .tabla td {
            padding: 4px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .tabla tr:nth-child(even) td {
            background: #f8fafc;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .tabla th.num {
            text-align: right;
        }

        .kpis td {
            width: 25%;
            padding: 4px;
        }

        .kpi {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            background: #f8fafc;
        }

        .kpi .valor {
            font-size: 18px;
            font-weight: bold;
            color: #28628f;
        }

        .kpi .etiqueta {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }

        .barra-fondo {
            background: #e2e8f0;
            height: 8px;
            border-radius: 4px;
        }

        .barra {
            background: #28628f;
            height: 8px;
            border-radius: 4px;
        }

        .vacio {
            color: #94a3b8;
            font-style: italic;
            padding: 6px 0;
        }

        .dos-col td.col {
            width: 50%;
            vertical-align: top;
        }

        .dos-col td.col-izq {
            padding-right: 10px;
        }

        .dos-col td.col-der {
            padding-left: 10px;
        }

        .estado {
            font-size: 8px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
        }

        .ok {
            background: #d1fae5;
            color: #047857;
        }

        .pend {
            background: #fef3c7;
            color: #b45309;
        }

        .no {
            background: #fee2e2;
            color: #b91c1c;
        }

        .pagina:after {
            content: "Página " counter(page) " de " counter(pages);
        }

        .nota {
            font-size: 8px;
            color: #64748b;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    @php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', '.');
    // Fila con barra proporcional (para rankings)
    $max = fn ($coleccion, $campo = 'valor') => max(1, collect($coleccion)->max($campo) ?? 1);
    @endphp

    <header>
        <table>
            <tr>
                <td>
                    <span class="marca">Surify</span><br>
                    <span class="sub">Turismo Nacional · Panel de administración</span>
                </td>
                <td style="text-align:right;" class="sub">
                    Período: <strong>{{ $desde->format('d/m/Y') }}</strong> al <strong>{{ $hasta->format('d/m/Y') }}</strong><br>
                    Generado el {{ $generado->format('d/m/Y H:i') }} por {{ $autor }}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table>
            <tr>
                <td>Surify · Reporte de uso interno</td>
                <td style="text-align:right;"><span class="pagina"></span></td>
            </tr>
        </table>
    </footer>


    {{-- ==================== RESUMEN ==================== --}}
    <h1>Reporte general</h1>
    <p class="sub">Resumen de la actividad de la plataforma en el período seleccionado.</p>

    <table class="kpis" style="margin-top:10px;">
        <tr>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['usuarios_nuevos']) }}</div>
                    <div class="etiqueta">Usuarios nuevos</div>
                    <div class="nota">de {{ $fmt($resumen['usuarios_total']) }} en total</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['resenas']) }}</div>
                    <div class="etiqueta">Reseñas escritas</div>
                    <div class="nota">Promedio aprobadas: {{ $resumen['promedio'] ?: '—' }} / 5</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['visitas']) }}</div>
                    <div class="etiqueta">Lugares marcados visitados</div>
                    <div class="nota">destinos y eventos</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['favoritos']) }}</div>
                    <div class="etiqueta">Favoritos agregados</div>
                    <div class="nota">&nbsp;</div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['sugerencias']) }}</div>
                    <div class="etiqueta">Sugerencias de eventos</div>
                    <div class="nota">recibidas en el período</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['destinos_total']) }}</div>
                    <div class="etiqueta">Destinos cargados</div>
                    <div class="nota">en total</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['eventos_activos']) }}</div>
                    <div class="etiqueta">Eventos publicados</div>
                    <div class="nota">en total</div>
                </div>
            </td>
            <td>
                <div class="kpi">
                    <div class="valor">{{ $fmt($resumen['usuarios_total']) }}</div>
                    <div class="etiqueta">Usuarios registrados</div>
                    <div class="nota">en total</div>
                </div>
            </td>
        </tr>
    </table>

    <p class="nota">Secciones incluidas: {{ collect($secciones)->map(fn ($s) => $titulos[$s])->implode(', ') }}.</p>

    {{-- ==================== USUARIOS ==================== --}}
    @isset($usuarios)
    <h2>Usuarios</h2>

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Estado de las cuentas (total)</h3>
                <table class="tabla">
                    <tr><th>Dato</th><th class="num">Cantidad</th></tr>
                    <tr><td>Usuarios registrados</td><td class="num">{{ $fmt($usuarios['total']) }}</td></tr>
                    <tr><td>Nuevos en el período</td><td class="num">{{ $fmt($usuarios['nuevos']) }}</td></tr>
                    <tr><td>Ingresan con Google</td><td class="num">{{ $fmt($usuarios['con_google']) }}</td></tr>
                    <tr><td>Con verificación en dos pasos (2FA)</td><td class="num">{{ $fmt($usuarios['con_2fa']) }}</td></tr>
                    <tr><td>Perfil público</td><td class="num">{{ $fmt($usuarios['publicos']) }}</td></tr>
                    <tr><td>Perfil privado</td><td class="num">{{ $fmt($usuarios['privados']) }}</td></tr>
                </table>
            </td>
            <td class="col col-der">
                <h3>Altas por mes</h3>
                @if($usuarios['por_mes']->isNotEmpty())
                @php $m = max(1, $usuarios['por_mes']->max()); @endphp
                <table class="tabla">
                    <tr><th>Mes</th><th style="width:45%;"></th><th class="num">Altas</th></tr>
                    @foreach($usuarios['por_mes'] as $mes => $cantidad)
                    <tr>
                        <td>{{ $mes }}</td>
                        <td><div class="barra-fondo"><div class="barra" style="width: {{ round($cantidad / $m * 100) }}%;"></div></div></td>
                        <td class="num">{{ $cantidad }}</td>
                    </tr>
                    @endforeach
                </table>
                @else
                <p class="vacio">No hubo altas en el período.</p>
                @endif
            </td>
        </tr>
    </table>

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Últimos registrados en el período</h3>
                @if($usuarios['ultimos']->isNotEmpty())
                <table class="tabla">
                    <tr><th>Nombre</th><th>Ingreso</th><th class="num">Fecha</th></tr>
                    @foreach($usuarios['ultimos'] as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->google_id ? 'Google' : 'Email' }}</td>
                        <td class="num">{{ $u->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </table>
                @else
                <p class="vacio">Sin registros nuevos.</p>
                @endif
            </td>
            <td class="col col-der">
                <h3>Usuarios más activos</h3>
                @if($usuarios['mas_activos']->isNotEmpty())
                <table class="tabla">
                    <tr><th>Nombre</th><th class="num">Reseñas</th><th class="num">Visitas</th></tr>
                    @foreach($usuarios['mas_activos'] as $u)
                    <tr><td>{{ $u['nombre'] }}</td><td class="num">{{ $u['resenas'] }}</td><td class="num">{{ $u['visitas'] }}</td></tr>
                    @endforeach
                </table>
                <p class="nota">Reseñas escritas y destinos marcados como visitados en el período.</p>
                @else
                <p class="vacio">Sin actividad en el período.</p>
                @endif
            </td>
        </tr>
    </table>
    @endisset

    {{-- ==================== DESTINOS ==================== --}}
    @isset($destinos)
    <h2 class="salto">Destinos</h2>

    @foreach([
        ['titulo' => 'Más visitados', 'datos' => $destinos['mas_visitados'], 'columna' => 'Visitas', 'vacio' => 'Nadie marcó destinos como visitados en el período.'],
        ['titulo' => 'Más agregados a favoritos', 'datos' => $destinos['mas_favoritos'], 'columna' => 'Favoritos', 'vacio' => 'No se agregaron favoritos en el período.'],
    ] as $bloque)
    <h3>{{ $bloque['titulo'] }} (top 10)</h3>
    @if($bloque['datos']->isNotEmpty())
    @php $m = $max($bloque['datos']); @endphp
    <table class="tabla">
        <tr><th style="width:4%;">#</th><th>Destino</th><th>Provincia</th><th style="width:30%;"></th><th class="num">{{ $bloque['columna'] }}</th></tr>
        @foreach($bloque['datos'] as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d['nombre'] }}</td>
            <td>{{ $d['provincia'] }}</td>
            <td><div class="barra-fondo"><div class="barra" style="width: {{ round($d['valor'] / $m * 100) }}%;"></div></div></td>
            <td class="num">{{ $d['valor'] }}</td>
        </tr>
        @endforeach
    </table>
    @else
    <p class="vacio">{{ $bloque['vacio'] }}</p>
    @endif
    @endforeach

    <h3>Mejor calificados (reseñas aprobadas del período)</h3>
    @if($destinos['mejor_calif']->isNotEmpty())
    <table class="tabla">
        <tr><th style="width:4%;">#</th><th>Destino</th><th>Provincia</th><th class="num">Promedio</th><th class="num">Reseñas</th></tr>
        @foreach($destinos['mejor_calif'] as $i => $d)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $d['nombre'] }}</td>
            <td>{{ $d['provincia'] }}</td>
            <td class="num">{{ number_format($d['promedio'], 1, ',', '') }} / 5</td>
            <td class="num">{{ $d['cantidad'] }}</td>
        </tr>
        @endforeach
    </table>
    @else
    <p class="vacio">No hay reseñas aprobadas de destinos en el período.</p>
    @endif

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Destinos por provincia (total)</h3>
                <table class="tabla">
                    <tr><th>Provincia</th><th class="num">Destinos</th></tr>
                    @foreach($destinos['por_provincia'] as $p)
                    <tr><td>{{ $p['nombre'] }}</td><td class="num">{{ $p['valor'] }}</td></tr>
                    @endforeach
                </table>
            </td>
            <td class="col col-der">
                <h3>Destinos por categoría (total)</h3>
                <table class="tabla">
                    <tr><th>Categoría</th><th class="num">Destinos</th></tr>
                    @foreach($destinos['por_categoria'] as $categoria => $cantidad)
                    <tr><td>{{ $categoria }}</td><td class="num">{{ $cantidad }}</td></tr>
                    @endforeach
                </table>
                <p class="nota">Total de destinos cargados: {{ $destinos['total'] }}.</p>
            </td>
        </tr>
    </table>
    @endisset

    {{-- ==================== RESEÑAS ==================== --}}
    @isset($resenas)
    <h2 class="salto">Reseñas</h2>

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Estado de las reseñas del período</h3>
                <table class="tabla">
                    <tr><th>Estado</th><th class="num">Cantidad</th></tr>
                    <tr><td>Aprobadas</td><td class="num">{{ $resenas['aprobadas'] }}</td></tr>
                    <tr><td>Pendientes de moderación</td><td class="num">{{ $resenas['pendientes'] }}</td></tr>
                    <tr><td>Rechazadas</td><td class="num">{{ $resenas['rechazadas'] }}</td></tr>
                    <tr><td><strong>Total</strong></td><td class="num"><strong>{{ $resenas['total'] }}</strong></td></tr>
                    <tr><td>Publicadas como anónimas</td><td class="num">{{ $resenas['anonimas'] }}</td></tr>
                </table>
            </td>
            <td class="col col-der">
                <h3>Calificaciones (aprobadas) · promedio {{ $resenas['promedio'] ? number_format($resenas['promedio'], 1, ',', '') : '—' }} / 5</h3>
                @php $m = max(1, max($resenas['estrellas'])); @endphp
                <table class="tabla">
                    <tr><th>Estrellas</th><th style="width:55%;"></th><th class="num">Cant.</th></tr>
                    @foreach($resenas['estrellas'] as $estrellas => $cantidad)
                    <tr>
                        <td>{{ $estrellas }} {{ $estrellas == 1 ? 'estrella' : 'estrellas' }}</td>
                        <td><div class="barra-fondo"><div class="barra" style="width: {{ round($cantidad / $m * 100) }}%; background:#f59e0b;"></div></div></td>
                        <td class="num">{{ $cantidad }}</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <h3>Últimas reseñas del período</h3>
    @if($resenas['ultimas']->isNotEmpty())
    <table class="tabla">
        <tr><th style="width:12%;">Fecha</th><th style="width:22%;">Lugar</th><th style="width:16%;">Autor</th><th class="num" style="width:8%;">Calif.</th><th>Comentario</th><th style="width:11%;">Estado</th></tr>
        @foreach($resenas['ultimas'] as $r)
        <tr>
            <td>{{ $r->created_at->timezone('America/Argentina/Buenos_Aires')->format('d/m/Y') }}</td>
            <td>{{ $r->destino->nombre ?? $r->evento->nombre ?? '—' }}</td>
            <td>{{ $r->anonima ? 'Anónimo' : ($r->user->name ?? '—') }}</td>
            <td class="num">{{ $r->calificacion }} / 5</td>
            <td>{{ \Illuminate\Support\Str::limit($r->comentario, 90) }}</td>
            <td>
                @if($r->aprobada === true)<span class="estado ok">Aprobada</span>
                @elseif($r->aprobada === false)<span class="estado no">Rechazada</span>
                @else<span class="estado pend">Pendiente</span>@endif
            </td>
        </tr>
        @endforeach
    </table>
    @else
    <p class="vacio">No se escribieron reseñas en el período.</p>
    @endif
    @endisset

    {{-- ==================== EVENTOS ==================== --}}
    @isset($eventos)
    <h2 class="salto">Eventos y sugerencias</h2>

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Sugerencias de usuarios (recibidas en el período)</h3>
                <table class="tabla">
                    <tr><th>Estado</th><th class="num">Cantidad</th></tr>
                    <tr><td>Recibidas</td><td class="num">{{ $eventos['sugerencias'] }}</td></tr>
                    <tr><td>Aprobadas (publicadas)</td><td class="num">{{ $eventos['sug_aprobadas'] }}</td></tr>
                    <tr><td>Pendientes de revisión</td><td class="num">{{ $eventos['sug_pendientes'] }}</td></tr>
                </table>
                <p class="nota">Las sugerencias rechazadas se eliminan, por eso no figuran.</p>
            </td>
            <td class="col col-der">
                <h3>Eventos más marcados como visitados</h3>
                @if($eventos['mas_visitados']->isNotEmpty())
                <table class="tabla">
                    <tr><th>Evento</th><th class="num">Visitas</th></tr>
                    @foreach($eventos['mas_visitados'] as $e)
                    <tr><td>{{ $e['nombre'] }}</td><td class="num">{{ $e['valor'] }}</td></tr>
                    @endforeach
                </table>
                @else
                <p class="vacio">Nadie marcó eventos como visitados en el período.</p>
                @endif
            </td>
        </tr>
    </table>

    <h3>Eventos que se realizan en el período ({{ $eventos['en_periodo']->count() }})</h3>
    @if($eventos['en_periodo']->isNotEmpty())
    <table class="tabla">
        <tr><th>Evento</th><th>Tipo</th><th>Provincia</th><th>Destino</th><th class="num">Desde</th><th class="num">Hasta</th><th>Origen</th></tr>
        @foreach($eventos['en_periodo'] as $e)
        <tr>
            <td>{{ $e->nombre }}</td>
            <td>{{ $e->tipo ?: '—' }}</td>
            <td>{{ $e->provincia->nombre ?? '—' }}</td>
            <td>{{ $e->destino->nombre ?? '—' }}</td>
            <td class="num">{{ \Illuminate\Support\Carbon::parse($e->fecha_inicio)->format('d/m/Y') }}</td>
            <td class="num">{{ $e->fecha_fin ? \Illuminate\Support\Carbon::parse($e->fecha_fin)->format('d/m/Y') : '—' }}</td>
            <td>{{ $e->sugerido_por ? 'Sugerido' : 'Admin' }}</td>
        </tr>
        @endforeach
    </table>

    <table class="dos-col">
        <tr>
            <td class="col col-izq">
                <h3>Por provincia</h3>
                <table class="tabla">
                    <tr><th>Provincia</th><th class="num">Eventos</th></tr>
                    @foreach($eventos['por_provincia'] as $nombre => $cantidad)
                    <tr><td>{{ $nombre }}</td><td class="num">{{ $cantidad }}</td></tr>
                    @endforeach
                </table>
            </td>
            <td class="col col-der">
                <h3>Por tipo</h3>
                <table class="tabla">
                    <tr><th>Tipo</th><th class="num">Eventos</th></tr>
                    @foreach($eventos['por_tipo'] as $nombre => $cantidad)
                    <tr><td>{{ $nombre }}</td><td class="num">{{ $cantidad }}</td></tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>
    @else
    <p class="vacio">No hay eventos publicados con fecha dentro del período.</p>
    @endif
    <p class="nota">Eventos publicados en total: {{ $eventos['activos_total'] }}.</p>
    @endisset

</body>

</html>