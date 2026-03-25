<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno — Panel Asesor</title>
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
    <style>
        body { background-color: #0d0d0d; font-family: 'Montserrat', sans-serif; }
        .glass { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(10px); }
        .status-badge { transition: all 0.3s ease; }
        .pulse-blue { animation: pulseb 2s infinite; }
        @keyframes pulseb {
            0%   { box-shadow: 0 0 0 0 rgba(16,6,159,0.5); }
            70%  { box-shadow: 0 0 0 14px rgba(16,6,159,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,6,159,0); }
        }
        .queue-item { transition: all 0.3s ease; }
        .queue-item:hover { border-color: #10069f; transform: translateX(4px); }
        .btn-primary   { background: #10069f; transition: all 0.2s; border-bottom: 4px solid #0a0455; }
        .btn-primary:hover   { background: #0a0455; transform: translateY(-1px); }
        .btn-primary:active  { transform: scale(0.97); }
        .btn-secondary { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); transition: all 0.2s; }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); }
        .btn-warning   { background: #ffb500; color: #000; transition: all 0.2s; }
        .btn-warning:hover   { background: #e6a400; }
        .btn-danger    { background: #991b1b; transition: all 0.2s; }
        .btn-danger:hover    { background: #7f1d1d; }
        .btn-info      { background: #10069f; transition: all 0.2s; }
        .btn-info:hover      { background: #1a1a1a; }
    </style>
</head>
<body class="min-h-screen text-white">

    {{-- ─── HEADER ──────────────────────────────────────────────── --}}
    <header class="border-b border-white/10 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="px-4 py-1.5 bg-white rounded-xl flex items-center justify-center">
                <div class="text-xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[5px] uppercase tracking-[0.2em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                 </div>
            </div>
            <div>
                <span class="text-white font-black text-lg uppercase tracking-tight">APE <span class="text-[#10069f]">Digiturno</span></span>
                <p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Panel del Asesor</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Estado actual --}}
            <div id="estado-badge" class="flex items-center gap-2 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider
                {{ $asesor->ase_estado === 'disponible' ? 'bg-[#10069f]/20 text-white border border-[#10069f]/30' :
                   ($asesor->ase_estado === 'en_espera'  ? 'bg-[#ffb500]/20 text-[#ffb500] border border-[#ffb500]/30' :
                                                           'bg-blue-600/20 text-blue-400 border border-blue-600/30') }}">
                <span class="w-2 h-2 rounded-full inline-block
                    {{ $asesor->ase_estado === 'disponible' ? 'bg-[#10069f] pulse-blue' :
                       ($asesor->ase_estado === 'en_espera'  ? 'bg-[#ffb500]' : 'bg-blue-400') }}"></span>
                <span id="estado-texto">
                    {{ $asesor->ase_estado === 'disponible' ? 'Disponible' : ($asesor->ase_estado === 'en_espera' ? 'En Espera' : 'Ocupado') }}
                </span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('asesor.logout') }}">
                @csrf
                <button type="submit" class="btn-secondary text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-xl">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    {{-- ─── CONTENIDO PRINCIPAL ────────────────────────────────── --}}
    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Columna Izquierda: Info + Acciones ────────────────── --}}
        <div class="lg:col-span-1 flex flex-col gap-5">

            {{-- Tarjeta: Info del Asesor --}}
            <div class="glass rounded-2xl p-6">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-4">Información del Asesor</p>
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 bg-[#10069f]/10 border border-[#10069f]/30 rounded-2xl flex items-center justify-center text-2xl font-black text-[#10069f]">
                        {{ strtoupper(substr($asesor->persona->pers_nombres ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-extrabold text-white text-lg leading-tight">
                            {{ $asesor->persona->pers_nombres ?? '—' }} {{ $asesor->persona->pers_apellidos ?? '' }}
                        </h2>
                        <p class="text-gray-400 text-xs font-semibold">Contrato #{{ $asesor->ase_nrocontrato ?? '—' }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Tipo de Cola</span>
                        <span class="font-black text-[#10069f]">
                            @switch($asesor->ase_tipo_asesor)
                                @case('V') Víctimas @break
                                @case('G') General @break
                                @case('P') Prioritario @break
                                @default {{ $asesor->ase_tipo_asesor }}
                            @endswitch
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Correo</span>
                        <span class="font-semibold text-white text-xs">{{ $asesor->ase_correo }}</span>
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Turno Actual (atención activa) --}}
            <div id="card-turno-actual" class="{{ $asesor->ase_estado === 'ocupado' ? '' : 'hidden' }} glass rounded-2xl p-6 border border-blue-500/30">
                <p class="text-blue-400 text-[10px] font-black uppercase tracking-widest mb-3">Atención Activa</p>
                <div class="text-center py-4">
                    <div id="turno-codigo-actual" class="text-6xl font-black text-white tracking-tighter mb-2">
                        —
                    </div>
                    <div id="info-persona" class="text-gray-400 text-xs font-semibold"></div>
                </div>
                <div class="flex gap-2 mt-2">
                    <button
                        onclick="abrirModal()"
                        id="btn-ver-detalles"
                        class="btn-info flex-1 text-white font-bold py-2.5 rounded-xl uppercase tracking-widest text-xs flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ver Detalles
                    </button>
                    <button
                        onclick="finalizarAtencion()"
                        class="btn-danger flex-1 text-white font-extrabold py-2.5 rounded-xl uppercase tracking-widest text-xs border-b-4 border-red-900"
                    >
                        ✓ Finalizar
                    </button>
                </div>
            </div>

            {{-- Tarjeta: Acciones Principales --}}
            <div class="glass rounded-2xl p-6 space-y-3">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-4">Acciones</p>

                {{-- Botón Aceptar Turno --}}
                <button
                    id="btn-aceptar"
                    onclick="aceptarTurno()"
                    class="btn-primary w-full text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 {{ $asesor->ase_estado !== 'disponible' ? 'opacity-40 cursor-not-allowed' : 'pulse-blue' }}"
                    {{ $asesor->ase_estado !== 'disponible' ? 'disabled' : '' }}
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Aceptar Turno
                </button>

                {{-- Botón Espera / Reanudar --}}
                <button
                    id="btn-espera"
                    onclick="toggleEspera()"
                    class="{{ $asesor->ase_estado === 'en_espera' ? 'btn-primary' : 'btn-warning' }} w-full text-white font-extrabold py-3.5 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 {{ $asesor->ase_estado === 'ocupado' ? 'opacity-40 cursor-not-allowed' : '' }}"
                    {{ $asesor->ase_estado === 'ocupado' ? 'disabled' : '' }}
                >
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($asesor->ase_estado === 'en_espera')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    <span id="btn-espera-texto">
                        {{ $asesor->ase_estado === 'en_espera' ? 'Reanudar Actividad' : 'Poner en Espera' }}
                    </span>
                </button>
            </div>

            {{-- Contador - Atendidos hoy --}}
            <div class="glass rounded-2xl p-5 flex items-center justify-between overflow-hidden">
                <div class="relative z-10">
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">Atendidos Hoy</p>
                    <p id="contador-atendidos" class="text-4xl font-black text-[#ffb500] mt-1">{{ count($historial) }}</p>
                </div>
                <div class="w-14 h-14 bg-[#10069f]/10 border border-[#10069f]/30 rounded-2xl flex items-center justify-center relative z-10">
                    <svg class="w-7 h-7 text-[#10069f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
        </div>

        {{-- ── Columna Derecha: Cola + Historial ──────────────────── --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Cola de Turnos Pendientes --}}
            <div class="glass rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Cola de Espera</p>
                        <h3 class="text-white font-extrabold text-lg">Turnos Pendientes</h3>
                    </div>
                    <div class="flex items-center gap-2 text-wrap">
                        <div class="w-2 h-2 bg-[#ffb500] rounded-full animate-pulse"></div>
                        <span class="text-[#ffb500] text-[10px] font-black uppercase tracking-wider">En vivo</span>
                        <span class="glass text-white text-[10px] font-black px-3 py-1 rounded-full ml-1">
                            <span id="cola-count">{{ count($cola) }}</span> en cola
                        </span>
                    </div>
                </div>

                {{-- Lista de turnos en cola --}}
                <div id="lista-cola" class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($cola as $turno)
                        <div class="queue-item glass rounded-xl px-5 py-4 border border-white/5 flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Turno en espera</p>
                                <p class="text-white font-black text-2xl">{{ $turno['codigo'] }}</p>
                            </div>
                            <p class="text-gray-500 text-xs font-semibold">
                                {{ \Carbon\Carbon::parse($turno['hora'])->format('H:i') }}
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-gray-600 mb-3">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">Sin turnos en cola</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Historial del día --}}
            <div class="glass rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Historial</p>
                        <h3 class="text-white font-extrabold text-lg">Atenciones de Hoy</h3>
                    </div>
                </div>

                <div id="lista-historial" class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    @forelse($historial as $item)
                        <div class="flex items-center justify-between glass rounded-xl px-5 py-3 border border-white/5">
                            <div class="flex items-center gap-3">
                                <span class="text-[#ffb500] font-black text-lg">{{ $item['codigo'] }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-gray-400 text-xs font-semibold">
                                    {{ \Carbon\Carbon::parse($item['hora_inicio'])->format('H:i') }}
                                    @if($item['hora_fin'])
                                        → {{ \Carbon\Carbon::parse($item['hora_fin'])->format('H:i') }}
                                    @else
                                        <span class="text-blue-400">(activo)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-600 font-semibold uppercase tracking-wider text-sm">Sin atenciones registradas hoy</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- ─── MODAL DATOS PERSONA ────────────────────────────── -->
    <div id="modal-persona" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.8);backdrop-filter:blur(6px);">
        <div class="glass rounded-3xl p-8 w-full max-w-md border border-white/10 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Datos del Solicitante</p>
                    <h3 class="text-white font-extrabold text-xl mt-0.5">Información Personal</h3>
                </div>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="modal-loading" class="text-center py-8 text-gray-400 font-semibold">Cargando...</div>

            <form id="forma-persona" class="space-y-4 hidden" onsubmit="guardarPersona(event)">
                <input type="hidden" id="f-pers-doc" name="pers_doc">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Número de Documento</label>
                        <input id="f-pers-doc-vis" type="text" disabled
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-gray-400 font-semibold text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tipo Doc.</label>
                        <select name="pers_tipodoc" id="f-tipodoc" required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                            <option value="CC">Cédula CC</option>
                            <option value="TI">Tarjeta TI</option>
                            <option value="CE">Céd. Extranjera</option>
                            <option value="PEP">PEP</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                        <input id="f-fecha-nac" name="pers_fecha_nac" type="date"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-3 text-white font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nombres</label>
                        <input id="f-nombres" name="pers_nombres" type="text" required placeholder="Nombres completos"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Apellidos</label>
                        <input id="f-apellidos" name="pers_apellidos" type="text" required placeholder="Apellidos completos"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                        <input id="f-telefono" name="pers_telefono" type="tel" placeholder="Ej: 310 000 0000"
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>
                </div>

                <div id="modal-error" class="hidden bg-red-900/30 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 text-sm font-semibold"></div>
                <div id="modal-success" class="hidden bg-green-900/30 border border-green-500/30 text-[#ffb500] rounded-xl px-4 py-3 text-sm font-semibold">✓ Datos guardados correctamente</div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModal()" class="btn-secondary flex-1 text-white font-bold py-3 rounded-xl uppercase tracking-widest text-xs">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary flex-1 text-white font-extrabold py-3 rounded-xl uppercase tracking-widest text-xs border-b-4 border-[#0a0455]">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─── TOAST NOTIFICACIÓN ──────────────────────────────────── --}}
    <div id="toast" class="fixed bottom-6 right-6 max-w-sm z-50 hidden">
        <div id="toast-inner" class="rounded-2xl px-5 py-4 shadow-2xl font-bold text-sm text-white">
            <p id="toast-msg"></p>
        </div>
    </div>

    {{-- ─── SCRIPTS ─────────────────────────────────────────────── --}}
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentUsuarioId = null; // almacena el usuario_id del turno activo

        // ── Toast ──────────────────────────────────────────────────
        function showToast(msg, tipo = 'success') {
            const toast = document.getElementById('toast');
            const inner = document.getElementById('toast-inner');
            const txtEl = document.getElementById('toast-msg');

            txtEl.innerText = msg;
            inner.className = `rounded-2xl px-5 py-4 shadow-2xl font-bold text-sm text-white ${
                tipo === 'success' ? 'bg-[#10069f]' :
                tipo === 'error'   ? 'bg-red-700' :
                tipo === 'warn'    ? 'bg-[#ffb500]' : 'bg-[#10069f]'
            }`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        // ── Actualizar UI de estado ────────────────────────────────
        function actualizarEstadoUI(estado) {
            const badge     = document.getElementById('estado-badge');
            const textoEl   = document.getElementById('estado-texto');
            const btnAceptar = document.getElementById('btn-aceptar');
            const btnEspera  = document.getElementById('btn-espera');
            const btnEsperaTexto = document.getElementById('btn-espera-texto');

            const labels = { disponible: 'Disponible', en_espera: 'En Espera', ocupado: 'Ocupado' };
            textoEl.innerText = labels[estado] || estado;

            badge.className = `flex items-center gap-2 px-4 py-2 rounded-full text-xs font-black uppercase tracking-wider ` + (
                estado === 'disponible' ? 'bg-[#10069f]/20 text-white border border-[#10069f]/30' :
                estado === 'en_espera'  ? 'bg-[#ffb500]/20 text-[#ffb500] border border-[#ffb500]/30' :
                                          'bg-blue-600/20 text-blue-400 border border-blue-600/30'
            );

            // Botón Aceptar
            if (estado === 'disponible') {
                btnAceptar.disabled = false;
                btnAceptar.classList.remove('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.add('pulse-blue');
            } else {
                btnAceptar.disabled = true;
                btnAceptar.classList.add('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.remove('pulse-blue');
            }

            // Botón Espera
            if (estado === 'ocupado') {
                btnEspera.disabled = true;
                btnEspera.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                btnEspera.disabled = false;
                btnEspera.classList.remove('opacity-40', 'cursor-not-allowed');
                if (estado === 'en_espera') {
                    btnEspera.className = btnEspera.className.replace('btn-warning', 'btn-primary');
                    btnEsperaTexto.innerText = 'Reanudar Actividad';
                } else {
                    btnEspera.className = btnEspera.className.replace('btn-primary', 'btn-warning');
                    btnEsperaTexto.innerText = 'Poner en Espera';
                }
            }
        }

        // ── Aceptar Turno ──────────────────────────────────────────
        async function aceptarTurno() {
            try {
                const res  = await fetch('{{ route("asesor.aceptar") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'No hay turnos disponibles.', 'warn');
                    return;
                }

                // Almacenar el usuario_id para el modal
                currentUsuarioId = data.usuario_id || null;

                // Mostrar card de turno activo
                document.getElementById('turno-codigo-actual').innerText = data.codigo_turno;
                if (data.persona) {
                    document.getElementById('info-persona').innerText =
                        data.persona.nombres + ' · Doc: ' + data.persona.documento;
                }
                document.getElementById('card-turno-actual').classList.remove('hidden');

                actualizarEstadoUI('ocupado');
                showToast('Turno ' + data.codigo_turno + ' aceptado.', 'success');
                await refreshPollData();

            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Finalizar Atención ─────────────────────────────────────
        async function finalizarAtencion() {
            try {
                const res  = await fetch('{{ route("asesor.finalizar") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'Error al finalizar.', 'error');
                    return;
                }

                document.getElementById('card-turno-actual').classList.add('hidden');
                actualizarEstadoUI('disponible');
                showToast('Atención finalizada correctamente.', 'success');
                await refreshPollData();

            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Toggle Espera ──────────────────────────────────────────
        async function toggleEspera() {
            try {
                const res  = await fetch('{{ route("asesor.espera") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'Error al cambiar estado.', 'warn');
                    return;
                }

                actualizarEstadoUI(data.ase_estado);
                const msg = data.ase_estado === 'en_espera' ? 'Ahora estás en espera.' : 'Actividad reanudada.';
                showToast(msg, data.ase_estado === 'en_espera' ? 'warn' : 'success');

            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Polling de datos ────────────────────────────────────────
        async function refreshPollData() {
            try {
                const res  = await fetch('{{ route("asesor.api.estado") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.status === 401) { location.href = '{{ route("asesor.login") }}'; return; }
                const data = await res.json();

                // Actualizar cola
                const listaCola = document.getElementById('lista-cola');
                document.getElementById('cola-count').innerText = data.cola_count;

                if (data.cola.length === 0) {
                    listaCola.innerHTML = `
                        <div class="text-center py-12">
                            <p class="text-gray-500 font-bold uppercase tracking-widest text-sm">Sin turnos en cola</p>
                        </div>`;
                } else {
                    listaCola.innerHTML = data.cola.map(t => `
                        <div class="queue-item glass rounded-xl px-5 py-4 border border-white/5 flex items-center justify-between">
                            <div>
                                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-wider">Turno en espera</p>
                                <p class="text-white font-black text-2xl">${t.codigo}</p>
                            </div>
                            <p class="text-gray-500 text-xs font-semibold">${t.hora ? t.hora.substring(11,16) : ''}</p>
                        </div>
                    `).join('');
                }

                // Actualizar historial
                const listaHist = document.getElementById('lista-historial');
                document.getElementById('contador-atendidos').innerText = data.historial.length;
                if (data.historial.length === 0) {
                    listaHist.innerHTML = `<div class="text-center py-8"><p class="text-gray-600 font-semibold uppercase tracking-wider text-sm">Sin atenciones registradas hoy</p></div>`;
                } else {
                    listaHist.innerHTML = data.historial.map(h => `
                        <div class="flex items-center justify-between glass rounded-xl px-5 py-3 border border-white/5">
                            <span class="text-[#ffb500] font-black text-lg">${h.codigo}</span>
                            <p class="text-gray-400 text-xs font-semibold">
                                ${h.hora_inicio ? h.hora_inicio.substring(11,16) : ''}
                                ${h.hora_fin ? '→ ' + h.hora_fin.substring(11,16) : '<span class="text-blue-400">(activo)</span>'}
                            </p>
                        </div>
                    `).join('');
                }

            } catch (e) {
                console.warn('Poll error:', e);
            }
        }

        // ── Modal Persona ─────────────────────────────────────────────────
        async function abrirModal() {
            if (!currentUsuarioId) {
                showToast('No hay usuario asociado al turno.', 'warn');
                return;
            }

            const modal = document.getElementById('modal-persona');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Resetear estado del form
            document.getElementById('modal-loading').classList.remove('hidden');
            document.getElementById('forma-persona').classList.add('hidden');
            document.getElementById('modal-error').classList.add('hidden');
            document.getElementById('modal-success').classList.add('hidden');

            try {
                const res = await fetch(`{{ route('asesor.persona.get') }}?usuario_id=${currentUsuarioId}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const p = await res.json();

                if (!res.ok) {
                    document.getElementById('modal-loading').innerText = p.error || 'Error al cargar datos.';
                    return;
                }

                // Poblar form
                document.getElementById('f-pers-doc').value      = p.pers_doc;
                document.getElementById('f-pers-doc-vis').value  = p.pers_doc;
                document.getElementById('f-tipodoc').value       = p.pers_tipodoc || 'CC';
                document.getElementById('f-nombres').value       = p.pers_nombres || '';
                document.getElementById('f-apellidos').value     = p.pers_apellidos || '';
                document.getElementById('f-telefono').value      = p.pers_telefono || '';
                document.getElementById('f-fecha-nac').value     = p.pers_fecha_nac ? p.pers_fecha_nac.substring(0,10) : '';

                document.getElementById('modal-loading').classList.add('hidden');
                document.getElementById('forma-persona').classList.remove('hidden');

            } catch (e) {
                document.getElementById('modal-loading').innerText = 'Error de conexión.';
            }
        }

        function cerrarModal() {
            const modal = document.getElementById('modal-persona');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function guardarPersona(e) {
            e.preventDefault();
            document.getElementById('modal-error').classList.add('hidden');
            document.getElementById('modal-success').classList.add('hidden');

            const form = document.getElementById('forma-persona');
            const data = new FormData(form);
            const body = {};
            data.forEach((v, k) => body[k] = v);
            // Agregar método PUT ya que fetch usa JSON
            body['_method'] = 'PUT';

            try {
                const res = await fetch('{{ route("asesor.persona.update") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const result = await res.json();

                if (!res.ok) {
                    const errEl = document.getElementById('modal-error');
                    errEl.innerText = result.error || Object.values(result.errors || {}).flat().join(', ');
                    errEl.classList.remove('hidden');
                    return;
                }

                document.getElementById('modal-success').classList.remove('hidden');

                // Actualizar info mostrada en card
                const nom = (body.pers_nombres || '') + ' ' + (body.pers_apellidos || '');
                document.getElementById('info-persona').innerText = nom.trim() + ' · Doc: ' + body.pers_doc;

                setTimeout(() => {
                    document.getElementById('modal-success').classList.add('hidden');
                    cerrarModal();
                }, 1500);

            } catch (e) {
                document.getElementById('modal-error').innerText = 'Error de conexión.';
                document.getElementById('modal-error').classList.remove('hidden');
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-persona').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });

        // Iniciar polling cada 5 segundos
        setInterval(refreshPollData, 5000);

        // Cerrar sesión al cerrar la pestaña/navegador
        window.addEventListener('unload', function() {
            const isRefresh = window.performance && window.performance.navigation.type === 1;
            if (!isRefresh) {
                const blob = new Blob([JSON.stringify({ _token: CSRF })], { type: 'application/json' });
                navigator.sendBeacon('{{ route("asesor.logout") }}', blob);
            }
        });
    </script>
</body>
</html>
