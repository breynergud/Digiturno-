<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Coordinador — SENA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { sena: { green: '#39a900', dark: '#000', gray: '#707070', light: '#f4f4f4' } },
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900">

    <!-- Header -->
    <header class="bg-black text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-1.5 rounded-lg border border-gray-800">
                    <img src="{{ asset('images/logosena.png') }}" class="h-8 w-auto">
                </div>
                <div>
                    <h1 class="font-black text-lg leading-tight tracking-tight uppercase">Dashboard <span class="text-[#39a900]">Coordinador</span></h1>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $coordinador->persona->pers_nombres }} {{ $coordinador->persona->pers_apellidos }}</p>
                </div>
            </div>
            <nav class="flex items-center space-x-6">
                <a href="{{ route('coordinador.reporte') }}" class="text-xs font-bold uppercase tracking-widest hover:text-[#39a900] transition-colors">Reportes Semanales</a>
                <form action="{{ route('coordinador.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all">Salir</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Empresario Queue -->
        <div class="lg:col-span-4 space-y-6">
            <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-black">Cola <span class="text-[#39a900]">Empresario</span></h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Turnos E-xxx pendientes</p>
                    </div>
                    <span id="empresario-count" class="bg-gray-100 text-black px-3 py-1 rounded-full text-xs font-black">{{ count($colaEmpresario) }}</span>
                </div>

                <div id="lista-empresario" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                    @forelse($colaEmpresario as $t)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-100 p-3 rounded-2xl">
                            <div>
                                <span class="block text-sm font-black text-[#39a900]">{{ $t['tur_numero'] }}</span>
                                <span class="text-[10px] font-semibold text-gray-400">{{ \Carbon\Carbon::parse($t['tur_hora_fecha'])->format('H:i') }}</span>
                            </div>
                            <span class="text-[9px] bg-white border border-gray-200 px-2 py-1 rounded-md font-bold uppercase text-gray-500">Pendiente</span>
                        </div>
                    @empty
                        <div class="text-center py-10 opacity-30">
                            <p class="text-xs font-bold uppercase tracking-widest mt-2">No hay turnos hoy</p>
                        </div>
                    @endforelse
                </div>

                @if($coordinador->coor_estado === 'disponible' && count($colaEmpresario) > 0)
                <button onclick="aceptarTurno()" class="w-full mt-6 bg-[#39a900] hover:bg-black text-white font-black py-4 rounded-2xl shadow-xl transition-all transform active:scale-95 uppercase text-xs tracking-widest">
                    Llamar Siguiente
                </button>
                @elseif($coordinador->coor_estado === 'ocupado')
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl text-center">
                    <p class="text-[10px] font-black text-yellow-800 uppercase tracking-widest">Atención en Curso</p>
                    <p class="text-sm font-bold text-yellow-900 mt-1">Finalice desde su panel</p>
                </div>
                @endif
            </section>
        </div>

        <!-- Right Column: Advisor Status Center -->
        <div class="lg:col-span-8 space-y-6">
            <section class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-xl font-black text-black">Monitor de <span class="text-[#39a900]">Asesores</span></h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estado y Colas en Tiempo Real</p>
                    </div>
                </div>

                <div id="asesores-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($asesores as $a)
                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-5 relative overflow-hidden group">
                        <!-- Status Bar -->
                        <div class="absolute top-0 left-0 w-full h-1 {{ $a->ase_estado === 'disponible' ? 'bg-[#39a900]' : ($a->ase_estado === 'ocupado' ? 'bg-orange-500' : 'bg-gray-400') }}"></div>
                        
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-200">
                                    <span class="font-black text-gray-400">{{ substr($a->persona->pers_nombres, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-black lines-1">{{ $a->persona->pers_nombres }}</h3>
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#39a900]">
                                        {{ $a->ase_tipo_asesor == 'G' ? 'General' : ($a->ase_tipo_asesor == 'V' ? 'Víctimas' : 'Prioritario') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $a->ase_estado === 'disponible' ? 'bg-green-100 text-green-700' : ($a->ase_estado === 'ocupado' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $a->ase_estado }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Nueva Cola</label>
                                <select onchange="cambiarTipo(this, {{ $a->ase_id }})" class="bg-white border border-gray-200 text-[10px] font-bold rounded-lg px-2 py-1 outline-none focus:border-[#39a900]">
                                    <option value="G" {{ $a->ase_tipo_asesor == 'G' ? 'selected' : '' }}>General</option>
                                    <option value="V" {{ $a->ase_tipo_asesor == 'V' ? 'selected' : '' }}>Víctimas</option>
                                    <option value="P" {{ $a->ase_tipo_asesor == 'P' ? 'selected' : '' }}>Prioritario</option>
                                </select>
                            </div>
                            @if($a->ase_estado === 'ocupado')
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Atendiendo</p>
                                <p class="text-xs font-black text-black">Verificando...</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function aceptarTurno() {
            try {
                const res = await fetch('{{ route('coordinador.aceptar') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                });
                const data = await res.json();
                if (data.success) {
                    alert('Turno aceptado: ' + data.codigo_turno);
                    location.reload();
                } else {
                    alert(data.error);
                }
            } catch (e) { console.error(e); }
        }

        async function cambiarTipo(select, ase_id) {
            const nuevo_tipo = select.value;
            try {
                const res = await fetch('{{ route('coordinador.reasignar') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ ase_id, nuevo_tipo })
                });
                const data = await res.json();
                if (data.success) {
                    // Feedback visual suave
                    select.classList.add('bg-green-50', 'border-green-500');
                    setTimeout(() => select.classList.remove('bg-green-50', 'border-green-500'), 2000);
                }
            } catch (e) { console.error(e); }
        }

        // Polling para actualizar estados
        setInterval(async () => {
            try {
                const res = await fetch('{{ route('coordinador.api.estado') }}');
                const data = await res.json();
                // Aquí podrías actualizar el DOM dinámicamente sin recargar
                // Por ahora solo actualizamos el contador de la cola para feedback visual
                document.getElementById('empresario-count').innerText = data.colaEmpresario.length;
            } catch (e) { console.error(e); }
        }, 5000);

        // Cerrar sesión al cerrar la pestaña/navegador
        window.addEventListener('unload', function() {
            // Si no es un refresco de página (navigation type 1)
            const isRefresh = window.performance && window.performance.navigation.type === 1;
            if (!isRefresh) {
                const blob = new Blob([JSON.stringify({ _token: CSRF_TOKEN })], { type: 'application/json' });
                navigator.sendBeacon('{{ route("coordinador.logout") }}', blob);
            }
        });
    </script>
</body>
</html>
