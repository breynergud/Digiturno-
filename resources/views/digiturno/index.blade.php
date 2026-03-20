<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA Digiturno - Inicio</title>
    <!-- Tailwind 3.4 for reliability and quick prototyping -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sena: {
                            orange: '#ff6b00',
                            green: '#39a900',
                            dark: '#1e293b',
                            light: '#f8fafc'
                        }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-sena {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 107, 0, 0.1);
        }
        .step-container {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sena-gradient {
            background: linear-gradient(135deg, #39a900 0%, #ff6b00 100%);
        }
        .success-pulse {
            animation: ring 2s infinite;
        }
        @keyframes ring {
            0% { transform: scale(0.9); opacity: 0.2; }
            50% { transform: scale(1.1); opacity: 0.1; }
            100% { transform: scale(0.9); opacity: 0.2; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4 md:p-8">

    <!-- Header Logo / Branding -->
    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-sena-green rounded-3xl mb-4 shadow-lg shadow-green-100 p-3">
             <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/SENA_logo.svg/1000px-SENA_logo.svg.png" alt="SENA" class="w-full h-auto filter brightness-0 invert">
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-sena-dark tracking-tighter">SENA <span class="text-sena-orange">DIGITURNO</span></h1>
    </div>

    <div class="w-full max-w-6xl relative">
        
        <!-- STEP 1: GRILLA DE SELECCIÓN -->
        <div id="step-1" class="step-container opacity-100 scale-100">
            <div class="text-center mb-10">
                <p class="text-slate-500 text-lg md:text-xl font-medium px-4">Bienvenido, seleccione el tipo de atención que requiere:</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-2">
                <!-- Víctimas -->
                <button type="button" onclick="selectType('Víctimas')" class="glass-sena group p-8 rounded-[2.5rem] hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center border-b-4 border-blue-500">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mb-6 text-4xl shadow-inner group-hover:bg-blue-600 group-hover:text-white transition-all transform group-hover:scale-110">🕊️</div>
                    <h3 class="text-2xl font-black text-sena-dark mb-2">Víctimas</h3>
                    <p class="text-slate-500 text-xs font-bold leading-relaxed uppercase tracking-widest opacity-60">Atención Especializada</p>
                </button>

                <!-- Especial -->
                <button type="button" onclick="selectType('Especial')" class="glass-sena group p-8 rounded-[2.5rem] hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center border-b-4 border-sena-green">
                    <div class="w-20 h-20 bg-green-50 text-sena-green rounded-3xl flex items-center justify-center mb-6 text-4xl shadow-inner group-hover:bg-sena-green group-hover:text-white transition-all transform group-hover:scale-110">🫂</div>
                    <h3 class="text-2xl font-black text-sena-dark mb-2">Especial</h3>
                    <p class="text-slate-500 text-xs font-bold leading-relaxed uppercase tracking-widest opacity-60">Adulto Mayor / Discapacitados</p>
                </button>

                <!-- General -->
                <button type="button" onclick="selectType('General')" class="glass-sena group p-8 rounded-[2.5rem] hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center border-b-4 border-slate-300">
                    <div class="w-20 h-20 bg-slate-50 text-slate-600 rounded-3xl flex items-center justify-center mb-6 text-4xl shadow-inner group-hover:bg-slate-700 group-hover:text-white transition-all transform group-hover:scale-110">👥</div>
                    <h3 class="text-2xl font-black text-sena-dark mb-2">General</h3>
                    <p class="text-slate-500 text-xs font-bold leading-relaxed uppercase tracking-widest opacity-60">Público en General</p>
                </button>

                <!-- Empresario -->
                <button type="button" onclick="selectType('Empresario')" class="bg-sena-dark group p-8 rounded-[2.5rem] hover:shadow-[0_20px_50px_rgba(255,107,0,0.2)] hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center border-b-4 border-sena-orange relative overflow-hidden">
                    <div class="absolute inset-0 bg-sena-orange opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    <div class="w-20 h-20 bg-white/10 text-sena-orange rounded-3xl flex items-center justify-center mb-6 text-4xl shadow-inner border border-white/10 group-hover:bg-sena-orange group-hover:text-white transition-all transform group-hover:scale-110">💼</div>
                    <h3 class="text-2xl font-black text-white mb-2">Empresario</h3>
                    <p class="text-sena-orange text-xs font-bold leading-relaxed uppercase tracking-widest opacity-100">Cordinador</p>
                    <div class="mt-4 px-3 py-1 bg-sena-orange/20 rounded-full text-[10px] font-black text-sena-orange uppercase tracking-tighter">Acceso Prioritario</div>
                </button>
            </div>
        </div>

        <!-- STEP 2: FORMULARIO -->
        <div id="step-2" class="step-container hidden opacity-0 translate-y-12 w-full max-w-xl mx-auto px-4">
            <div class="glass-sena rounded-[3rem] p-8 md:p-14 shadow-2xl relative overflow-hidden border-orange-500/20">
                <button onclick="goBack()" class="absolute top-8 left-8 text-slate-400 hover:text-sena-orange transition-all flex items-center text-xs font-bold uppercase tracking-widest group bg-slate-50 px-4 py-2 rounded-2xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    Atrás
                </button>

                <div class="text-center mt-12 mb-12">
                    <h2 class="text-4xl font-black text-sena-dark mb-4 tracking-tight">Registro de <span class="text-sena-green text-3xl block">Digiturno</span></h2>
                    <div class="inline-flex items-center space-x-2 bg-sena-orange/5 text-sena-orange font-black px-6 py-2 rounded-full text-sm border border-sena-orange/10 uppercase tracking-widest" id="type-badge">
                        Tipo de Turno
                    </div>
                </div>

                <form id="turno-form" class="space-y-8">
                    <input type="hidden" name="tipo_atencion" id="tipo_atencion_hidden">
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Tipo de Documento</label>
                        <select name="tipo_documento" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-6 py-5 text-sena-dark font-black focus:border-sena-orange transition-all outline-none appearance-none cursor-pointer text-lg shadow-sm">
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Número de Documento</label>
                        <input type="text" name="numero_documento" required placeholder="00-000-000" class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-6 py-5 text-sena-dark font-black focus:border-sena-orange transition-all outline-none text-lg shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Teléfono Móvil</label>
                        <input type="tel" name="telefono" required placeholder="+57 3..." class="w-full bg-slate-50 border-2 border-slate-100 rounded-3xl px-6 py-5 text-sena-dark font-black focus:border-sena-orange transition-all outline-none text-lg shadow-sm">
                    </div>

                    <button type="submit" id="submit-btn" class="w-full bg-sena-green hover:bg-[#329200] text-white font-black py-6 rounded-3xl shadow-2xl shadow-green-100 transition-all transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-lg flex justify-center items-center">
                        <span id="btn-text">Confirmar Solicitud</span>
                        <div id="btn-loader" class="hidden animate-spin h-6 w-6 border-4 border-white border-t-transparent rounded-full ml-3"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- STEP 3: RESULTADO -->
        <div id="step-3" class="step-container hidden opacity-0 translate-y-12 w-full max-w-lg mx-auto text-center px-4">
            <div class="glass-sena rounded-[4rem] p-12 md:p-16 shadow-2xl border-green-500/20">
                <div class="w-28 h-28 bg-sena-green text-white rounded-full flex items-center justify-center mx-auto mb-10 relative shadow-2xl">
                    <div class="absolute inset-0 bg-sena-green rounded-full success-pulse"></div>
                    <svg class="w-14 h-14 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <h2 class="text-3xl font-black text-sena-dark mb-2 tracking-tight">¡Solicitud Exitosa!</h2>
                <p class="text-slate-500 font-bold mb-10 uppercase text-xs tracking-[0.2em]">Su código de turno es:</p>

                <div class="bg-sena-dark rounded-3xl py-12 mb-10 border-4 border-sena-orange shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-2 h-full bg-sena-orange"></div>
                    <span id="turno-codigo" class="text-8xl font-black text-white tracking-tighter">G-001</span>
                </div>

                <p class="text-slate-400 text-sm mb-12 max-w-[250px] mx-auto leading-relaxed">Esté atento a las pantallas de la sala para su llamado.</p>

                <button onclick="location.reload()" class="w-full py-5 text-sena-green font-black hover:bg-green-50 rounded-2xl transition-all uppercase tracking-widest text-sm border-2 border-transparent hover:border-sena-green/10">
                    Finalizar Atención
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function selectType(type) {
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const badge = document.getElementById('type-badge');
            const hidden = document.getElementById('tipo_atencion_hidden');

            badge.innerText = type;
            hidden.value = type;

            step1.style.opacity = '0';
            step1.style.transform = 'scale(0.95) translateY(-20px)';
            
            setTimeout(() => {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                setTimeout(() => {
                    step2.style.opacity = '1';
                    step2.style.transform = 'translateY(0)';
                }, 50);
            }, 500);
        }

        function goBack() {
             const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');

            step2.style.opacity = '0';
            step2.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                step2.classList.add('hidden');
                step1.classList.remove('hidden');
                setTimeout(() => {
                    step1.style.opacity = '1';
                    step1.style.transform = 'scale(1) translateY(0)';
                }, 50);
            }, 500);
        }

        document.getElementById('turno-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const loader = document.getElementById('btn-loader');
            
            btn.disabled = true;
            btnText.innerText = "Tramitando...";
            loader.classList.remove('hidden');

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('{{ route("turnos.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const step2 = document.getElementById('step-2');
                    const step3 = document.getElementById('step-3');
                    const codigoDisplay = document.getElementById('turno-codigo');

                    codigoDisplay.innerText = data.codigo;

                    step2.style.opacity = '0';
                    step2.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        step2.classList.add('hidden');
                        step3.classList.remove('hidden');
                        setTimeout(() => {
                            step3.style.opacity = '1';
                            step3.style.transform = 'translateY(0)';
                        }, 50);
                    }, 500);
                }
            } catch (error) {
                console.error(error);
                alert('Ocurrió un error en el sistema.');
            } finally {
                btn.disabled = false;
                btnText.innerText = "Confirmar Solicitud";
                loader.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
