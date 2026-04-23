<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno - Pantalla</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; overflow: hidden; background-color: #f3f4f6; }
        .sena-blue { color: #10069f; }
        .sena-bg-blue { background-color: #10069f; }
        .sena-orange { color: #ff6b00; }
        .sena-bg-orange { background-color: #ff6b00; }
        .glass-card { background: white; border-radius: 2.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
        
        .main-call-anim { animation: pulse-border 2s infinite; }
        @keyframes pulse-border {
            0%, 100% { border-color: #10069f; }
            50% { border-color: #ff6b00; }
        }

        #audio-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.98); z-index: 9999;
            display: flex; justify-content: center; align-items: center;
        }

        @keyframes pixel-shift {
            0%, 100% { transform: translate(0,0); }
            25% { transform: translate(0.5px, 0.5px); }
            75% { transform: translate(-0.5px, -0.5px); }
        }
        #master-container { animation: pixel-shift 60s infinite linear; }
    </style>
</head>
<body class="min-h-screen flex flex-col p-6">

    <div id="audio-overlay" onclick="enableAudio()">
        <div class="text-center">
            <div class="mb-6 sena-blue">
                <svg class="w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
            </div>
            <h2 class="text-4xl font-black mb-8 italic">SISTEMA AUDIBLE</h2>
            <button class="sena-bg-blue text-white px-12 py-5 rounded-2xl text-2xl font-bold shadow-2xl">ACTIVAR SONIDO</button>
        </div>
    </div>

    <div id="master-container" class="flex flex-col h-full flex-1">
        
        <header class="flex justify-between items-center mb-8 px-4">
            <div class="flex items-center space-x-6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/SENA_logo.svg/1200px-SENA_logo.svg.png" class="h-16 w-auto">
                <div class="border-l-2 border-gray-300 pl-6">
                    <h1 class="text-3xl font-black text-gray-800">SENA <span class="sena-orange">APE</span></h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Gestión de Turnos</p>
                </div>
            </div>
            <div class="text-right">
                <div id="clock" class="text-5xl font-black text-gray-800 tabular-nums">00:00:00</div>
                <div id="date" class="text-gray-400 font-bold uppercase text-sm">Cargando...</div>
            </div>
        </header>

        <main class="flex flex-col flex-1 gap-8 mb-8">
            <!-- FILA SUPERIOR: TURNOS VS VIDEO (IGUALES EN TAMAÑO) -->
            <div id="daily-layout" class="flex gap-8 h-3/5">
                <!-- LLAMADO PRINCIPAL -->
                <div class="w-1/2 glass-card p-10 flex flex-col items-center justify-center border-l-[20px] border-[#10069f] main-call-anim">
                    <h2 class="text-xl font-black sena-orange uppercase tracking-widest mb-4">Llamado Actual</h2>
                    <div id="main-turn-code" class="text-[11rem] leading-none font-black sena-blue tracking-tighter">---</div>
                    <div class="mt-8 sena-bg-blue text-white px-16 py-5 rounded-3xl shadow-xl">
                        <span class="text-6xl font-black" id="main-turn-mesa">MESA --</span>
                    </div>
                </div>

                <!-- VIDEO (IGUAL AL TAMAÑO DE LA IZQUIERDA) -->
                <div class="w-1/2 glass-card overflow-hidden relative sena-bg-blue">
                    <iframe 
                        class="absolute inset-0 w-full h-full" 
                        src="https://www.youtube.com/embed/nF_azk9vCwo?autoplay=1&mute=1&loop=1&playlist=nF_azk9vCwo&controls=0&modestbranding=1" 
                        frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
                    </iframe>
                    <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-black/90 to-transparent text-white">
                        <h3 class="text-2xl font-black mb-1">Registro APE</h3>
                        <p class="text-white/70 text-sm font-medium">Accede a las mejores vacantes del país.</p>
                    </div>
                </div>
            </div>

            <!-- FILA INFERIOR: TURNOS EN ESPERA -->
            <div class="flex-1 flex flex-col gap-4">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest pl-4">Próximos Turnos</h3>
                <div id="turns-list" class="grid grid-cols-3 gap-6 h-full">
                    <!-- Items dinámicos -->
                </div>
            </div>
        </main>

        <footer class="h-16 flex rounded-2xl overflow-hidden shadow-lg border border-white">
            <div class="sena-bg-blue text-white px-8 flex items-center font-black uppercase text-xs tracking-widest">Aviso APE</div>
            <div class="flex-1 sena-bg-orange flex items-center px-6 overflow-hidden">
                <marquee class="text-xl font-black text-white uppercase">
                    Bienvenido a la Agencia Pública de Empleo (APE). Recuerde que todos nuestros servicios son gratuitos. • Mantenga su documento a la mano. • APE: Transformando vidas a través del empleo digno.
                </marquee>
            </div>
        </footer>
    </div>

    <audio id="call-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

    <script>
        let announcedAttentions = new Set();
        let audioEnabled = false;

        const labels = {
            'victimas': 'Víctimas', 'especial': 'Especial', 'general': 'General', 'empresario': 'Empresas', 'prioritario': 'Prioritario'
        };

        function applyDailyLayout() {
            const day = new Date().getDate();
            const layout = document.getElementById('daily-layout');
            if (day % 2 !== 0) {
                layout.classList.add('flex-row-reverse');
                console.log("Día Impar: Layout Invertido");
            } else {
                layout.classList.remove('flex-row-reverse');
                console.log("Día Par: Layout Normal");
            }
        }

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
                
                const mainCode = document.getElementById('main-turn-code');
                const mainMesa = document.getElementById('main-turn-mesa');
                const list = document.getElementById('turns-list');

                let newestCall = data.calling[0] || null;
                if (newestCall) {
                    mainCode.innerText = newestCall.codigo_turno;
                    mainMesa.innerText = 'MESA ' + newestCall.mesa;
                    if (!announcedAttentions.has(newestCall.atencion_id) && audioEnabled) {
                        speakTurn(newestCall.codigo_turno, labels[newestCall.tipo_atencion] || newestCall.tipo_atencion, newestCall.mesa);
                        announcedAttentions.add(newestCall.atencion_id);
                    }
                } else {
                    mainCode.innerText = '---';
                    mainMesa.innerText = 'MESA --';
                }

                list.innerHTML = '';
                const waiting = data.waiting.slice(0, 3); 
                waiting.forEach(turn => {
                    const card = document.createElement('div');
                    card.className = "glass-card p-6 flex justify-between items-center border-l-8 border-gray-200";
                    card.innerHTML = `
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">${labels[turn.tipo_atencion] || turn.tipo_atencion}</p>
                            <p class="text-2xl font-black text-gray-800">Mesa ${turn.mesa}</p>
                        </div>
                        <div class="text-5xl font-black sena-blue tabular-nums">${turn.codigo_turno}</div>
                    `;
                    list.appendChild(card);
                });
                
                if (waiting.length === 0) {
                    list.innerHTML = '<div class="col-span-3 text-center py-8 text-gray-300 font-bold italic">No hay más turnos en espera...</div>';
                }

            } catch (err) { console.error(err); }
        }

        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true }).toUpperCase();
            document.getElementById('date').innerText = now.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        applyDailyLayout();
        setInterval(updateClock, 1000);
        setInterval(updateTurns, 4000);
        updateClock();
        updateTurns();
        document.addEventListener('click', () => { if(!audioEnabled) enableAudio(); });
    </script>
</body>
</html>
