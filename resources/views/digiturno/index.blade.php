<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA Digiturno - Inicio</title>
    <!-- Tailwind 3.4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sena: {
                            green: '#39a900',
                            dark: '#000000',
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
            background-image: radial-gradient(#39a900 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-opacity: 0.05;
        }
        .sena-card {
            background: white;
            border: 1px solid #e5e7eb;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sena-card:hover {
            border-color: #39a900;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(57, 169, 0, 0.1);
        }
        .step-container {
            transition: all 0.4s ease-in-out;
        }
        .success-pulse {
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(57, 169, 0, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(57, 169, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(57, 169, 0, 0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 md:p-8 relative">

    <!-- Acceso Administrativo -->
    <div class="absolute top-4 right-4 flex gap-2 z-50">
        <a href="{{ route('asesor.login') }}" class="bg-white/90 backdrop-blur-sm border border-gray-200 text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl hover:border-sena-green hover:text-sena-green transition-all shadow-sm flex items-center group">
            <svg class="w-3 h-3 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Asesor
        </a>
        <a href="{{ route('coordinador.login') }}" class="bg-black text-white text-[10px] font-black uppercase tracking-widest px-5 py-2.5 rounded-xl hover:bg-sena-green transition-all shadow-md flex items-center group">
            <svg class="w-3 h-3 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Administrar
        </a>
    </div>

    <!-- Header Institucional -->
    <div class="mb-12 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-2xl mb-6 shadow-sm border border-gray-100 p-4">
             <img src="{{ asset('images/logosena.png') }}" alt="SENA" class="w-full h-auto">
        </div>
        <h1 class="text-3xl md:text-4xl font-black text-sena-dark tracking-tight uppercase">Sistema de <span class="text-sena-green">Digiturno</span></h1>
        <div class="h-1 w-20 bg-sena-green mx-auto mt-4 rounded-full"></div>
    </div>

    <div class="w-full max-w-5xl relative">
        
        <!-- PASO 1: SELECCIÓN DE TRÁMITE -->
        <div id="step-1" class="step-container opacity-100 scale-100">
            <div class="text-center mb-10">
                <p class="text-sena-gray text-lg font-medium">Bienvenido al Centro de Atención. Seleccione su tipo de trámite:</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Víctimas -->
                <button type="button" onclick="selectType('victimas', 'Víctimas')" class="sena-card group p-10 rounded-3xl text-center flex flex-col items-center">
                    <h3 class="text-xl font-bold text-sena-dark mb-1">Víctimas</h3>
                    <p class="text-sena-gray text-xs font-semibold uppercase tracking-wider">Prioritario</p>
                </button>

                <!-- Especial -->
                <button type="button" onclick="selectType('especial', 'Especial')" class="sena-card group p-10 rounded-3xl text-center flex flex-col items-center">
                    <h3 class="text-xl font-bold text-sena-dark mb-1">Especial</h3>
                    <p class="text-sena-gray text-xs font-semibold uppercase tracking-wider">Preferencial</p>
                </button>

                <!-- General -->
                <button type="button" onclick="selectType('general', 'General')" class="sena-card group p-10 rounded-3xl text-center flex flex-col items-center">
                    <h3 class="text-xl font-bold text-sena-dark mb-1">General</h3>
                    <p class="text-sena-gray text-xs font-semibold uppercase tracking-wider">Atención General</p>
                </button>

                <!-- Empresario -->
                <button type="button" onclick="selectType('empresario', 'Empresario')" class="sena-card group p-10 rounded-3xl text-center flex flex-col items-center border-b-4 border-b-sena-green">
                    <h3 class="text-xl font-bold text-sena-dark mb-1">Empresario</h3>
                    <p class="text-sena-green text-xs font-bold uppercase tracking-wider">Coordinación</p>
                </button>
            </div>
        </div>

        <!-- PASO 2: FORMULARIO DE REGISTRO -->
        <div id="step-2" class="step-container hidden opacity-0 translate-y-8 w-full max-w-lg mx-auto">
            <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl border border-gray-100">
                <button onclick="goBack()" class="mb-8 text-sena-gray hover:text-sena-green transition-colors flex items-center text-xs font-bold uppercase tracking-widest group">
                    <svg class="w-4 h-4 mr-1 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    Regresar
                </button>

                <div class="mb-10">
                    <h2 class="text-2xl font-extrabold text-sena-dark mb-2">Información del Solicitante</h2>
                    <div class="inline-block bg-sena-light text-sena-green font-bold px-4 py-1.5 rounded-full text-xs uppercase tracking-wider border border-green-100" id="type-badge">
                        Tipo de Turno
                    </div>
                </div>

                <form id="turno-form" class="space-y-6">
                    <input type="hidden" name="tipo_atencion" id="tipo_atencion_hidden">
                    
                    <div>
                        <label class="block text-[11px] font-bold text-sena-gray uppercase tracking-widest mb-2 px-1">Tipo de Documento</label>
                        <select name="pers_tipodoc" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-sena-dark font-semibold focus:border-sena-green focus:ring-1 focus:ring-sena-green outline-none transition-all appearance-none cursor-pointer">
                            <option value="CC">Cédula de Ciudadanía</option>
                            <option value="TI">Tarjeta de Identidad</option>
                            <option value="CE">Cédula de Extranjería</option>
                            <option value="PEP">PEP</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-sena-gray uppercase tracking-widest mb-2 px-1">Número de Documento</label>
                        <input type="text" name="numero_documento" required placeholder="Ingrese su documento" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-sena-dark font-semibold focus:border-sena-green focus:ring-1 focus:ring-sena-green outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-sena-gray uppercase tracking-widest mb-2 px-1">Teléfono de Contacto</label>
                        <input type="tel" name="telefono" required placeholder="Ej: 310 000 0000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-sena-dark font-semibold focus:border-sena-green focus:ring-1 focus:ring-sena-green outline-none transition-all">
                    </div>

                    <button type="submit" id="submit-btn" class="w-full bg-sena-green hover:bg-[#2d8500] text-white font-extrabold py-5 rounded-xl shadow-lg shadow-green-100 transition-all transform active:scale-95 uppercase tracking-widest flex justify-center items-center mt-4">
                        <span id="btn-text">Generar mi Turno</span>
                        <div id="btn-loader" class="hidden animate-spin h-5 w-5 border-3 border-white border-t-transparent rounded-full ml-3"></div>
                    </button>
                </form>
            </div>
        </div>

        <!-- PASO 3: CONFIRMACIÓN DE TURNO -->
        <div id="step-3" class="step-container hidden opacity-0 translate-y-8 w-full max-w-md mx-auto text-center">
            <div class="bg-white rounded-[2.5rem] p-10 md:p-14 shadow-2xl border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-sena-green"></div>
                
                <div class="w-20 h-20 bg-sena-green text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl success-pulse">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <h2 class="text-2xl font-extrabold text-sena-dark mb-1">¡Turno Asignado!</h2>
                <p class="text-sena-gray font-semibold mb-8 uppercase text-[10px] tracking-widest">Su código de atención es:</p>

                <div class="bg-sena-dark rounded-2xl py-10 mb-8 shadow-inner group transition-all">
                    <span id="turno-codigo" class="text-7xl font-black text-sena-green tracking-tighter">G-001</span>
                </div>

                <div class="bg-sena-light rounded-xl p-4 mb-10 inline-block border border-green-50">
                    <p class="text-sena-gray text-xs font-bold mb-1">Diríjase a la sala y espere el llamado en pantalla</p>
                    <p class="text-sena-green text-[10px] font-black uppercase tracking-tighter">CENTRO DE ATENCIÓN SENA</p>
                </div>

                <button onclick="location.reload()" class="w-full py-4 text-sena-green font-extrabold hover:bg-sena-light rounded-xl transition-all uppercase tracking-widest text-xs border-2 border-transparent hover:border-green-100">
                    Solicitar otro turno
                </button>
            </div>
        </div>
    </div>

    <!-- Pie de página Institucional -->
    <div class="mt-16 text-center space-y-4">
        <p class="text-sena-gray text-[10px] font-bold uppercase tracking-[0.3em]">Servicio Nacional de Aprendizaje - SENA</p>
    </div>

    <!-- Scripts -->
    <script>
        function selectType(type, label) {
            const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');
            const badge = document.getElementById('type-badge');
            const hidden = document.getElementById('tipo_atencion_hidden');

            badge.innerText = label;
            hidden.value = type;

            step1.style.opacity = '0';
            step1.style.transform = 'translateY(-10px)';
            
            setTimeout(() => {
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                setTimeout(() => {
                    step2.style.opacity = '1';
                    step2.style.transform = 'translateY(0)';
                }, 50);
            }, 400);
        }

        function goBack() {
             const step1 = document.getElementById('step-1');
            const step2 = document.getElementById('step-2');

            step2.style.opacity = '0';
            step2.style.transform = 'translateY(10px)';
            
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
            
            btn.disabled = true;
            btnText.innerText = "Procesando...";
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
                    step2.style.transform = 'scale(0.98)';
                    
                    setTimeout(() => {
                        step2.classList.add('hidden');
                        step3.classList.remove('hidden');
                        setTimeout(() => {
                            step3.style.opacity = '1';
                            step3.style.transform = 'translateY(0)';
                        }, 50);
                    }, 400);
                }
            } catch (error) {
                console.error(error);
                alert('Ocurrió un error al procesar el turno.');
            } finally {
                btn.disabled = false;
                btnText.innerText = "Generar mi Turno";
                loader.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
