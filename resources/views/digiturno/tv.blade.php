<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA Digiturno - Pantalla</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; overflow: hidden; }
        .tv-card {
            background: rgba(255, 255, 255, 0.95);
            border-left: 15px solid #ff6b00;
        }
        .animate-blink {
            animation: blink 1s infinite;
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex flex-col p-8 lg:p-12">

    <!-- Header TV -->
    <div class="flex justify-between items-center mb-12 border-b-2 border-white/10 pb-8">
        <div class="flex items-center space-x-6">
            <div class="w-16 h-16 bg-[#39a900] rounded-2xl p-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/SENA_logo.svg/1000px-SENA_logo.svg.png" alt="SENA" class="w-full filter brightness-0 invert">
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tighter">SENA <span class="text-[#ff6b00]">DIGITURNO</span></h1>
                <p class="text-slate-400 font-bold uppercase tracking-[0.3em] text-sm">Sala de Espera</p>
            </div>
        </div>
        <div class="text-right">
            <div id="clock" class="text-5xl font-black text-white">00:00:00</div>
            <div id="date" class="text-slate-400 font-bold uppercase tracking-widest text-lg">Cargando...</div>
        </div>
    </div>

    <!-- Main TV Content -->
    <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-10">
        
        <!-- Left Column: Large Call (Last One) -->
        <div class="bg-[#ff6b00] rounded-[3rem] p-12 flex flex-col justify-center items-center text-center shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-4 bg-white/20"></div>
            <h2 class="text-3xl font-black text-white/80 uppercase tracking-widest mb-6">Llamado Actual</h2>
            
            <div id="main-turn-code" class="text-[18rem] md:text-[22rem] leading-none font-black text-white tracking-tighter drop-shadow-2xl animate-blink">
                ---
            </div>
            
            <div class="mt-8 bg-white text-[#ff6b00] px-12 py-6 rounded-[2rem] shadow-xl">
                <p class="text-2xl font-black uppercase tracking-widest mb-1">Diríjase a:</p>
                <div id="main-turn-mesa" class="text-7xl font-black uppercase tracking-tighter">
                    MESA --
                </div>
            </div>
            
            <audio id="call-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>
        </div>

        <!-- Right Column: Grid Categories -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Dynamically populated -->
            <div id="category-container" class="grid grid-cols-1 gap-6 h-full">
                <!-- Template example -->
                <div class="tv-card rounded-[2.5rem] p-8 flex justify-between items-center text-slate-900 shadow-xl overflow-hidden">
                    <div>
                        <h3 class="text-4xl font-black uppercase tracking-tight text-slate-400 mb-2">Cargando</h3>
                        <p class="text-xl font-bold text-slate-400">Espere un momento...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer TV -->
    <div class="mt-12 bg-sena-dark/50 p-6 rounded-[2rem] flex justify-between items-center border border-white/5">
        <marquee class="text-2xl font-bold text-slate-300">Bienvenido al Centro de Atención SENA. Por favor, tenga su documento a la mano. Los empresarios tienen prioridad alta y serán atendidos por el Coordinador de turno.</marquee>
    </div>

    <script>
        let lastCodes = {};
        let firstLoad = true;

        async function updateTurns() {
            try {
                const response = await fetch('{{ route("turnos.api.ultimo") }}');
                const turns = await response.json();
                
                const container = document.getElementById('category-container');
                container.innerHTML = '';
                
                let newestTurn = null;

                turns.forEach(turn => {
                    if (turn) {
                        // Detect global newest
                        if (!newestTurn || new Date(turn.created_at) > new Date(newestTurn.created_at)) {
                            newestTurn = turn;
                        }

                        // Play sound if code changed
                        if (!firstLoad && lastCodes[turn.tipo_atencion] !== turn.codigo_turno) {
                             if (new Date(turn.created_at) > new Date(Date.now() - 10000)) { // Only sound if created in last 10s
                                document.getElementById('call-sound').play();
                             }
                        }
                        lastCodes[turn.tipo_atencion] = turn.codigo_turno;

                        const card = document.createElement('div');
                        card.className = "tv-card rounded-[2.5rem] p-8 flex justify-between items-center text-slate-900 shadow-xl";
                        card.style.borderLeftColor = getCategoryColor(turn.tipo_atencion);
                        
                        card.innerHTML = `
                            <div>
                                <h3 class="text-3xl font-black uppercase tracking-tight text-slate-500 mb-1">${turn.tipo_atencion}</h3>
                                <p class="text-xl font-bold text-slate-400 uppercase tracking-widest">Mesa ${turn.mesa}</p>
                            </div>
                            <div class="text-7xl font-black text-slate-900">${turn.codigo_turno}</div>
                        `;
                        container.appendChild(card);
                    }
                });

                if (newestTurn) {
                    document.getElementById('main-turn-code').innerText = newestTurn.codigo_turno;
                    document.getElementById('main-turn-mesa').innerText = 'MESA ' + newestTurn.mesa;
                }

                firstLoad = false;
            } catch (err) {
                console.error("Error fetching turns:", err);
            }
        }

        function getCategoryColor(type) {
            const colors = {
                'Víctimas': '#3b82f6',
                'Especial': '#39a900',
                'Empresario': '#ff6b00',
                'General': '#64748b'
            };
            return colors[type] || '#ff6b00';
        }

        // Clock logic
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString('es-ES', { hour12: false });
            document.getElementById('date').innerText = now.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }

        setInterval(updateClock, 1000);
        setInterval(updateTurns, 3000); // Poll every 3 seconds
        updateClock();
        updateTurns();
    </script>
</body>
</html>
