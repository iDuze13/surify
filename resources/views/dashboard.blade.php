@extends('layouts.admin')

@section('title', 'Surify - Panel de Administración')

@section('content')

{{-- Bienvenida --}}
<section class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-2">Panel de Control</p>
    <h1 class="text-5xl font-black text-slate-900 tracking-tight leading-none mb-3" style="font-family: 'Inter', sans-serif;">
        Bienvenido, {{ auth()->user()->name }}.
    </h1>
    <p class="text-slate-500 text-base max-w-2xl">Este es el estado actual de Surify. Gestioná destinos, usuarios, roles y contenido desde acá.</p>
</section>

{{-- Métricas --}}
<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-[#28628f]/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[#28628f]">landscape</span>
            </div>
            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">trending_up</span> Activos
            </span>
        </div>
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Destinos</p>
        <p class="text-3xl font-black text-slate-800 contador" data-target="{{ $countDestinos }}">00</p>
        <div class="mt-3 h-1 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full bg-[#28628f] w-3/4 rounded-full"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-500">person_add</span>
            </div>
            <span class="text-xs font-bold text-slate-400">Total registrados</span>
        </div>
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Usuarios</p>
        <p class="text-3xl font-black text-slate-800 contador" data-target="{{ $countUsuarios }}">00</p>
        <div class="mt-3 flex gap-1 items-end h-8">
            <div class="w-full rounded-t-sm bg-amber-200" style="height:40%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:60%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:45%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:80%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:55%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:90%"></div>
            <div class="w-full rounded-t-sm bg-amber-200" style="height:100%"></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-b-4 border-slate-200 border-b-rose-400 shadow-sm p-6 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-rose-400">celebration</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 bg-rose-50 text-rose-500 rounded-full border border-rose-100">PRÓXIMOS</span>
        </div>
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Eventos</p>
        <p class="text-3xl font-black text-slate-800 contador" data-target="{{ $countEventos }}">00</p>
        <a href="{{ route('eventos.index') }}" class="mt-3 text-rose-400 text-xs font-bold flex items-center gap-1 hover:underline text-decoration-none">
            Ver todos <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
        </a>
    </div>
</section>

{{-- Tabla + Acciones --}}
<div class="flex flex-col lg:flex-row gap-6">

    {{-- Tabla provincias --}}
    <section class="flex-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-black text-slate-800" style="font-family: 'Inter', sans-serif;">Provincias Cargadas</h2>
            <a href="#" class="px-4 py-2 bg-[#28628f] text-white text-xs font-bold rounded-full hover:bg-[#1a4669] transition-all text-decoration-none">
                + Nueva Provincia
            </a>
        </div>
        @php
        $provincias = $provincias ?? collect();
        @endphp
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Provincia</th>
                        <th class="text-left py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Región</th>
                        <th class="text-left py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Destinos</th>
                        <th class="text-right py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($provincias as $provincia)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4">
                            <span class="text-sm font-bold text-slate-800">{{ optional($provincia)->nombre }}</span>
                        </td>
                        <td class="py-4">
                            <span class="text-sm text-slate-500">{{ optional($provincia)->region ?? '—' }}</span>
                        </td>
                        <td class="py-4">
                            <span class="text-sm font-bold text-[#28628f]">{{ optional($provincia)->destinos_count ?? 0 }}</span>
                        </td>
                        @php
                        $imagenes = $provincia->imagenes->map(function ($i) {
                            return ['id' => $i->id, 'url' => $i->url];
                        });
                        @endphp
                        <td class="py-4 text-right">
                            <button onclick='abrirEditarProvincia({{ $provincia->id }}, @json($provincia->nombre), @json($provincia->region ?? ""), @json($provincia->descripcion ?? ""), @json($imagenes))'
                                class="material-symbols-outlined text-[#28628f] hover:scale-110 transition-transform text-[20px] cursor-pointer bg-transparent border-none">
                                edit_square
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">folder_open</span>
                            <p class="text-sm text-slate-400">No hay provincias cargadas todavía.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Panel lateral --}}
    <aside class="w-full lg:w-72 flex flex-col gap-4">

        {{-- Acciones rápidas --}}
        <div class="bg-slate-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-400 text-[20px]">bolt</span>
                Acciones Rápidas
            </h3>
            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.destinos.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium text-decoration-none">
                    <span class="material-symbols-outlined text-amber-400 text-[20px]">add_location</span>
                    Agregar Destino
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium text-decoration-none">
                    <span class="material-symbols-outlined text-blue-300 text-[20px]">celebration</span>
                    Cargar Evento
                </a>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium text-decoration-none">
                    <span class="material-symbols-outlined text-emerald-400 text-[20px]">admin_panel_settings</span>
                    Gestionar Roles
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium text-decoration-none">
                    <span class="material-symbols-outlined text-purple-300 text-[20px]">group</span>
                    Ver Usuarios
                </a>
                <a href="{{ route('admin.resenas.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium text-decoration-none">
                    <span class="material-symbols-outlined text-rose-300 text-[20px]">chat_bubble</span>
                    Moderar Reseñas
                </a>

                {{-- Botón Backup --}}
                <form method="POST" action="{{ route('admin.backup') }}" id="form-backup">
                    @csrf
                    <button type="button" onclick="confirmarBackup()"
                        class="flex items-center gap-3 p-3 rounded-xl bg-white/10 hover:bg-white/20 transition-colors text-white text-sm font-medium w-full text-left">
                        <span class="material-symbols-outlined text-yellow-300 text-[20px]">database</span>
                        Generar Backup BD
                    </button>
                </form>
            </div>
        </div>

        {{-- Estado del sistema --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h4 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Estado del Sistema</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Base de Datos</span>
                    <span class="text-xs font-bold text-emerald-500 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span> Online
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Laravel</span>
                    <span class="text-xs font-bold text-slate-600">v{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Entorno</span>
                    <span class="text-xs font-bold text-slate-600">{{ app()->environment() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Roles creados</span>
                    <span class="text-xs font-bold text-[#28628f]">{{ $countRoles }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-slate-500">Permisos definidos</span>
                    <span class="text-xs font-bold text-[#28628f]">{{ $countPermisos }}</span>
                </div>
            </div>
        </div>
    </aside>
</div>

{{-- Modal Editar Provincia --}}
<div id="modal-editar-provincia" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Editar Provincia</h2>
            <button onclick="document.getElementById('modal-editar-provincia').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="form-editar-provincia" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nombre</label>
                <input type="text" name="nombre" id="editar-provincia-nombre" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Región</label>
                <input type="text" name="region" id="editar-provincia-region"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Descripción</label>
                <textarea name="descripcion" id="editar-provincia-descripcion" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] resize-none"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Imágenes actuales</label>
                <div id="imagenes-actuales" class="flex flex-wrap gap-2 min-h-[40px]">
                    <p class="text-xs text-slate-400">No hay imágenes cargadas.</p>
                </div>
            </div>

            <div class="space-y-1" id="nuevas-imagenes-urls-container">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Agregar imágenes (URL)</label>
                <div class="flex flex-col gap-2" id="inputs-urls">
                    <input type="text" name="imagenes_urls[]" placeholder="https://ejemplo.com/foto1.jpg"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
                </div>
                <button type="button" onclick="agregarInputUrl()"
                    class="text-xs font-bold text-[#28628f] hover:underline mt-1">
                    + Agregar otra URL
                </button>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all">
                    Guardar cambios
                </button>
                <button type="button" onclick="document.getElementById('modal-editar-provincia').classList.add('hidden')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmarBackup() {
        Swal.fire({
            title: '¿Generar backup?',
            text: 'Se generará un backup de la base de datos y se subirá a Cloudinary.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#28628f',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('form-backup').submit();
            }
        });
    }

    function abrirEditarProvincia(id, nombre, region, descripcion, imagenes) {
        document.getElementById('editar-provincia-nombre').value = nombre;
        document.getElementById('editar-provincia-region').value = region;
        document.getElementById('editar-provincia-descripcion').value = descripcion;
        document.getElementById('form-editar-provincia').action = `/admin/provincias/${id}`;

        const container = document.getElementById('imagenes-actuales');
        container.innerHTML = '';

        if (imagenes && imagenes.length > 0) {
            imagenes.forEach(img => {
                agregarImagenAlModal(container, img);
            });
        } else {
            container.innerHTML = '<p class="text-xs text-slate-400">No hay imágenes cargadas todavía.</p>';
        }

        window.imagenesActualesCount = imagenes ? imagenes.length : 0;

        const inputsUrls = document.getElementById('inputs-urls');
        inputsUrls.innerHTML = '';

        const boton = document.querySelector('#nuevas-imagenes-urls-container button');

        if (window.imagenesActualesCount >= 3) {
            inputsUrls.innerHTML = '<p class="text-xs text-amber-600 font-semibold">Ya alcanzaste el máximo de 3 imágenes. Eliminá alguna para agregar otra.</p>';
            if (boton) boton.classList.add('hidden');
        } else {
            inputsUrls.innerHTML = `
            <input type="text" name="imagenes_urls[]" placeholder="https://ejemplo.com/foto1.jpg"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">`;
            if (boton) boton.classList.remove('hidden');
        }

        document.getElementById('modal-editar-provincia').classList.remove('hidden');
    }

    function agregarInputUrl() {
        const inputsUrls = document.getElementById('inputs-urls');
        const inputsActuales = inputsUrls.querySelectorAll('input').length;
        const totalConNuevos = (window.imagenesActualesCount || 0) + inputsActuales;

        if (totalConNuevos >= 3) {
            Swal.fire({
                icon: 'warning',
                title: 'Límite alcanzado',
                text: 'Una provincia puede tener un máximo de 3 imágenes.',
                confirmButtonColor: '#28628f'
            });
            return;
        }

        const nuevoInput = document.createElement('input');
        nuevoInput.type = 'text';
        nuevoInput.name = 'imagenes_urls[]';
        nuevoInput.placeholder = 'https://ejemplo.com/foto.jpg';
        nuevoInput.className = 'w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]';
        inputsUrls.appendChild(nuevoInput);

        const boton = document.querySelector('#nuevas-imagenes-urls-container button');
        if ((window.imagenesActualesCount || 0) + inputsUrls.querySelectorAll('input').length >= 3 && boton) {
            boton.classList.add('hidden');
        }
    }

    function agregarImagenAlModal(container, img) {
        const div = document.createElement('div');
        div.id = `img-${img.id}`;
        div.className = 'relative w-20 h-20 rounded-xl overflow-hidden border border-slate-200 group';
        div.innerHTML = `
        <img src="${img.url}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <button type="button" onclick="eliminarImagen(${img.id})"
                class="text-white text-[10px] font-bold bg-rose-500 px-2 py-1 rounded-lg">
                Eliminar
            </button>
        </div>`;
        container.appendChild(div);
    }

    function eliminarImagen(id) {
        fetch(`/admin/provincias/imagenes/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(r => {
                if (r.ok) {
                    const el = document.getElementById(`img-${id}`);
                    if (el) el.remove();

                    window.imagenesActualesCount = Math.max(0, (window.imagenesActualesCount || 0) - 1);

                    const boton = document.querySelector('#nuevas-imagenes-urls-container button');
                    if (boton) boton.classList.remove('hidden');

                    const inputsUrls = document.getElementById('inputs-urls');
                    if (inputsUrls.querySelector('p')) {
                        inputsUrls.innerHTML = `
                        <input type="text" name="imagenes_urls[]" placeholder="https://ejemplo.com/foto1.jpg"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">`;
                    }
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar la imagen',
                    confirmButtonColor: '#28628f'
                });
            });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.contador').forEach(function(el) {
            var target = parseInt(el.getAttribute('data-target'));
            var duration = 1500;
            var step = target / (duration / 16);
            var current = 0;
            var timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = String(Math.floor(current)).padStart(2, '0');
            }, 16);
        });
    });
</script>

@endsection