<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE digiturno - Inicio</title>
    <!-- Tailwind 3.4 -->
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
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f4f4f4;
            background-image: radial-gradient(#10069f 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-opacity: 0.05;
        }
        .ape-card {
            background: white;
            border: 1px solid #e5e7eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ape-card:hover {
            border-color: #10069f;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(16, 6, 159, 0.1);
        }
        .step-container {
            transition: all 0.4s ease-in-out;
        }
        .success-pulse {
            animation: pulse-blue 2s infinite;
        }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(16, 6, 159, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(16, 6, 159, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 6, 159, 0); }
        }
        
        /* Teclado en pantalla */
        .key-btn {
            @apply flex items-center justify-center bg-white border border-gray-200 rounded-2xl shadow-sm hover:border-ape-blue hover:text-ape-blue active:scale-95 transition-all font-black text-xl aspect-square size-full;
        }
        
        /* Ajustes para pantallas gigantes (40"+) */
        @media (min-width: 2000px) {
            html { font-size: 24px; }
            .max-w-5xl { max-w-7xl; }
            .max-w-lg { max-w-2xl; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 md:p-8 relative">
    
    <!-- Acceso Administrativo (Solo visible en paso 1) -->
    <div id="admin-buttons" class="absolute top-4 right-4 flex gap-2 z-50">
        <a href="{{ route('turnos.tv') }}" class="bg-white/90 backdrop-blur-sm border border-gray-200 text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl hover:border-ape-blue hover:text-ape-blue transition-all shadow-sm flex items-center group">
            <svg class="w-3 h-3 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Televisor
        </a>
        <a href="{{ route('asesor.login') }}" class="bg-white/90 backdrop-blur-sm border border-gray-200 text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl hover:border-ape-blue hover:text-ape-blue transition-all shadow-sm flex items-center group">
            <svg class="w-3 h-3 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Asesor
        </a>
        <a href="{{ route('coordinador.login') }}" class="bg-ape-dark text-white text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl hover:bg-ape-blue transition-all shadow-md flex items-center group">
            <svg class="w-3 h-3 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Administrar
        </a>
    </div>

    <!-- Header Institucional -->
    <div class="mb-12 text-center">
        <div class="inline-flex items-center justify-center px-8 py-4 bg-white rounded-2xl mb-6 shadow-sm border border-gray-100">
             <div class="text-2xl font-black tracking-tighter text-ape-blue flex flex-col items-center">
                <span class="leading-none">APE</span>
                <span class="text-[8px] uppercase tracking-[0.2em] font-bold text-ape-yellow mt-1">Agencia Pública de Empleo</span>
             </div>
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-ape-dark tracking-tight uppercase">Sistema de <span class="text-ape-blue">Digiturno</span></h1>
        <div class="h-1.5 w-24 bg-gradient-to-r from-ape-blue via-ape-yellow to-ape-orange mx-auto mt-4 rounded-full"></div>
    </div>

    <div class="w-full max-w-5xl relative">
        
        <!-- PASO 1: SELECCIÓN DE TRÁMITE -->
        <div id="step-1" class="step-container opacity-100 scale-100">
            <div class="text-center mb-10">
                <p class="text-ape-gray text-lg font-medium">Bienvenido al Centro de Atención APE. Seleccione su tipo de trámite:</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Víctimas -->
                <button type="button" onclick="selectType('victimas', 'Víctimas')" class="ape-card group p-10 rounded-3xl text-center flex flex-col items-center border-t-4 border-t-transparent hover:border-t-ape-orange">
                    <h3 class="text-xl font-bold text-ape-dark mb-1">Víctimas</h3>
                    <p class="text-ape-gray text-xs font-semibold uppercase tracking-wider">Prioritario</p>
                </button>

                <!-- Especial -->
                <button type="button" onclick="selectType('especial', 'Especial')" class="ape-card group p-10 rounded-3xl text-center flex flex-col items-center border-t-4 border-t-transparent hover:border-t-ape-yellow">
                    <h3 class="text-xl font-bold text-ape-dark mb-1">Especial</h3>
                    <p class="text-ape-gray text-xs font-semibold uppercase tracking-wider">Preferencial</p>
                </button>

                <!-- General -->
                <button type="button" onclick="selectType('general', 'General')" class="ape-card group p-10 rounded-3xl text-center flex flex-col items-center border-t-4 border-t-transparent hover:border-t-ape-blue">
                    <h3 class="text-xl font-bold text-ape-dark mb-1">General</h3>
                    <p class="text-ape-gray text-xs font-semibold uppercase tracking-wider">Atención General</p>
                </button>

                <!-- Empresario -->
                <button type="button" onclick="selectType('empresario', 'Empresario')" class="ape-card group p-10 rounded-3xl text-center flex flex-col items-center border-b-4 border-b-ape-blue">
                    <h3 class="text-xl font-black text-ape-blue uppercase tracking-tight">Empresario</h3>
                </button>
            </div>
        </div>

        <!-- PASO 2: FORMULARIO DE REGISTRO -->
        <div id="step-2" class="step-container hidden opacity-0 translate-y-8 w-full max-w-lg mx-auto pb-20">
            <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-gray-100">
                <button onclick="goBack()" class="mb-8 text-ape-gray hover:text-ape-blue transition-colors flex items-center text-xs font-bold uppercase tracking-widest group">
                    <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    Regresar
                </button>

                <div class="mb-8 text-center sm:text-left">
                    <h2 class="text-3xl font-black text-ape-dark mb-2">Registro de Turno</h2>
                    <div class="inline-block bg-ape-blue text-white font-black px-6 py-1.5 rounded-full text-[10px] uppercase tracking-widest" id="type-badge">
                        Tipo de Turno
                    </div>
                </div>

                <form id="turno-form" class="space-y-8">
                    <input type="hidden" name="tipo_atencion" id="tipo_atencion_hidden">
                    
                    <div class="space-y-6">
                        <div class="w-full">
                            <label class="block text-[10px] font-black text-ape-gray uppercase tracking-widest mb-3 px-1">Número de Documento</label>
                            <input type="text" name="numero_documento" id="input_doc" required 
                                onclick="setActiveInput('input_doc')"
                                onfocus="setActiveInput('input_doc')"
                                placeholder="Toque los números" 
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-[1.5rem] px-6 py-6 text-2xl font-black text-ape-blue focus:border-ape-blue outline-none transition-all placeholder:text-gray-300 placeholder:font-bold">
                        </div>

                        <div class="w-full">
                            <label class="block text-[10px] font-black text-ape-gray uppercase tracking-widest mb-3 px-1">Teléfono (opcional)</label>
                            <input type="tel" name="telefono" id="input_tel" 
                                onclick="setActiveInput('input_tel')"
                                onfocus="setActiveInput('input_tel')"
                                placeholder="Opcional para recibir notificaciones" 
                                class="w-full bg-gray-50 border-2 border-gray-100 rounded-[1.5rem] px-6 py-6 text-xl font-black text-ape-blue focus:border-ape-blue outline-none transition-all placeholder:text-gray-300 placeholder:font-bold">
                        </div>

                        <div class="w-full">
                            <label class="block text-[10px] font-black text-ape-gray uppercase tracking-widest mb-3 px-1">Tipo Documento</label>
                            <select name="pers_tipodoc" id="pers_tipodoc" required class="w-full bg-white border-2 border-gray-100 rounded-xl px-4 py-4 text-sm font-black text-ape-dark focus:border-ape-blue outline-none transition-all">
                                <option value="CC">Cédula CC</option>
                                <option value="PPT">Permiso por Protección Temporal (PPT)</option>
                            </select>
                        </div>
                    </div>

                    <!-- TECLADO NUMÉRICO -->
                    <div class="grid grid-cols-3 gap-3 p-2 bg-gray-50 rounded-[2rem] border border-gray-100 max-w-sm mx-auto shadow-inner">
                        <button type="button" onclick="pressKey('1')" class="key-btn">1</button>
                        <button type="button" onclick="pressKey('2')" class="key-btn">2</button>
                        <button type="button" onclick="pressKey('3')" class="key-btn">3</button>
                        <button type="button" onclick="pressKey('4')" class="key-btn">4</button>
                        <button type="button" onclick="pressKey('5')" class="key-btn">5</button>
                        <button type="button" onclick="pressKey('6')" class="key-btn">6</button>
                        <button type="button" onclick="pressKey('7')" class="key-btn">7</button>
                        <button type="button" onclick="pressKey('8')" class="key-btn">8</button>
                        <button type="button" onclick="pressKey('9')" class="key-btn">9</button>
                        <button type="button" onclick="deleteKey()" class="key-btn text-red-500 bg-red-50 border-red-100">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path></svg>
                        </button>
                        <button type="button" onclick="pressKey('0')" class="key-btn">0</button>
                        <button type="button" onclick="clearKeys()" class="key-btn text-gray-400 text-sm">Borrar</button>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-6 rounded-[1.5rem] shadow-xl shadow-blue-200 transition-all transform active:scale-95 uppercase tracking-[0.2em] text-sm flex justify-center items-center mt-6 border-b-4 border-[#0a0455]">
                        <span id="btn-text">Generar mi Turno</span>
                        <div id="btn-loader" class="hidden animate-spin h-5 w-5 border-3 border-white border-t-transparent rounded-full ml-3"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- PASO 3: CONFIRMACIÓN DE TURNO -->
        <div id="step-3" class="step-container hidden opacity-0 translate-y-8 w-full max-w-md mx-auto text-center">
            <div class="bg-white rounded-[2.5rem] p-10 md:p-14 shadow-2xl border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-ape-blue"></div>
                
                <div class="w-20 h-20 bg-ape-blue text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl success-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <h2 class="text-2xl font-extrabold text-ape-dark mb-1">¡Turno Asignado!</h2>
                <p class="text-ape-gray font-semibold mb-8 uppercase text-[10px] tracking-widest">Su código de atención es:</p>

                <div class="bg-ape-dark rounded-2xl py-6 mb-8 shadow-inner group transition-all w-full max-w-[280px] mx-auto">
                    <span id="turno-codigo" class="text-6xl font-black text-ape-yellow tracking-tighter">G-001</span>
                </div>

                <div class="bg-ape-blue/5 rounded-2xl p-5 mb-10 block border border-ape-blue/10 w-full max-w-[280px] mx-auto">
                    <p class="text-ape-gray text-[10px] font-extra-bold mb-1.5 leading-tight">Diríjase a la sala y espere el llamado en pantalla</p>
                    <p class="text-ape-blue text-[9px] font-black uppercase tracking-tight">AGENCIA PÚBLICA DE EMPLEO - APE</p>
                </div>

                <button onclick="location.reload()" class="w-full py-4 text-ape-blue font-extrabold hover:bg-ape-blue/5 rounded-xl transition-all uppercase tracking-widest text-xs border-2 border-transparent hover:border-ape-blue/10">
                    Solicitar otro turno
                </button>
            </div>
        </div>
    </div>

    <!-- Pie de página Institucional -->
    <div class="mt-16 text-center space-y-4">
        <p class="text-ape-gray text-[10px] font-bold uppercase tracking-[0.3em]">Agencia Pública de Empleo - APE</p>
    </div>


    {{-- Sonido de éxito --}}
    <audio id="success-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <!-- Scripts -->
    <script>
        let focusedInput = 'input_doc';
        
        // Manejar enfoque táctil simulado
        document.getElementById('input_doc').addEventListener('focus', () => focusedInput = 'input_doc');
        document.getElementById('input_tel').addEventListener('focus', () => focusedInput = 'input_tel');
        
        // Evitar que el teclado físico nativo se abra (si es teclado táctil puro)
        // Pero mantendremos el foco para la UI
        
        function pressKey(num) {
            const input = document.getElementById(focusedInput);
            if (input.value.length < (focusedInput === 'input_doc' ? 12 : 10)) {
                input.value += num;
            }
        }
        
        function deleteKey() {
            const input = document.getElementById(focusedInput);
            input.value = input.value.slice(0, -1);
        }
        
        function clearKeys() {
             const input = document.getElementById(focusedInput);
             input.value = '';
        }

        function setActiveInput(id) {
            focusedInput = id;
        }

        function selectType(type, label) {
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const badge = document.getElementById('type-badge');
            const hidden = document.getElementById('tipo_atencion_hidden');
            const selectDoc = document.getElementById('pers_tipodoc');

            // Limpiar opciones
            selectDoc.innerHTML = '';

            // Opción Cédula siempre disponible
            const optCC = document.createElement('option');
            optCC.value = 'CC';
            optCC.textContent = 'Cédula CC';
            selectDoc.appendChild(optCC);

            if (type === 'empresario') {
                // Agregar NIT solo si es empresario
                const optNit = document.createElement('option');
                optNit.value = 'NIT';
                optNit.textContent = 'NIT de Empresa';
                selectDoc.appendChild(optNit);
                selectDoc.value = 'NIT'; // Por defecto para empresario
            } else {
                // Agregar PPT solo si NO es empresario
                const optPpt = document.createElement('option');
                optPpt.value = 'PPT';
                optPpt.textContent = 'Permiso por Protección Temporal (PPT)';
                selectDoc.appendChild(optPpt);
                selectDoc.value = 'CC'; // Por defecto para otros
            }

            badge.innerText = label;
            hidden.value = type;

            // Ocultar botones de administración
            document.getElementById('admin-buttons').classList.add('hidden');

            step1.style.opacity = '0';
            step1.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                setTimeout(() => {
                    step2.style.opacity = '1';
                    step2.style.transform = 'translateY(0)';
                    // Auto-foco al documento al entrar
                    document.getElementById('input_doc').focus();
                    focusedInput = 'input_doc';
                }, 50);
            }, 400);
        }

        function goBack() {
             const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');

            // Mostrar botones de administración
            document.getElementById('admin-buttons').classList.remove('hidden');

            step2.style.opacity = '0';
            step2.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                step2.classList.add('hidden');
                step1.classList.remove('hidden');
                setTimeout(() => {
                    step1.style.opacity = '1';
                    step1.style.transform = 'translateY(0)';
                }, 50);
            }, 400);
        }

        document.getElementById('turno-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');
            const loader = document.getElementById('btn-loader');
            
            // Validación mínima táctil
            const doc = document.getElementById('input_doc').value;
            if (doc.length < 5) {
                alert("Por favor ingrese un número de documento válido.");
                return;
            }

            btn.disabled = true;
            btnText.innerText = "PROCESANDO...";
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

                if (!response.ok) {
                    let msg = 'Error en el servidor';
                    try {
                        const ct = response.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            const errorData = await response.json();
                            msg = errorData.message || (errorData.errors ? Object.values(errorData.errors).flat().join('\n') : msg);
                        }
                    } catch (_) {}
                    throw new Error(msg);
                }

                const data = await response.json();

                if (data.success) {
                    // Reproducir sonido para todos los tipos
                    document.getElementById('success-sound').play();

                    const step2 = document.getElementById('step-2');
                    const step3 = document.getElementById('step-3');
                    const codigoDisplay = document.getElementById('turno-codigo');

                    codigoDisplay.innerText = data.codigo;

                    step2.style.opacity = '0';
                    step2.style.transform = 'scale(0.98)';
                    
                    setTimeout(() => {
                        step2.classList.add('hidden');
                        step3.classList.remove('hidden');
                        setTimeout(() => {
                            step3.style.opacity = '1';
                            step3.style.transform = 'translateY(0)';
                            
                            // Recargar automáticamente después de 15 segundos para limpiar el kiosco
                            setTimeout(() => {
                                if (!step3.classList.contains('hidden')) location.reload();
                            }, 15000);
                        }, 50);
                    }, 400);
                }
            } catch (error) {
                console.error(error);
                alert(error.message);
            } finally {
                btn.disabled = false;
                btnText.innerText = "GENERAR MI TURNO";
                loader.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
