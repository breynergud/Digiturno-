<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA Digiturno - Pantalla</title>
    <!-- Tailwind 3.4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; overflow: hidden; background-color: #000; }
        .tv-card {
            background: #ffffff;
            border-left: 12px solid #39a900;
            transition: all 0.5s ease;
        }
        .main-call-bg {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            border: 4px solid #39a900;
        }
        .animate-call {
            animation: highlight-call 2s infinite;
        }
        @keyframes highlight-call {
            0% { border-color: #39a900; box-shadow: 0 0 0 0 rgba(57, 169, 0, 0.4); }
            50% { border-color: #fff; box-shadow: 0 0 50px 10px rgba(57, 169, 0, 0.6); }
            100% { border-color: #39a900; box-shadow: 0 0 0 0 rgba(57, 169, 0, 0.4); }
        }
        .news-marquee {
            background-color: #39a900;
            color: white;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col p-6 lg:p-10 text-white">

    <!-- Header TV Institucional -->
    <div class="flex justify-between items-center mb-10 border-b border-white/10 pb-6">
        <div class="flex items-center space-x-6">
            <div class="w-16 h-16 bg-white rounded-xl p-2 flex items-center justify-center">
                <img src="{{ asset('images/logosena.png') }}" alt="SENA" class="w-full">
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tight uppercase">SENA <span class="text-sena-green text-[#39a900]">DIGITURNO</span></h1>
                <p class="text-gray-400 font-bold uppercase tracking-[0.4em] text-xs">Centro de Atención Institucional</p>
            </div>
        </div>
        <div class="text-right flex flex-col items-end">
            <div id="clock" class="text-5xl font-black text-white tabular-nums">00:00:00</div>
            <div id="date" class="text-gray-400 font-bold uppercase tracking-widest text-sm mt-1 mb-2">Cargando fecha...</div>
            <a href="/" class="text-[10px] font-black uppercase tracking-widest text-white/30 hover:text-sena-green transition-colors flex items-center group">
                <svg class="w-3 h-3 mr-1.5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Configuración / Inicio
            </a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-8">
        
        <!-- Columna Izquierda: Llamado Principal (3/5 del ancho) -->
        <div class="lg:col-span-3 main-call-bg rounded-[3rem] p-10 flex flex-col justify-center items-center text-center shadow-2xl relative overflow-hidden animate-call">
            <h2 class="text-2xl font-black text-[#39a900] uppercase tracking-[0.2em] mb-4">Llamado Actual</h2>
            
            <div id="main-turn-code" class="text-[clamp(10rem,30vw,22rem)] leading-none font-black text-white tracking-tighter drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]">
                ---
            </div>
            
            <div class="mt-8 bg-white text-black px-16 py-8 rounded-3xl shadow-2xl border-b-8 border-[#39a900]">
                <p class="text-xl font-bold uppercase tracking-widest text-gray-500 mb-1">Por favor diríjase a:</p>
                <div id="main-turn-mesa" class="text-6xl md:text-8xl font-black uppercase tracking-tighter text-black">
                    MESA --
                </div>
            </div>
            
            <!-- Sonido de llamado -->
            <audio id="call-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>
        </div>

        <!-- Columna Derecha: Otros Turnos Recientes (2/5 del ancho) -->
        <div class="lg:col-span-2 flex flex-col gap-5">
            <h3 class="text-xl font-black text-white uppercase tracking-widest px-4 mb-1 border-l-4 border-[#39a900]">Últimos Turnos</h3>
            
            <div id="category-container" class="flex flex-col gap-4 overflow-hidden">
                <!-- Se poblará dinámicamente -->
                <div class="tv-card rounded-2xl p-6 flex justify-between items-center text-black opacity-50">
                    <p class="font-bold text-gray-400 uppercase tracking-widest italic">Sincronizando sistema...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer TV: Barra de Noticias -->
    <div class="mt-10 news-marquee p-5 rounded-2xl shadow-lg border border-white/20">
        <marquee class="text-2xl font-extrabold uppercase tracking-wide">
            Bienvenido al SENA. Por favor tenga su documento de identidad a la mano. • Los empresarios serán atendidos en la oficina de Coordinación. • Recuerde respetar el orden de llamado. • SENA: Emprendimiento, Empleo y Equidad.
        </marquee>
    </div>

    <script>
        let lastCodes = {};
        let firstLoad = true;

        async function updateTurns() {
            try {
                // Usamos el endpoint de pendientes: solo turnos NO atendidos
                const response = await fetch('{{ route("turnos.api.pendientes") }}');
                const turns = await response.json();
                
                const container = document.getElementById('category-container');
                container.innerHTML = '';
                
                let newestTurn = null;

                turns.forEach(turn => {
                    if (turn) {
                        // Detectar el más reciente globalmente por ID
                        if (!newestTurn || turn.id > newestTurn.id) {
                            newestTurn = turn;
                        }

                        // Sonido si el código cambió (solo para el más nuevo)
                        if (!firstLoad && lastCodes[turn.tipo_atencion] !== turn.codigo_turno) {
                             const turnDate = new Date(turn.created_at);
                             if (new Date() - turnDate < 20000) {
                                document.getElementById('call-sound').play();
                             }
                        }
                        lastCodes[turn.tipo_atencion] = turn.codigo_turno;

                        const card = document.createElement('div');
                        card.className = "tv-card rounded-2xl p-6 flex justify-between items-center text-black shadow-lg";
                        card.style.borderLeftColor = '#39a900';
                        
                        const labels = {
                            'victimas': 'Víctimas',
                            'especial': 'Especial',
                            'general': 'General',
                            'empresario': 'Empresario'
                        };

                        card.innerHTML = `
                            <div>
                                <h3 class="text-2xl font-black uppercase tracking-tight text-gray-400 mb-0.5">${labels[turn.tipo_atencion] || turn.tipo_atencion}</h3>
                                <p class="text-lg font-bold text-black uppercase tracking-widest">Mesa ${turn.mesa}</p>
                            </div>
                            <div class="text-6xl font-black text-[#39a900] tabular-nums">${turn.codigo_turno}</div>
                        `;
                        container.appendChild(card);
                    }
                });

                // Si no hay turnos pendientes, mostrar mensaje
                if (turns.length === 0) {
                    container.innerHTML = `
                        <div class="tv-card rounded-2xl p-6 flex justify-between items-center text-black opacity-50">
                            <p class="font-bold text-gray-400 uppercase tracking-widest italic">Sin turnos en espera...</p>
                        </div>`;
                }

                // Actualizar panel principal con el turno más reciente pendiente
                if (newestTurn) {
                    const mainCode = document.getElementById('main-turn-code');
                    const mainMesa = document.getElementById('main-turn-mesa');
                    
                    if (mainCode.innerText !== newestTurn.codigo_turno) {
                        mainCode.style.opacity = '0';
                        setTimeout(() => {
                            mainCode.innerText = newestTurn.codigo_turno;
                            mainMesa.innerText = 'MESA ' + newestTurn.mesa;
                            mainCode.style.transition = 'opacity 0.5s';
                            mainCode.style.opacity = '1';
                        }, 300);
                    }
                } else {
                    // No hay turnos pendientes: limpiar panel
                    const mainCode = document.getElementById('main-turn-code');
                    const mainMesa = document.getElementById('main-turn-mesa');
                    mainCode.innerText = '---';
                    mainMesa.innerText = 'MESA --';
                }

                firstLoad = false;
            } catch (err) {
                console.error("Error fetching turns:", err);
            }
        }

        // Lógica del Reloj
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('es-ES', { hour12: false });
            document.getElementById('date').innerText = now.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        setInterval(updateClock, 1000);
        setInterval(updateTurns, 4000); // Polling cada 4 segundos
        updateClock();
        updateTurns();

        // Seguridad: Limpiar rastro al cerrar (consistencia con el resto del sistema)
        window.addEventListener('unload', function() {
            if (window.performance && window.performance.navigation.type !== 1) {
                console.log('Cerrando pantalla segura...');
            }
        });
    </script>
</body>
</html>
