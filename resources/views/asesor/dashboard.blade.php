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
                            orange: '#ff6b00',
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
        body { 
            background-color: #f8fafc; 
            font-family: 'Montserrat', sans-serif; 
            background-image: radial-gradient(#10069f 0.5px, transparent 0.5px);
            background-size: 32px 32px;
        }
        .glass { background: #ffffff; border: 1px solid #eef2f7; box-shadow: 0 10px 25px -5px rgba(16, 6, 159, 0.1); }
        .status-badge { transition: all 0.3s ease; }
        .pulse-blue { animation: pulseb 2s infinite; }
        @keyframes pulseb {
            0%   { box-shadow: 0 0 0 0 rgba(16,6,159,0.4); }
            70%  { box-shadow: 0 0 0 14px rgba(16,6,159,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,6,159,0); }
        }
        .queue-item { transition: all 0.3s ease; background: #fff; border: 1px solid #f1f5f9; }
        .queue-item:hover { border-color: #10069f; transform: translateX(4px); box-shadow: 0 4px 12px rgba(16,6,159,0.08); }
        .btn-primary   { background: #10069f; transition: all 0.2s; border-bottom: 4px solid #0a0455; color: #fff; }
        .btn-primary:hover   { background: #0a0455; transform: translateY(-1px); }
        .btn-primary:active  { transform: scale(0.97); }
        .btn-secondary { background: #ffffff; border: 1px solid #e2e8f0; transition: all 0.2s; color: #64748b; }
        .btn-secondary:hover { background: #f1f5f9; color: #10069f; }
        .btn-logout { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; transition: all 0.2s; }
        .btn-logout:hover { background: #ef4444; color: #fff; border-color: #dc2626; }
        .btn-warning   { background: #ffb500; color: #000; transition: all 0.2s; border-bottom: 4px solid #cc9100; }
        .btn-warning:hover   { background: #e6a400; }
        .btn-danger    { background: #991b1b; transition: all 0.2s; color: #fff; }
        .btn-danger:hover    { background: #7f1d1d; }
        .btn-info      { background: #f1f5f9; transition: all 0.2s; color: #10069f; border: 1px solid #e2e8f0; }
        .btn-info:hover      { background: #e2e8f0; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <div class="px-4 py-1.5 bg-[#f8fafc] rounded-xl border border-gray-100 flex items-center justify-center">
                <div class="text-xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[5px] uppercase tracking-[0.2em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                 </div>
            </div>
            <div>
                <span class="text-slate-900 font-black text-lg uppercase tracking-tight">APE <span class="text-[#10069f]">Digiturno</span></span>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Panel del Asesor</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            {{-- Estado actual --}}
            <div id="estado-badge" class="flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border-2
                {{ $asesor->ase_estado === 'disponible' ? 'bg-green-600 text-white border-green-600' :
                   ($asesor->ase_estado === 'en_espera'  ? 'bg-gray-400 text-white border-gray-400' :
                                                            'bg-blue-600 text-white border-blue-600') }}">
                <span class="w-1.5 h-1.5 rounded-full inline-block
                    {{ $asesor->ase_estado === 'disponible' ? 'bg-white animate-pulse' :
                       ($asesor->ase_estado === 'en_espera'  ? 'bg-white' :
                       ($asesor->ase_estado === 'inactivo'   ? 'bg-gray-400' : 'bg-white')) }}"></span>
                <span id="estado-texto">
                    {{ $asesor->ase_estado === 'disponible' ? 'Disponible' : ($asesor->ase_estado === 'en_espera' ? 'En Espera' : ($asesor->ase_estado === 'inactivo' ? 'Inactivo' : 'Ocupado')) }}
                </span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('asesor.logout') }}" onsubmit="confirmarLogoutManual(event)">
                @csrf
                <button type="submit" class="btn-logout text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl shadow-sm">
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
            <div class="glass rounded-[32px] p-6 shadow-sm">
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-4">Información del Asesor</p>
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-14 h-14 bg-[#10069f]/5 border border-[#10069f]/10 rounded-2xl flex items-center justify-center text-2xl font-black text-[#10069f]">
                        {{ strtoupper(substr($asesor->persona->pers_nombres ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-lg leading-tight">
                            {{ $asesor->persona->pers_nombres ?? '—' }} {{ $asesor->persona->pers_apellidos ?? '' }}
                        </h2>
                        <p class="text-slate-400 text-xs font-semibold uppercase tracking-wide">Mesa #{{ $asesor->ase_mesa ?? '—' }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Tipo de Cola</span>
                        <span id="tipo-cola-texto" class="font-black text-[#10069f]">
                            @switch($asesor->ase_tipo_asesor)
                                @case('V') Víctimas @break
                                @case('G') General @break
                                @case('P') Prioritario @break
                                @case('E') Empresario @break
                                @default {{ $asesor->ase_tipo_asesor }}
                            @endswitch
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-semibold">Correo</span>
                        <span class="font-semibold text-slate-600 text-xs">{{ $asesor->ase_correo }}</span>
                    </div>
                </div>
            </div>

            {{-- Tarjeta: Turno Actual (atención activa) --}}
            <div id="card-turno-actual" class="{{ $asesor->ase_estado === 'ocupado' ? '' : 'hidden' }} glass rounded-[32px] p-6 border-2 border-blue-500/30">
                <p class="text-blue-600 text-[10px] font-black uppercase tracking-widest mb-3">Atención Activa</p>
                <div class="text-center py-4">
                    <div id="turno-codigo-actual" class="text-6xl font-black text-slate-900 tracking-tighter mb-2">
                        —
                    </div>
                    <div id="info-persona" class="text-slate-500 text-xs font-semibold italic"></div>
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
                        onclick="finalizarAtencion('atendido')"
                        class="bg-green-600 hover:bg-green-700 flex-1 text-white font-extrabold py-2.5 rounded-xl uppercase tracking-widest text-xs border-b-4 border-green-900 transition-colors"
                    >
                        ✓ Finalizar
                    </button>
                </div>
                <button
                    onclick="finalizarAtencion('ausente')"
                    class="w-full mt-2 bg-transparent border border-gray-600 border-dashed text-gray-500 font-bold py-2 rounded-xl uppercase tracking-widest text-[10px] hover:border-red-500 hover:text-red-400 transition-colors"
                >
                    ✕ El usuario no se presentó
                </button>
            </div>

            {{-- Tarjeta: Acciones Principales --}}
            <div class="glass rounded-[32px] p-6 space-y-3">
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-4">Acciones del Sistema</p>

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
                    class="{{ $asesor->ase_estado === 'en_espera' ? 'btn-primary' : 'btn-warning' }} w-full font-extrabold py-3.5 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 mt-3 {{ in_array($asesor->ase_estado, ['ocupado','inactivo']) ? 'opacity-40 cursor-not-allowed' : '' }}"
                    {{ in_array($asesor->ase_estado, ['ocupado','inactivo']) ? 'disabled' : '' }}
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

                {{-- Separador --}}
                <div class="border-t border-slate-100 my-1 pt-1"></div>

                {{-- Botón Iniciar / Finalizar Turno --}}
                @if($asesor->ase_estado === 'inactivo')
                <button
                    id="btn-turno"
                    onclick="iniciarTurno()"
                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-emerald-700 pulse-blue"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M12 8v4l3 3"/></svg>
                    ▶ Iniciar Turno
                </button>
                @else
                <button
                    id="btn-turno"
                    onclick="confirmarFinalizarTurno()"
                    class="w-full bg-red-800 hover:bg-red-900 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-red-950"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 10h6v4H9z"/></svg>
                    ■ Finalizar Turno
                </button>
                @endif

                {{-- Cronómetro de turno activo --}}
                <div id="turno-timer" class="{{ $asesor->ase_estado !== 'inactivo' ? '' : 'hidden' }} text-center">
                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Turno iniciado hace</p>
                    <p id="turno-timer-texto" class="text-slate-700 font-black text-sm">—</p>
                </div>

            </div>

            {{-- Aviso cuando asesor está inactivo --}}
            <div id="card-inactivo" class="{{ $asesor->ase_estado === 'inactivo' ? '' : 'hidden' }} glass rounded-[32px] p-5 border-2 border-dashed border-gray-300 text-center">
                <div class="text-3xl mb-2">⏸</div>
                <p class="text-slate-700 font-black text-sm uppercase tracking-tight">Turno no iniciado</p>
                <p class="text-slate-400 text-xs font-medium mt-1">Presiona <strong>Iniciar Turno</strong> para comenzar a recibir turnos</p>
            </div>

        </div>

        {{-- ── Columna Derecha: Colas de Turnos ────────────────────── --}}
        <div class="lg:col-span-2 flex flex-col gap-5">

            {{-- Sección: Turnos PRIORITARIOS --}}
            <div id="card-cola-prioritaria" class="glass rounded-[32px] p-6 border-b-4 border-[#ffb500]">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-[#ffb500] text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-[#ffb500] rounded-full animate-pulse"></span>
                            Atención Especial / Prioritarios
                        </p>
                        <h3 class="text-slate-900 font-extrabold text-lg">Turnos en Espera</h3>
                    </div>
                </div>
                <div id="lista-cola-prioritaria" class="grid grid-cols-1 md:grid-cols-2 gap-3 min-h-[100px]">
                    {{-- Se puebla por JS --}}
                </div>
            </div>

            {{-- Sección: Turnos VÍCTIMAS --}}
            @if($asesor->ase_tipo_asesor === 'V')
            <div class="glass rounded-[32px] p-6 border-b-4 border-red-600">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-red-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full animate-pulse"></span>
                            Población Víctima
                        </p>
                        <h3 class="text-slate-900 font-extrabold text-lg">Turnos Víctimas</h3>
                    </div>
                    <span class="bg-red-50 text-red-600 text-[10px] font-black px-3 py-1 rounded-full border border-red-100">
                        <span id="cola-victimas-count">0</span> pendientes
                    </span>
                </div>
                <div id="lista-cola-victimas" class="grid grid-cols-1 md:grid-cols-2 gap-3 min-h-[60px]">
                    {{-- Se puebla por JS --}}
                </div>
            </div>
            @endif

            {{-- Sección: Turnos GENERALES --}}
            <div class="glass rounded-[32px] p-6 flex-1 border-b-4 border-slate-300">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Cola de Espera</p>
                        <h3 class="text-slate-900 font-extrabold text-lg">Turnos Generales</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-slate-100 text-slate-600 text-[10px] font-black px-3 py-1 rounded-full border border-slate-200">
                            <span id="cola-count">0</span> pendientes
                        </span>
                    </div>
                </div>

                <div id="lista-cola" class="space-y-3 max-h-[500px] overflow-y-auto pr-1 min-h-[100px]">
                    {{-- Se puebla por JS --}}
                </div>
            </div>

            {{-- Sección: Turnos EMPRESARIO --}}
            <div class="glass rounded-[32px] p-6 border-b-4 border-blue-500">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-blue-600 text-[10px] font-black uppercase tracking-widest">Cola Empresarial</p>
                        <h3 class="text-slate-900 font-extrabold text-lg">Turnos Empresario</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-black px-3 py-1 rounded-full border border-blue-100">
                            <span id="cola-empresario-count">0</span> pendientes
                        </span>
                    </div>
                </div>
                <div id="lista-cola-empresario" class="space-y-3 max-h-[300px] overflow-y-auto pr-1 min-h-[60px]">
                    {{-- Se puebla por JS --}}
                </div>
            </div>
        </div>
        </div>
    </main>

    <!-- ─── MODAL RECORDATORIO TURNO ESPECIAL ────────────────────── -->
    <div id="modal-prioridad" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/40 backdrop-blur-md px-4">
        <div class="bg-white border-2 border-[#ffb500] w-full max-w-md rounded-[32px] p-8 shadow-2xl animate-in zoom-in duration-300">
            <div class="flex justify-center mb-6 text-[#ffb500]">
                <div class="bg-[#ffb500]/10 p-5 rounded-full ring-8 ring-[#ffb500]/5">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 text-center mb-2 uppercase tracking-tighter">¡Turno Especial Pendiente!</h3>
            <p class="text-slate-500 text-center mb-8 px-4 text-sm font-medium leading-relaxed">Hay un ciudadano de atención preferencial asignado a su mesa esperando ser llamado. ¿Desea atenderlo ahora?</p>
            
            <div class="grid grid-cols-2 gap-4">
                <button onclick="cerrarRecordatorio()" class="py-4 rounded-2xl bg-slate-100 text-slate-500 font-black uppercase tracking-widest text-[10px] hover:bg-slate-200 transition-colors">
                    Ver después
                </button>
                <button onclick="aceptarPrioritarioModal()" class="py-4 rounded-2xl bg-[#ffb500] text-[#0a0455] font-black uppercase tracking-widest text-[10px] shadow-lg shadow-yellow-600/20 hover:scale-[1.02] active:scale-95 transition-all">
                    ATENDER YA
                </button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL DATOS PERSONA ────────────────────────────── -->
    <div id="modal-persona" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md">
        <div class="bg-white rounded-[32px] p-8 w-full max-w-md border border-slate-100 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Datos del Solicitante</p>
                    <h3 class="text-slate-900 font-extrabold text-xl mt-0.5">Información Personal</h3>
                </div>
                <button onclick="cerrarModal()" class="text-slate-400 hover:text-slate-900 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div id="modal-loading" class="text-center py-8 text-slate-400 font-semibold">Cargando...</div>

            <form id="forma-persona" class="space-y-4 hidden" onsubmit="guardarPersona(event)">
                <input type="hidden" id="f-pers-doc" name="pers_doc">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Número de Documento</label>
                        <input id="f-pers-doc-vis" type="text" disabled
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-slate-400 font-semibold text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Tipo Doc.</label>
                        <select name="pers_tipodoc" id="f-tipodoc" required
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-3 text-slate-900 font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                            <option value="CC">Cédula CC</option>
                            <option value="TI">Tarjeta TI</option>
                            <option value="CE">Céd. Extranjera</option>
                            <option value="PEP">PEP</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                        <input id="f-fecha-nac" name="pers_fecha_nac" type="date"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-3 py-3 text-slate-900 font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Nombres</label>
                        <input id="f-nombres" name="pers_nombres" type="text" required placeholder="Nombres completos"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-slate-900 font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Apellidos</label>
                        <input id="f-apellidos" name="pers_apellidos" type="text" required placeholder="Apellidos completos"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-slate-900 font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Teléfono</label>
                        <input id="f-telefono" name="pers_telefono" type="tel" placeholder="Ej: 310 000 0000"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-slate-900 font-semibold text-sm focus:border-[#10069f] outline-none transition-all">
                    </div>
                </div>

                <div id="modal-error" class="hidden bg-red-50 border border-red-100 text-red-600 rounded-xl px-4 py-3 text-sm font-semibold"></div>
                <div id="modal-success" class="hidden bg-green-50 border border-green-100 text-green-600 rounded-xl px-4 py-3 text-sm font-semibold">✓ Datos guardados correctamente</div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModal()" class="btn-secondary flex-1 font-bold py-3 rounded-xl uppercase tracking-widest text-xs">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary flex-1 text-white font-extrabold py-3 rounded-xl uppercase tracking-widest text-xs border-b-4 border-[#0a0455]">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── MODAL CIERRE DE SESIÓN (Timeout) ────────────────────── -->
    <div id="modal-timeout" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-white rounded-[2rem] overflow-hidden w-full max-w-md shadow-2xl transform transition-all border border-white/10">
            <div class="h-2 bg-[#10069f] w-full"></div>
            <div class="p-8">
                <h3 class="text-3xl font-black text-[#0a0455] mb-4 uppercase tracking-tighter">Cierre de Sesión</h3>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed font-medium">
                    Su Sesión se va a cerrar en un minuto. Cancelar para seguir trabajando o Aceptar para cerrar ahora
                </p>
                <div class="flex gap-4">
                    <button id="btn-timeout-aceptar" onclick="logoutAhora(true)" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl text-lg uppercase transition-all shadow-lg border-b-4 border-red-900">
                        ACEPTAR (<span id="timeout-countdown">60</span>)
                    </button>
                    <button onclick="continuarSesion()" class="flex-1 bg-gray-50 hover:bg-gray-100 text-slate-900 font-bold py-4 rounded-xl text-lg uppercase transition-colors border border-gray-200">
                        CANCELAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── MODAL ADVERTENCIA CIERRE MANUAL ────────────────────── -->
    <div id="modal-logout-manual" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] overflow-hidden w-full max-w-md shadow-2xl transform transition-all border border-white/10 animate-in zoom-in duration-300">
            <div class="h-2 bg-red-600 w-full"></div>
            <div class="p-10 text-center">
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4 uppercase tracking-tighter">Turno de Trabajo Activo</h3>
                <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                    Aún tienes un turno de trabajo iniciado. Para cerrar sesión, primero debes <b>Finalizar Turno</b> usando el botón rojo del panel.
                </p>
                <button onclick="cerrarModalLogoutManual()" class="w-full bg-slate-900 hover:bg-black text-white font-black py-4 rounded-xl text-sm uppercase tracking-widest transition-all shadow-lg border-b-4 border-slate-700">
                    ENTENDIDO
                </button>
            </div>
        </div>
    </div>

    <!-- ─── MODAL CONFIRMACIÓN FINALIZAR TURNO ────────────────────── -->
    <div id="modal-finalizar-turno" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-[2.5rem] overflow-hidden w-full max-w-md shadow-2xl transform transition-all border border-white/10 animate-in zoom-in duration-300">
            <div class="h-2 bg-red-600 w-full"></div>
            <div class="p-10 text-center">
                <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-3xl font-black text-slate-900 mb-4 uppercase tracking-tighter">¿Finalizar Jornada?</h3>
                <p class="text-slate-500 text-lg mb-10 leading-relaxed font-medium">
                    ¿Estás seguro de que deseas finalizar tu turno de trabajo? Quedará registrada la hora de finalización en el sistema.
                </p>
                <div class="flex flex-col gap-3">
                    <button onclick="finalizarTurnoConfirmado()" class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-2xl text-lg uppercase transition-all shadow-lg shadow-red-200 border-b-4 border-red-900 active:scale-95">
                        SÍ, FINALIZAR AHORA
                    </button>
                    <button onclick="cerrarModalFinalizar()" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl text-sm uppercase transition-colors">
                        CANCELAR
                    </button>
                </div>
            </div>
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
            if (res.status === 401) {
                location.href = '{{ route("asesor.login") }}';
                return null;
            }
            return res;
        }

        let currentUsuarioId = null;
        let lastPriorityCount = 0;
        let showReminderLock = false; // Bloqueo para no repetir el modal a cada rato

        async function aceptarPrioritarioModal() {
            cerrarRecordatorio();
            aceptarTurno();
        }

        // ── Iniciar Turno de Trabajo ───────────────────────────────
        async function iniciarTurno() {
            try {
                let res = await fetch('{{ route("asesor.turno.iniciar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                res = await handleFetchResponse(res);
                if (!res) return;
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'Error al iniciar turno.', 'error'); return; }
                actualizarEstadoUI(data.estado, data.ses_inicio, data.total_pausa_ms, data.en_pausa);
                showToast('Turno de trabajo iniciado. ¡Listo para atender!', 'success');
                await refreshPollData();
            } catch (e) { showToast('Error de conexión.', 'error'); }
        }

        function confirmarFinalizarTurno() {
            const modal = document.getElementById('modal-finalizar-turno');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function cerrarModalFinalizar() {
            const modal = document.getElementById('modal-finalizar-turno');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function finalizarTurnoConfirmado() {
            cerrarModalFinalizar();
            await finalizarTurno();
        }

        async function finalizarTurno() {
            try {
                let res = await fetch('{{ route("asesor.turno.finalizar") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                res = await handleFetchResponse(res);
                if (!res) return;
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'Error al finalizar turno.', 'error'); return; }
                document.getElementById('card-turno-actual').classList.add('hidden');
                actualizarEstadoUI('inactivo');
                showToast('Turno de trabajo finalizado.', 'warn');
                await refreshPollData();
            } catch (e) { showToast('Error de conexión.', 'error'); }
        }

        function cerrarRecordatorio() {
            document.getElementById('modal-prioridad').classList.add('hidden');
            document.getElementById('modal-prioridad').classList.remove('flex');
            showReminderLock = true;
        } 

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

        // ── Variables de turno de trabajo ───────────────────────
        let sesInicioTimestamp = null; // Timestamp ISO de inicio de turno
        let totalPausaMs = 0;          // Total de tiempo en pausa (ms)
        let enPausa = false;          // ¿Está actualmente en pausa?
        let timerInterval = null;

        function formatDuracion(segundos) {
            const h = Math.floor(segundos / 3600);
            const m = Math.floor((segundos % 3600) / 60);
            const s = segundos % 60;
            if (h > 0) return `${h}h ${m}m`;
            if (m > 0) return `${m}m ${s}s`;
            return `${s}s`;
        }

        function iniciarTimerTurno(isoInicio, pausaMs = 0, isPaused = false) {
            if (!isoInicio) {
                detenerTimerTurno();
                return;
            }
            sesInicioTimestamp = new Date(isoInicio).getTime();
            totalPausaMs = pausaMs;
            enPausa = isPaused;

            const timerEl = document.getElementById('turno-timer-texto');
            clearInterval(timerInterval);
            if (!timerEl) return;

            timerInterval = setInterval(() => {
                if (enPausa) return; // Congelar visualmente si está en pausa

                const now = Date.now();
                const diffSec = Math.floor((now - sesInicioTimestamp - totalPausaMs) / 1000);
                timerEl.innerText = formatDuracion(Math.max(0, diffSec));
            }, 1000);
        }

        function detenerTimerTurno() {
            clearInterval(timerInterval);
            sesInicioTimestamp = null;
            totalPausaMs = 0;
            enPausa = false;
            const timerEl = document.getElementById('turno-timer-texto');
            if (timerEl) timerEl.innerText = '—';
        }

        // ── Actualizar UI de estado ────────────────────────────────
        function actualizarEstadoUI(estado, sesInicio = null, pausaMs = 0, isPaused = false) {
            const badge      = document.getElementById('estado-badge');
            const textoEl    = document.getElementById('estado-texto');
            const btnAceptar = document.getElementById('btn-aceptar');
            const btnEspera  = document.getElementById('btn-espera');
            const btnEsperaTexto  = document.getElementById('btn-espera-texto');
            const btnTurno   = document.getElementById('btn-turno');
            const timerDiv   = document.getElementById('turno-timer');
            const cardInactivo = document.getElementById('card-inactivo');

            enPausa = isPaused; // Sincronizar estado de pausa
            totalPausaMs = pausaMs;

            const labels = { disponible: 'Disponible', en_espera: 'En Espera', ocupado: 'Ocupado', inactivo: 'Inactivo' };
            textoEl.innerText = labels[estado] || estado;

            badge.className = `flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border-2 ` + (
                estado === 'disponible' ? 'bg-green-600 text-white border-green-600' :
                estado === 'en_espera'  ? 'bg-gray-400 text-white border-gray-400' :
                estado === 'inactivo'   ? 'bg-gray-700 text-white border-gray-700' :
                                          'bg-blue-600 text-white border-blue-600'
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
            if (estado === 'ocupado' || estado === 'inactivo') {
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

            // Botón Turno + cronómetro + aviso inactivo
            if (btnTurno) {
                if (estado === 'inactivo') {
                    btnTurno.onclick = iniciarTurno;
                    btnTurno.className = 'w-full bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-emerald-700 pulse-blue';
                    btnTurno.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M12 8v4l3 3"/></svg> ▶ Iniciar Turno';
                    if (timerDiv) timerDiv.classList.add('hidden');
                    if (cardInactivo) cardInactivo.classList.remove('hidden');
                    detenerTimerTurno();
                } else {
                    btnTurno.onclick = confirmarFinalizarTurno;
                    btnTurno.className = 'w-full bg-red-800 hover:bg-red-900 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-red-950';
                    btnTurno.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 10h6v4H9z"/></svg> ■ Finalizar Turno';
                    if (timerDiv) timerDiv.classList.remove('hidden');
                    if (cardInactivo) cardInactivo.classList.add('hidden');
                    
                    // Reiniciar timer con datos de pausa
                    iniciarTimerTurno(sesInicio, pausaMs, isPaused);
                }
            }
        }

        // ── Aceptar Turno Específico (Prioritarios) ────────────────
        async function aceptarTurnoEspecifico(turId) {
            try {
                let res = await fetch('{{ route("asesor.aceptar") }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ tur_id: turId })
                });
                
                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'No se pudo aceptar el turno.', 'error'); return; }
                
                procesarTurnoAceptado(data);
            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // Helper para procesar la respuesta de aceptación (general o específica)
        function procesarTurnoAceptado(data) {
            currentUsuarioId = data.usuario_id || null;
            document.getElementById('turno-codigo-actual').innerText = data.codigo_turno;
            if (data.persona) {
                document.getElementById('info-persona').innerText =
                    data.persona.nombres + ' · Doc: ' + data.persona.documento;
            }
            document.getElementById('card-turno-actual').classList.remove('hidden');
            
            // Actualizar estado usando los datos completos si vienen, sino forzar 'ocupado'
            if (data.estado) {
                actualizarEstadoUI(data.estado, data.ses_inicio, data.total_pausa_ms, data.en_pausa);
            } else {
                actualizarEstadoUI('ocupado');
            }

            showToast('Turno ' + data.codigo_turno + ' aceptado.', 'success');
            refreshPollData();
        }

        // ── Aceptar Turno (Normal / Siguiente) ──────────────────────
        async function aceptarTurno() {
            try {
                let res = await fetch('{{ route("asesor.aceptar") }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'No hay turnos disponibles.', 'warn');
                    return;
                }

                procesarTurnoAceptado(data);
            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Finalizar Atención ─────────────────────────────────────
        async function finalizarAtencion(estado = 'atendido') {
            try {
                let res = await fetch('{{ route("asesor.finalizar") }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ estado: estado })
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'Error al finalizar.', 'error');
                    return;
                }

                document.getElementById('card-turno-actual').classList.add('hidden');
                
                if (data.estado) {
                    actualizarEstadoUI(data.estado, data.ses_inicio, data.total_pausa_ms, data.en_pausa);
                } else {
                    actualizarEstadoUI('disponible');
                }

                showToast('Atención finalizada correctamente.', 'success');
                await refreshPollData();

            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Toggle Espera ──────────────────────────────────────────
        async function toggleEspera() {
            try {
                let res  = await fetch('{{ route("asesor.espera") }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                const data = await res.json();

                if (!res.ok) {
                    showToast(data.error || 'Error al cambiar estado.', 'warn');
                    return;
                }

                actualizarEstadoUI(data.estado, data.ses_inicio, data.total_pausa_ms, data.en_pausa);
                const msg = data.estado === 'en_espera' ? 'Ahora estás en espera.' : 'Actividad reanudada.';
                showToast(msg, data.estado === 'en_espera' ? 'warn' : 'success');

            } catch (e) {
                showToast('Error de conexión.', 'error');
            }
        }

        // ── Identificador de Pestaña (Aislamiento) ────────────────
        if (!sessionStorage.getItem('asesor_tab_id')) {
            sessionStorage.setItem('asesor_tab_id', 'tab_' + Math.random().toString(36).substr(2, 9));
        }
        const TAB_ID = sessionStorage.getItem('asesor_tab_id');

        // ── Polling de datos ────────────────────────────────────────
        let currentTipoAsesor = '{{ $asesor->ase_tipo_asesor }}';

        async function refreshPollData() {
            try {
                const timestamp = new Date().getTime();
                const res  = await fetch('{{ route("asesor.api.estado") }}?window_id=' + TAB_ID + '&_t=' + timestamp, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-store'
                });

                // Si el servidor nos redirigió (302), significa que la sesión expiró
                if (res.redirected) {
                    location.reload(); // Esto disparará la redirección real a sesión finalizada
                    return;
                }

                if (res.status === 401) { location.href = '{{ route("asesor.login") }}'; return; }
                const data = await res.json();

                // 1. Actualizar ESTADO GENERAL
                actualizarEstadoUI(data.estado, data.ses_inicio, data.total_pausa_ms, data.en_pausa);

                // Detección de cambio de perfil remoto
                if (data.tipo_asesor && data.tipo_asesor !== currentTipoAsesor) {
                    currentTipoAsesor = data.tipo_asesor;
                    
                    const labelsTipo = {
                        'V': 'Víctimas',
                        'G': 'General',
                        'P': 'Prioritario',
                        'E': 'Empresario'
                    };
                    const nuevoNombre = labelsTipo[currentTipoAsesor] || currentTipoAsesor;
                    
                    const spanCola = document.getElementById('tipo-cola-texto');
                    if (spanCola) {
                        spanCola.innerText = nuevoNombre;
                    }
                    
                    showToast('Tu perfil ha sido reasignado a: ' + nuevoNombre, 'warn');
                }

                // 1. Actualizar COLA PRIORITARIA (Global para G)
                const listaPrioritaria = document.getElementById('lista-cola-prioritaria');

                if (data.cola_prioritaria && data.cola_prioritaria.length > 0) {
                    listaPrioritaria.innerHTML = data.cola_prioritaria.map(t => {
                        const disabled = !t.habilitado;
                        return `
                        <div class="flex items-center justify-between ${disabled ? 'bg-gray-50 border border-gray-200 opacity-60' : 'bg-amber-50 border border-amber-200'} p-4 rounded-2xl shadow-sm animate-in fade-in zoom-in duration-300">
                            <div>
                                <p class="${disabled ? 'text-gray-400' : 'text-amber-600'} text-[10px] font-black uppercase tracking-widest">Turno Especial${disabled ? ' · Sin disponibilidad' : ''}</p>
                                <p class="text-slate-900 font-black text-2xl">${t.codigo}</p>
                            </div>
                            <button onclick="${disabled ? '' : 'aceptarTurnoEspecifico(' + t.id + ')'}"
                                ${disabled ? 'disabled title="Atiende primero los turnos de tu cola"' : ''}
                                class="${disabled ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-[#ffb500] text-[#0a0455] hover:scale-105 cursor-pointer'} font-black py-2 px-4 rounded-lg text-xs uppercase transition-transform border-b-2 ${disabled ? 'border-gray-400' : 'border-yellow-700'}">
                                ATENDER
                            </button>
                        </div>`;
                    }).join('');
                } else {
                    listaPrioritaria.innerHTML = `
                        <div class="col-span-full py-8 text-center border-2 border-dashed border-slate-100 rounded-[2rem]">
                            <p class="text-slate-300 font-bold uppercase tracking-widest text-[10px]">Sin turnos prioritarios</p>
                        </div>`;
                }

                // 2. Actualizar COLA VÍCTIMAS
                const listaVictimas = document.getElementById('lista-cola-victimas');
                if (listaVictimas) {
                    const colaVictimas = data.cola_victimas || [];
                    document.getElementById('cola-victimas-count').innerText = colaVictimas.length;

                    if (colaVictimas.length === 0) {
                        listaVictimas.innerHTML = `
                            <div class="col-span-full py-8 text-center border-2 border-dashed border-slate-100 rounded-[2rem]">
                                <p class="text-slate-300 font-bold uppercase tracking-widest text-[10px]">Sin turnos víctimas</p>
                            </div>`;
                    } else {
                        listaVictimas.innerHTML = colaVictimas.map(t => {
                            const disabled = !t.habilitado;
                            return `
                            <div class="flex items-center justify-between ${disabled ? 'bg-gray-50 border border-gray-200 opacity-60' : 'bg-red-50 border border-red-100'} p-4 rounded-2xl shadow-sm animate-in fade-in zoom-in duration-300">
                                <div>
                                    <p class="${disabled ? 'text-gray-400' : 'text-red-600'} text-[10px] font-black uppercase tracking-widest">Víctimas${disabled ? ' · Sin disponibilidad' : ''}</p>
                                    <p class="text-slate-900 font-black text-2xl">${t.codigo}</p>
                                </div>
                                <button onclick="${disabled ? '' : 'aceptarTurnoEspecifico(' + t.id + ')'}"
                                    ${disabled ? 'disabled title="Atiende primero los turnos de tu cola"' : ''}
                                    class="${disabled ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-red-600 text-white hover:scale-105 cursor-pointer'} font-black py-2 px-4 rounded-lg text-xs uppercase transition-transform border-b-2 ${disabled ? 'border-gray-400' : 'border-red-800'}">
                                    ATENDER
                                </button>
                            </div>`;
                        }).join('');
                    }
                }

                // 3. Actualizar COLA GENERAL (Asignada a mí)
                const listaCola = document.getElementById('lista-cola');
                document.getElementById('cola-count').innerText = data.cola_general.length;

                if (data.cola_general.length === 0) {
                    listaCola.innerHTML = `
                        <div class="text-center py-12 border-2 border-dashed border-slate-100 rounded-[2rem]">
                            <p class="text-slate-300 font-bold uppercase tracking-widest text-sm">Sin turnos generales</p>
                        </div>`;
                } else {
                    listaCola.innerHTML = data.cola_general.map(t => {
                        const disabled = !t.habilitado;
                        return `
                        <div class="queue-item rounded-2xl px-5 py-4 flex items-center justify-between animate-in slide-in-from-right duration-300 ${disabled ? 'bg-gray-50 border border-gray-200 opacity-60' : 'bg-white border border-slate-100 shadow-sm'}">
                            <div>
                                <p class="${disabled ? 'text-gray-400' : 'text-slate-400'} text-[10px] font-bold uppercase tracking-wider">General${disabled ? ' · Sin disponibilidad' : ''}</p>
                                <p class="text-slate-900 font-black text-2xl">${t.codigo}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-slate-500 text-xs font-semibold">${t.hora ? t.hora.substring(11,16) : ''}</p>
                                <button onclick="${disabled ? '' : 'aceptarTurnoEspecifico(' + t.id + ')'}"
                                    ${disabled ? 'disabled title="Atiende primero los turnos de tu cola"' : ''}
                                    class="${disabled ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-slate-700 text-white hover:scale-105 cursor-pointer'} font-black py-2 px-4 rounded-lg text-xs uppercase transition-transform border-b-2 ${disabled ? 'border-gray-400' : 'border-slate-900'}">
                                    ATENDER
                                </button>
                            </div>
                        </div>`;
                    }).join('');
                }

                // 4. Actualizar COLA EMPRESARIO
                const listaEmpresario = document.getElementById('lista-cola-empresario');
                const colaEmpresario = data.cola_empresario || [];
                document.getElementById('cola-empresario-count').innerText = colaEmpresario.length;

                if (colaEmpresario.length === 0) {
                    listaEmpresario.innerHTML = `
                        <div class="text-center py-8 border-2 border-dashed border-slate-100 rounded-[2rem]">
                            <p class="text-slate-300 font-bold uppercase tracking-widest text-sm">Sin turnos empresariales</p>
                        </div>`;
                } else {
                    listaEmpresario.innerHTML = colaEmpresario.map(t => {
                        const disabled = !t.habilitado;
                        return `
                        <div class="queue-item rounded-2xl px-5 py-4 flex items-center justify-between animate-in slide-in-from-right duration-300 ${disabled ? 'bg-gray-50 border border-gray-200 opacity-60' : 'bg-blue-50 border border-blue-100 shadow-sm'}">
                            <div>
                                <p class="${disabled ? 'text-gray-400' : 'text-blue-600'} text-[10px] font-bold uppercase tracking-wider">Empresario${disabled ? ' · Sin disponibilidad' : ''}</p>
                                <p class="text-slate-900 font-black text-2xl">${t.codigo}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <p class="text-slate-500 text-xs font-semibold">${t.hora ? t.hora.substring(11,16) : ''}</p>
                                <button onclick="${disabled ? '' : 'aceptarTurnoEspecifico(' + t.id + ')'}"
                                    ${disabled ? 'disabled title="Atiende primero los turnos de tu cola"' : ''}
                                    class="${disabled ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 text-white hover:scale-105 cursor-pointer'} font-black py-2 px-4 rounded-lg text-xs uppercase transition-transform border-b-2 ${disabled ? 'border-gray-400' : 'border-blue-800'}">
                                    ATENDER
                                </button>
                            </div>
                        </div>`;
                    }).join('');
                }

                // 5. Lógica de Recordatorio (Solo si el asesor queda disponible y hay prioritarios)
                const estadoActual = document.getElementById('estado-texto').innerText.trim().toLowerCase();
                const prioritariosCount = data.cola_prioritaria ? data.cola_prioritaria.length : 0;

                if (prioritariosCount > 0 && estadoActual === 'disponible') {
                    // Si el número de prioritarios creció, desbloqueamos el aviso
                    if (prioritariosCount > lastPriorityCount) {
                        showReminderLock = false;
                    }
                    
                    if (!showReminderLock) {
                        const modalP = document.getElementById('modal-prioridad');
                        if (modalP.classList.contains('hidden')) {
                            // Audio (opcional, solo si el user lo permite)
                            // const beep = new Audio('/audio/notify.mp3'); beep.play().catch(e => {});
                            modalP.classList.remove('hidden');
                            modalP.classList.add('flex');
                        }
                    }
                } else if (prioritariosCount === 0) {
                    showReminderLock = false; // Reset si se vacía la cola
                }
                
                lastPriorityCount = prioritariosCount;
                firstLoad = false;

                // Actualizar cronómetro de turno con datos del servidor
                if (data.ses_inicio) {
                    if (!sesInicioTimestamp) {
                        iniciarTimerTurno(data.ses_inicio, data.total_pausa_ms, data.en_pausa);
                        const timerDiv = document.getElementById('turno-timer');
                        if (timerDiv) timerDiv.classList.remove('hidden');
                        const cardInactivo = document.getElementById('card-inactivo');
                        if (cardInactivo) cardInactivo.classList.add('hidden');
                    }
                } else {
                    detenerTimerTurno();
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
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
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

            // Leer valores directamente de los inputs
            const body = {
                pers_doc:       document.getElementById('f-pers-doc').value,
                pers_tipodoc:   document.getElementById('f-tipodoc').value,
                pers_nombres:   document.getElementById('f-nombres').value,
                pers_apellidos: document.getElementById('f-apellidos').value,
                pers_telefono:  document.getElementById('f-telefono').value,
                pers_fecha_nac: document.getElementById('f-fecha-nac').value,
            };

            try {
                let res = await fetch('{{ route("asesor.persona.update") }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': CSRF, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(body)
                });

                res = await handleFetchResponse(res);
                if (!res) return;

                let result;
                try {
                    result = await res.json();
                } catch (_) {
                    // Respuesta no es JSON (probablemente HTML de redirección)
                    location.reload();
                    return;
                }

                if (!res.ok) {
                    const errEl = document.getElementById('modal-error');
                    const msg = result.error || Object.values(result.errors || {}).flat().join(', ') || 'Error al guardar';
                    errEl.innerText = msg;
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
                const errEl = document.getElementById('modal-error');
                errEl.innerText = 'Error de conexión: ' + e.message;
                errEl.classList.remove('hidden');
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-persona').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });

        // ── Inactividad (5 min total) ──────────────────────────────
        let lastActivityTimestamp = Date.now();
        let countdownTime = 60;
        let countdownInterval = null;
        let heartbeatInterval = null;
        const IDLE_LIMIT = 9 * 60 * 1000; // 9 minutos en milisegundos (el aviso dura 1 min adicional)

        function resetIdleTimer() {
            if (document.getElementById('modal-timeout').classList.contains('hidden')) {
                const now = Date.now();
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
                await fetch('{{ route("asesor.api.estado") }}?heartbeat=1&window_id=' + TAB_ID, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            } catch (e) { console.error("Error heartbeat", e); }
        }

        // Detectar actividad
        ['mousedown', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
            document.addEventListener(evt, resetIdleTimer, true);
        });

        // ── Polling automático de datos cada 4 segundos ────────────
        refreshPollData();
        setInterval(refreshPollData, 4000);

        // Revisar inactividad (timestamp-based)
        setInterval(() => {
            const now = Date.now();
            const inactiveTime = now - lastActivityTimestamp;

            // SOLO mostrar aviso si estamos en estado 'disponible'
            // Si está en 'en_espera' u 'ocupado', el asesor está "activo" en su labor
            const estadoActual = document.getElementById('estado-texto').innerText.trim().toLowerCase();
            const esDisponible = estadoActual === 'disponible';

            if (esDisponible && inactiveTime >= IDLE_LIMIT && document.getElementById('modal-timeout').classList.contains('hidden')) {
                mostrarAvisoTimeout();
            } else if (!esDisponible) {
                // Si no está disponible, mantenemos el timestamp al día para que no salte al volver a disponible
                lastActivityTimestamp = now;
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
                await enviarHeartbeat();
                clearInterval(countdownInterval);
                document.getElementById('modal-timeout').classList.add('hidden');
                document.getElementById('modal-timeout').classList.remove('flex');
                lastActivityTimestamp = Date.now();
            } catch (e) {
                console.error("Error al refrescar sesión", e);
            }
        }

        function logoutAhora(porInactividad = false) {
            const form = document.createElement('form');
            form.method = 'POST';
            let action = '{{ route("asesor.logout") }}';
            if (porInactividad) action += '?inactivity=1';
            form.action = action;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = CSRF;
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }

        function confirmarLogoutManual(e) {
            const estadoActual = document.getElementById('estado-texto').innerText.trim().toLowerCase();
            if (estadoActual !== 'inactivo') {
                e.preventDefault();
                const modal = document.getElementById('modal-logout-manual');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function cerrarModalLogoutManual() {
            const modal = document.getElementById('modal-logout-manual');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

    </script>
</body>
</html>
