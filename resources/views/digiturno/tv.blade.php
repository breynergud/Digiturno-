<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno - Pantalla</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; overflow: hidden; background-color: #f8f9fa; }
        .ape-blue { color: #10069f; }
        .ape-bg-blue { background-color: #10069f; }
        .ape-bg-orange { background-color: #ff6b00; }
        .glass-card { background: white; border-radius: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05); }
        
        .main-call-anim { animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0%, 100% { border-color: #10069f; }
            50% { border-color: #ff6b00; }
        }

        #audio-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.98); z-index: 9999;
            display: flex; justify-content: center; align-items: center;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col p-4">

    <div id="audio-overlay" onclick="enableAudio()">
        <div class="text-center">
            <div class="mb-6 ape-red">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
            </div>
            <h2 class="text-4xl font-black mb-8 italic">SISTEMA AUDIBLE</h2>
            <button class="ape-bg-blue text-white px-12 py-5 rounded-2xl text-2xl font-bold shadow-2xl">ACTIVAR SONIDO</button>
        </div>
    </div>

    <div id="master-container" class="flex flex-col h-full flex-1">
        
        <main class="flex flex-1 gap-4 overflow-hidden mb-4">
            <!-- COLUMNA IZQUIERDA: VIDEO Y MENSAJES -->
            <div class="w-[60%] flex flex-col gap-4">
                <!-- VIDEO -->
                <div class="flex-[3] glass-card overflow-hidden relative shadow-lg">
                    <iframe 
                        class="absolute inset-0 w-full h-full" 
                        src="https://www.youtube.com/embed/nF_azk9vCwo?autoplay=1&mute=1&loop=1&playlist=nF_azk9vCwo&controls=0&modestbranding=1" 
                        frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
                    </iframe>
                </div>

                <!-- CUADRO DE MENSAJE -->
                <div class="flex-1 ape-bg-orange rounded-[1.5rem] p-6 flex items-center justify-center shadow-lg">
                    <h2 class="text-white text-3xl font-black italic tracking-tight text-center">¡Prepárate! Estamos en inscripciones</h2>
                </div>

                <!-- RELOJ E INFO -->
                <div class="h-28 glass-card p-6 flex justify-between items-center">
                    <div class="flex items-center space-x-6">
                        <img src="https://www.sena.edu.co/Style%20Library/sena/images/logoSena.png" class="h-16 w-auto" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/SENA_logo.svg/1200px-SENA_logo.svg.png'">
                        <div class="border-l-2 border-gray-200 pl-6">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Agencia Pública de Empleo</p>
                            <h1 class="text-2xl font-black text-gray-800 tracking-tighter">SENA <span class="ape-blue">APE</span></h1>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-6">
                        <div>
                            <div id="clock" class="text-5xl font-black text-gray-800 leading-none tabular-nums">00:00</div>
                            <div id="date" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Cargando...</div>
                        </div>
                        <div class="ape-blue">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: TABLA DE TURNOS -->
            <div class="w-[40%] flex flex-col gap-4">
                <!-- CABECERA TABLA -->
                <div class="flex gap-4 h-16">
                    <div class="flex-1 ape-bg-blue rounded-2xl flex items-center justify-center shadow-md">
                        <span class="text-white font-black uppercase tracking-[0.4em] text-lg">Turno</span>
                    </div>
                    <div class="flex-1 ape-bg-blue rounded-2xl flex items-center justify-center shadow-md">
                        <span class="text-white font-black uppercase tracking-[0.4em] text-lg">Mesa</span>
                    </div>
                </div>

                <!-- LISTA DE TURNOS -->
                <div id="turns-list" class="flex-1 flex flex-col gap-3 overflow-y-auto pr-2">
                    <!-- Items dinámicos -->
                </div>
            </div>
        </main>

        <footer class="h-10 flex items-center px-6 overflow-hidden bg-white shadow-inner rounded-xl border border-gray-100">
             <div class="text-[10px] font-black ape-blue uppercase tracking-widest mr-6 whitespace-nowrap border-r border-gray-200 pr-6">Aviso Institucional</div>
             <marquee class="text-sm font-bold text-gray-600 uppercase">
                Bienvenido a la Agencia Pública de Empleo (APE). Recuerde que todos nuestros servicios son gratuitos. • Mantenga su documento a la mano. • APE: Transformando vidas a través del empleo digno. • Visite nuestro portal oficial para más vacantes.
             </marquee>
        </footer>
    </div>

    <audio id="call-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

    <script>
        let announcedAttentions = new Set();
        let audioEnabled = false;

        const labels = {
            'victimas': 'Víctimas', 'especial': 'Especial', 'general': 'General', 'empresario': 'Empresas', 'prioritario': 'Prioritario'
        };

        function enableAudio() {
            audioEnabled = true;
            document.getElementById('audio-overlay').style.display = 'none';
            const audio = document.getElementById('call-sound');
            audio.muted = true;
            audio.play().then(() => { audio.muted = false; }).catch(e => console.log(e));
            if (window.speechSynthesis) { window.speechSynthesis.speak(new SpeechSynthesisUtterance("")); }
        }

        function speakTurn(codigo, tipo, mesa) {
            if (!window.speechSynthesis || !audioEnabled) return;
            const msg = new SpeechSynthesisUtterance(`Turno ${codigo.replace('-', ' ')}. Atención ${tipo}. Diríjase a mesa ${mesa}`);
            msg.lang = 'es-ES'; msg.rate = 0.9; window.speechSynthesis.speak(msg);
        }

        async function updateTurns() {
            try {
                const response = await fetch('{{ route("turnos.api.pendientes") }}');
                const data = await response.json();
                
                const list = document.getElementById('turns-list');

                // Combinamos llamados y en espera para la lista de la derecha
                const allTurns = [...data.calling, ...data.waiting].slice(0, 7);

                list.innerHTML = '';
                allTurns.forEach((turn, index) => {
                    const isCalling = index < data.calling.length;
                    const card = document.createElement('div');
                    card.className = `glass-card p-5 flex flex-col gap-1 transition-all ${isCalling ? 'main-call-anim border-l-[15px] border-ape-blue' : ''}`;
                    card.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div class="text-5xl font-black ape-blue tabular-nums tracking-tighter">${turn.codigo_turno}</div>
                            <div class="text-5xl font-black text-gray-800 tabular-nums">${turn.mesa || '--'}</div>
                        </div>
                        <div class="flex justify-between items-center border-t border-gray-100 pt-2 mt-1">
                             <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">${labels[turn.tipo_atencion] || turn.tipo_atencion}</p>
                             ${isCalling ? '<span class="text-[10px] font-black ape-blue animate-pulse tracking-widest">LLAMANDO...</span>' : ''}
                        </div>
                    `;
                    list.appendChild(card);

                    // Audio para el nuevo llamado
                    if (isCalling && !announcedAttentions.has(turn.atencion_id) && audioEnabled) {
                        speakTurn(turn.codigo_turno, labels[turn.tipo_atencion] || turn.tipo_atencion, turn.mesa);
                        announcedAttentions.add(turn.atencion_id);
                    }
                });
                
                if (allTurns.length === 0) {
                    list.innerHTML = '<div class="flex-1 flex items-center justify-center text-gray-300 font-bold italic text-sm text-center">No hay turnos activos...</div>';
                }

            } catch (err) { console.error(err); }
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true }).toUpperCase();
            document.getElementById('date').innerText = now.toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        setInterval(updateClock, 1000);
        setInterval(updateTurns, 4000);
        updateClock();
        updateTurns();
        document.addEventListener('click', () => { if(!audioEnabled) enableAudio(); });
    </script>
</body>
</html>
