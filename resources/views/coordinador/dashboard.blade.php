<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno — Panel Coordinador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        ape: { 
                            blue: '#10069f', 
                            yellow: '#ffb500', 
                            orange: '#ff671f',
                            dark: '#0a0455', 
                            gray: '#707070', 
                            light: '#f4f4f4' 
                        } 
                    },
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900">

    <!-- Header -->
    <header class="bg-[#0a0455] text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="bg-white px-3 py-1 rounded-lg border border-white/20">
                    <div class="text-xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                        <span class="leading-none">APE</span>
                    </div>
                </div>
                <div>
                    <h1 class="font-black text-lg leading-tight tracking-tight uppercase">Dashboard <span class="text-[#ffb500]">Coordinador</span></h1>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $coordinador->persona->pers_nombres }} {{ $coordinador->persona->pers_apellidos }}</p>
                </div>
            </div>
            <nav class="flex items-center space-x-6">
                <button onclick="openModal()" class="bg-[#ffb500] hover:bg-[#e6a300] text-[#0a0455] text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all shadow-md border-b-4 border-[#b38600]">Registrar Asesor</button>
                <a href="{{ route('coordinador.reporte') }}" class="text-xs font-bold uppercase tracking-widest hover:text-[#ffb500] transition-colors">Reportes Semanales</a>
                <form action="{{ route('coordinador.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-all shadow-lg border-b-4 border-red-900">Salir</button>
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
                        <h2 class="text-xl font-black text-black">Cola <span class="text-[#10069f]">Empresario</span></h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Turnos E-xxx pendientes</p>
                    </div>
                    <span id="empresario-count" class="bg-gray-100 text-black px-3 py-1 rounded-full text-xs font-black">{{ count($colaEmpresario) }}</span>
                </div>

                <div id="lista-empresario" class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                    @forelse($colaEmpresario as $t)
                        <div class="flex items-center justify-between bg-gray-50 border border-gray-100 p-3 rounded-2xl">
                            <div>
                                <span class="block text-sm font-black text-[#10069f]">{{ $t['tur_numero'] }}</span>
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
                <button onclick="aceptarTurno()" class="w-full mt-6 bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-4 rounded-2xl shadow-xl transition-all transform active:scale-95 uppercase text-xs tracking-widest border-b-4 border-[#0a0455]">
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
                        <h2 class="text-xl font-black text-black">Monitor de <span class="text-[#10069f]">Asesores</span></h2>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estado y Colas en Tiempo Real</p>
                    </div>
                </div>

                <div id="asesores-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($asesores as $a)
                    <div class="bg-gray-50 border border-gray-100 rounded-3xl p-5 relative overflow-hidden group">
                        <!-- Status Bar -->
                        <div class="absolute top-0 left-0 w-full h-1 {{ $a->ase_estado === 'disponible' ? 'bg-[#10069f]' : ($a->ase_estado === 'ocupado' ? 'bg-[#ff671f]' : 'bg-gray-400') }}"></div>
                        
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center border border-gray-200">
                                    <span class="font-black text-gray-400">{{ substr($a->persona->pers_nombres, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-black lines-1">{{ $a->persona->pers_nombres }}</h3>
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#10069f]">
                                        {{ $a->ase_tipo_asesor == 'G' ? 'General' : ($a->ase_tipo_asesor == 'V' ? 'Víctimas' : 'Prioritario') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ $a->ase_estado === 'disponible' ? 'bg-blue-100 text-[#10069f]' : ($a->ase_estado === 'ocupado' ? 'bg-orange-100 text-[#ff671f]' : 'bg-gray-100 text-gray-600') }}">
                                    {{ $a->ase_estado }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-between">
                            <div>
                                <label class="text-[9px] font-black text-gray-400 uppercase block mb-1">Nueva Cola</label>
                                <select onchange="cambiarTipo(this, {{ $a->ase_id }})" class="bg-white border border-gray-200 text-[10px] font-bold rounded-lg px-2 py-1 outline-none focus:border-[#10069f]">
                                    <option value="G" {{ $a->ase_tipo_asesor == 'G' ? 'selected' : '' }}>General</option>
                                    <option value="V" {{ $a->ase_tipo_asesor == 'V' ? 'selected' : '' }}>Víctimas</option>
                                </select>
                            </div>
                            @if($a->ase_estado === 'ocupado')
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Atendiendo</p>
                                <p class="text-xs font-black text-black">Activo</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    <!-- ─── MODAL CIERRE DE SESIÓN (Timeout) ────────────────────── -->
    <div id="modal-timeout" class="fixed inset-0 z-[200] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] overflow-hidden w-full max-w-md shadow-2xl transform transition-all border border-white/10">
            <div class="h-2 bg-[#10069f] w-full"></div>
            <div class="p-8">
                <h3 class="text-3xl font-black text-[#0a0455] mb-4 uppercase tracking-tighter">Cierre de Sesión</h3>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed font-medium">
                    Su Sesión se va a cerrar en un minuto. Cancelar para seguir trabajando o Aceptar para cerrar ahora
                </p>
                <div class="flex gap-4">
                    <button id="btn-timeout-aceptar" onclick="logoutAhora()" class="flex-1 bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-4 rounded-xl text-lg uppercase transition-all shadow-lg border-b-4 border-[#0a0455]">
                        ACEPTAR (<span id="timeout-countdown">60</span>)
                    </button>
                    <button onclick="continuarSesion()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-black py-4 rounded-xl text-lg uppercase transition-colors border border-gray-200">
                        CANCELAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div id="modalAsesor" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] w-full max-w-lg overflow-hidden shadow-2xl transform transition-all">
            <div class="bg-[#0a0455] p-6 text-white flex justify-between items-center">
                <h3 class="font-black uppercase tracking-widest text-sm">Nuevo Asesor APE</h3>
                <button onclick="closeModal()" class="text-white/50 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form id="formAsesor" class="p-8 space-y-4">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Tipo Doc</label>
                        <select name="pers_tipodoc" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-[#10069f] outline-none">
                            <option value="CC">CC</option>
                            <option value="CE">CE</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Documento</label>
                        <input type="text" name="pers_doc" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-[#10069f] outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Mesa</label>
                        <input type="number" name="ase_mesa" required min="1" max="20" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-[#10069f] outline-none" placeholder="1-20">
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Nombres</label>
                    <input type="text" name="pers_nombres" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-bold focus:border-[#10069f] outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Apellidos</label>
                    <input type="text" name="pers_apellidos" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-bold focus:border-[#10069f] outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Correo Institucional</label>
                    <input type="email" name="ase_correo" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-xs font-bold focus:border-[#10069f] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Contraseña</label>
                        <input type="password" name="ase_password" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-[#10069f] outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Fila</label>
                        <select name="ase_tipo_asesor" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:border-[#10069f] outline-none">
                            <option value="G">General</option>
                            <option value="V">Víctimas</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="w-full bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-4 rounded-2xl shadow-xl transition-all uppercase text-[10px] tracking-widest mt-4">Guardar Asesor</button>
            </form>
        </div>
    </div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        /**
         * Helper para validar la respuesta de fetch.
         * Si detecta redirección o error 419 (CSRF), recarga la página.
         */
        async function handleFetchResponse(res) {
            if (res.redirected || res.status === 419) {
                console.warn('Sesión expirada o error 419. Recargando...');
                location.reload();
                return null;
            }
            return res;
        }

        function openModal() {
            document.getElementById('modalAsesor').classList.remove('hidden');
            document.getElementById('modalAsesor').classList.add('flex');
        }
        function closeModal() {
            document.getElementById('modalAsesor').classList.add('hidden');
            document.getElementById('modalAsesor').classList.remove('flex');
        }

        document.getElementById('formAsesor').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            try {
                let res = await fetch('{{ route('coordinador.asesor.store') }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();
                if (data.success) {
                    alert('Asesor registrado correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + JSON.stringify(data.errors || 'Verifique los datos'));
                }
            } catch (e) { alert('Error de conexión'); }
        });

        async function aceptarTurno() {
            try {
                let res = await fetch('{{ route('coordinador.aceptar') }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                res = await handleFetchResponse(res);
                if (!res) return;

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
                let res = await fetch('{{ route('coordinador.reasignar') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ase_id, nuevo_tipo })
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();
                if (data.success) {
                    // Feedback visual suave
                    select.classList.add('bg-green-50', 'border-green-500');
                    setTimeout(() => select.classList.remove('bg-green-50', 'border-green-500'), 2000);
                }
            } catch (e) { console.error(e); }
        }

        // ── Identificador de Pestaña (Aislamiento) ────────────────
        if (!sessionStorage.getItem('coor_tab_id')) {
            sessionStorage.setItem('coor_tab_id', 'ctab_' + Math.random().toString(36).substr(2, 9));
        }
        const TAB_ID = sessionStorage.getItem('coor_tab_id');

        // Polling para actualizar estados
        setInterval(async () => {
            try {
                let res = await fetch('{{ route('coordinador.api.estado') }}?window_id=' + TAB_ID, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();
                document.getElementById('empresario-count').innerText = data.colaEmpresario.length;
            } catch (e) { console.error(e); }
        }, 5000);

        // ── Inactividad (15 min) ───────────────────────────────────
        let lastActivityTimestamp = Date.now(); // Usar timestamp real para evitar throttling de pestañas
        let countdownTime = 60;
        let countdownInterval = null;
        let heartbeatInterval = null;
        const IDLE_LIMIT = 14 * 60 * 1000; // 14 minutos en milisegundos

        function resetIdleTimer() {
            if (document.getElementById('modal-timeout').classList.contains('hidden')) {
                const now = Date.now();
                // Si ha pasado más de 30 segundos desde el último latido, avisar al servidor
                if (now - lastActivityTimestamp > 30000) {
                    enviarHeartbeat();
                }
                lastActivityTimestamp = now;
            }
        }

        async function enviarHeartbeat() {
            try {
                // heartbeat=1 hace que el middleware actualice la actividad.
                // Enviamos el TAB_ID para que solo esta pestaña sea la "dueña" de la sesión.
                let res = await fetch('{{ route("coordinador.api.estado") }}?heartbeat=1&window_id=' + TAB_ID, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                await handleFetchResponse(res);
            } catch (e) { console.error("Error heartbeat", e); }
        }

        // Detectar actividad del usuario
        ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, resetIdleTimer, true);
        });

        // Revisar inactividad cada 2 segundos (más eficiente)
        setInterval(() => {
            const now = Date.now();
            const inactiveTime = now - lastActivityTimestamp;

            if (inactiveTime >= IDLE_LIMIT && document.getElementById('modal-timeout').classList.contains('hidden')) {
                mostrarAvisoTimeout();
            }
        }, 2000);

        function mostrarAvisoTimeout() {
            const modal = document.getElementById('modal-timeout');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            countdownTime = 60;
            document.getElementById('timeout-countdown').innerText = countdownTime;
            
            clearInterval(countdownInterval);
            countdownInterval = setInterval(() => {
                countdownTime--;
                document.getElementById('timeout-countdown').innerText = countdownTime;
                if (countdownTime <= 0) {
                    logoutAhora();
                }
            }, 1000);
        }

        async function continuarSesion() {
            try {
                // Forzar actualización de sesión en el servidor
                await enviarHeartbeat();
                clearInterval(countdownInterval);
                document.getElementById('modal-timeout').classList.add('hidden');
                document.getElementById('modal-timeout').classList.remove('flex');
                lastActivityTimestamp = Date.now();
            } catch (e) {
                console.error("Error al refrescar sesión", e);
            }
        }

        function logoutAhora() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("coordinador.logout") }}';
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = CSRF_TOKEN;
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Cerrar sesión al cerrar la pestaña/navegador
        window.addEventListener('unload', function() {
            const isRefresh = window.performance && window.performance.navigation.type === 1;
            if (!isRefresh) {
                const blob = new Blob([JSON.stringify({ _token: CSRF_TOKEN })], { type: 'application/json' });
                navigator.sendBeacon('{{ route("coordinador.logout") }}', blob);
            }
        });
    </script>
</body>
</html>
