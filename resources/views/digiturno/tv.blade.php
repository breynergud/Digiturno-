<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno - Pantalla</title>
    <!-- Tailwind 3.4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; overflow: hidden; background-color: #000; }
        .tv-card {
            background: #ffffff;
            border-left: 12px solid #10069f;
            transition: all 0.5s ease;
        }
        .main-call-bg {
            background: linear-gradient(135deg, #0a0455 0%, #000000 100%);
            border: 4px solid #10069f;
        }
        .animate-call {
            animation: highlight-call 2s infinite;
        }
        @keyframes highlight-call {
            0% { border-color: #10069f; box-shadow: 0 0 0 0 rgba(16, 6, 159, 0.4); }
            50% { border-color: #ffb500; box-shadow: 0 0 50px 10px rgba(16, 6, 159, 0.6); }
            100% { border-color: #10069f; box-shadow: 0 0 0 0 rgba(16, 6, 159, 0.4); }
        }
        .news-marquee {
            background-color: #10069f;
            color: white;
            border-top: 4px solid #ffb500;
        }
        /* Overlay de Audio */
        #audio-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            items-center: center;
            text-align: center;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col p-6 lg:p-10 text-white">

    <!-- Overlay para activar audio -->
    <div id="audio-overlay">
        <div class="bg-white p-12 rounded-[3rem] shadow-2xl max-w-2xl mx-auto">
            <div class="text-[#10069f] mb-8">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
            </div>
            <h2 class="text-4xl font-black text-black mb-6 uppercase tracking-tight">Activar Sistema de Audio</h2>
            <p class="text-gray-600 text-xl font-medium mb-10">Para escuchar los llamados de turnos, por favor haz clic en el botón.</p>
            <button onclick="enableAudio()" class="bg-[#10069f] hover:bg-[#ffb500] text-white hover:text-black px-12 py-6 rounded-2xl text-2xl font-black uppercase tracking-widest transition-all transform hover:scale-105 shadow-xl">
                Activar Sonido
            </button>
        </div>
    </div>

    <!-- Header TV Institucional -->
    <div class="flex justify-between items-center mb-8 border-b-2 border-white/10 pb-8">
        <div class="flex items-center space-x-8">
            <div class="px-8 py-3 bg-white rounded-2xl flex items-center justify-center shadow-xl">
                 <div class="text-4xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[8px] uppercase tracking-[0.3em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                 </div>
            </div>
            <div>
                <h1 class="text-5xl font-black tracking-tight uppercase"><span class="text-[#ffb500]">APE</span> <span class="text-[#10069f]">DIGITURNO</span></h1>
                <p class="text-gray-400 font-bold uppercase tracking-[0.5em] text-sm mt-1">Agencia Pública de Empleo - Territorial</p>
            </div>
        </div>
        <div class="text-right flex flex-col items-end">
            <div id="clock" class="text-7xl font-black text-white tabular-nums drop-shadow-lg">00:00:00</div>
            <div id="date" class="text-gray-400 font-bold uppercase tracking-widest text-lg mt-1 mb-2">Cargando fecha...</div>
            <a href="/" class="text-xs font-black uppercase tracking-widest text-white/20 hover:text-[#ffb500] transition-colors flex items-center group">
                <svg class="w-4 h-4 mr-2 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Configuración / Inicio
            </a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-8">
        
        <!-- Columna Izquierda: Llamado Principal (3/5 del ancho) -->
        <div class="lg:col-span-3 main-call-bg rounded-[3rem] p-10 flex flex-col justify-center items-center text-center shadow-2xl relative overflow-hidden animate-call">
            <h2 class="text-2xl font-black text-[#ffb500] uppercase tracking-[0.2em] mb-4">Llamado Actual</h2>
            
            <div id="main-turn-code" class="text-[clamp(6rem,12vw,15rem)] leading-none font-black text-white tracking-tight drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)] w-full overflow-hidden text-ellipsis px-2">
                ---
            </div>
            
            <div class="mt-8 bg-white text-black px-16 py-8 rounded-3xl shadow-2xl border-b-8 border-[#ffb500]">
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
            <h3 class="text-xl font-black text-white uppercase tracking-widest px-4 mb-1 border-l-4 border-[#ffb500]">Últimos Turnos</h3>
            
            <div id="category-container" class="flex flex-col gap-4 overflow-hidden">
                <!-- Se poblará dinámicamente -->
                <div class="tv-card rounded-2xl p-6 flex justify-between items-center text-black opacity-50">
                    <p class="font-bold text-gray-400 uppercase tracking-widest italic">Sincronizando sistema...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer TV: Barra de Noticias -->
    <div class="mt-8 news-marquee p-6 rounded-2xl shadow-lg border border-white/20">
        <marquee class="text-3xl font-extrabold uppercase tracking-wide">
            Bienvenido a la Agencia Pública de Empleo (APE). Por favor tenga su documento de identidad a la mano. • Explore nuestras vacantes vigentes en el portal oficial. • Recuerde que todos nuestros servicios son gratuitos. • APE: Intermediación laboral transparente y eficiente.
        </marquee>
    </div>

    <script>
        let announcedAttentions = new Set();
        let firstLoad = true;
        let audioEnabled = false;

        const labels = {
            'victimas': 'Víctimas',
            'especial': 'Especial',
            'general': 'General',
            'empresario': 'Empresas',
            'prioritario': 'Prioritario'
        };

        function enableAudio() {
            audioEnabled = true;
            document.getElementById('audio-overlay').style.display = 'none';
            // Tocar un silencio o el primer sonido para desbloquear
            const audio = document.getElementById('call-sound');
            audio.muted = true;
            audio.play().then(() => {
                audio.muted = false;
            }).catch(e => console.log("Audio play blocked", e));
            
            // Probar síntesis (algunos navegadores requieren esta interacción)
            if (window.speechSynthesis) {
                const init = new SpeechSynthesisUtterance("");
                window.speechSynthesis.speak(init);
            }
        }

        function speakTurn(codigo, tipo, mesa) {
            if (!window.speechSynthesis || !audioEnabled) return;
            
            const codigoLimpio = codigo.replace('-', ' ');
            const mensaje = `Turno, ${codigoLimpio}. Atención, ${tipo}. Por favor, diríjase a la mesa, ${mesa}`;
            
            const utterance = new SpeechSynthesisUtterance(mensaje);
            utterance.lang = 'es-ES';
            utterance.rate = 0.85;
            utterance.pitch = 1.0;
            
            window.speechSynthesis.speak(utterance);
        }

        async function updateTurns() {
            try {
                const response = await fetch('{{ route("turnos.api.pendientes") }}');
                const data = await response.json();
                
                const container = document.getElementById('category-container');
                container.innerHTML = '';
                
                // 1. Mostrar la lista de espera (Waiting)
                // Estos son los turnos asignados pero aún no aceptados
                data.waiting.forEach(turn => {
                    if (turn) {
                        const card = document.createElement('div');
                        card.className = "tv-card rounded-2xl p-6 flex justify-between items-center text-black bg-white shadow-lg border-l-[12px] border-[#10069f]";

                        card.innerHTML = `
                            <div>
                                <h3 class="text-3xl font-black uppercase tracking-tight text-gray-400 mb-1">${labels[turn.tipo_atencion] || turn.tipo_atencion}</h3>
                                <p class="text-xl font-bold text-black uppercase tracking-widest">Mesa ${turn.mesa}</p>
                            </div>
                            <div class="text-7xl font-black text-[#10069f] tabular-nums">${turn.codigo_turno}</div>
                        `;
                        container.appendChild(card);
                    }
                });

                // 2. Manejar los llamados activos (Calling)
                // Estos turnos acaban de ser aceptados por un asesor
                let newestCall = null;
                data.calling.forEach(turn => {
                    if (turn) {
                        if (!newestCall || turn.id > newestCall.id) newestCall = turn;

                        // Voz: SOLO si no ha sido anunciado
                        if (!announcedAttentions.has(turn.atencion_id)) {
                            if (!firstLoad && audioEnabled) {
                                const labelText = labels[turn.tipo_atencion] || turn.tipo_atencion;
                                speakTurn(turn.codigo_turno, labelText, turn.mesa);
                            }
                            announcedAttentions.add(turn.atencion_id);
                        }
                    }
                });

                // Mostrar el llamado actual en el panel principal
                // Desaparecerá solo después de los 20 segundos que define el backend
                if (newestCall) {
                    const mainCode = document.getElementById('main-turn-code');
                    const mainMesa = document.getElementById('main-turn-mesa');
                    
                    if (mainCode.innerText !== newestCall.codigo_turno) {
                        mainCode.innerText = newestCall.codigo_turno;
                        mainMesa.innerText = 'MESA ' + newestCall.mesa;
                    }
                } else {
                    const mainCode = document.getElementById('main-turn-code');
                    const mainMesa = document.getElementById('main-turn-mesa');
                    mainCode.innerText = '---';
                    mainMesa.innerText = 'MESA --';
                }

                if (data.waiting.length === 0 && !newestCall) {
                    container.innerHTML = `
                        <div class="tv-card rounded-2xl p-6 flex justify-between items-center text-black opacity-50">
                            <p class="font-bold text-gray-400 uppercase tracking-widest italic">Sin turnos pendientes...</p>
                        </div>`;
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
