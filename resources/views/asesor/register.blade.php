<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno — Registro Asesor</title>
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
                            dark: '#0a0455' 
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
            background-image: radial-gradient(#10069f 0.5px, transparent 0.5px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-12" style="font-family: 'Montserrat', sans-serif;">

    <div class="w-full max-w-2xl">
        {{-- Logo + Título --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center bg-white rounded-2xl mb-6 shadow-xl border border-gray-100 px-6 py-2">
                <div class="text-3xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[6px] uppercase tracking-[0.2em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">
                Registro de <span class="text-[#10069f]">Asesor</span>
            </h1>
        </div>

        {{-- Tarjeta de Registro --}}
        <div class="bg-white rounded-[32px] p-8 md:p-12 shadow-2xl border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-[#10069f]"></div>
            
            <form method="POST" action="{{ route('asesor.register.post') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Información Personal --}}
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-ape-blue uppercase tracking-widest border-b pb-2">Datos Personales</h3>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Número Documento</label>
                            <input type="text" name="pers_doc" value="{{ old('pers_doc') }}" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                            @error('pers_doc') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Tipo Documento</label>
                            <select name="pers_tipodoc" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PPT">Permiso de Protección Temporal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Nombres</label>
                            <input type="text" name="pers_nombres" value="{{ old('pers_nombres') }}" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Apellidos</label>
                            <input type="text" name="pers_apellidos" value="{{ old('pers_apellidos') }}" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                        </div>
                    </div>

                    {{-- Información de Cuenta --}}
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-ape-blue uppercase tracking-widest border-b pb-2">Datos de Sistema</h3>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Correo Institucional</label>
                            <input type="email" name="ase_correo" value="{{ old('ase_correo') }}" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                            @error('ase_correo') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Tipo Perfil</label>
                                <select name="ase_tipo_asesor" id="ase_tipo_asesor" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all pointer-events-none opacity-70">
                                    <option value="G">General</option>
                                    <option value="V">Víctimas</option>
                                    <option value="E">Empresario</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Mesa #</label>
                                <input type="number" name="ase_mesa" id="ase_mesa" value="{{ old('ase_mesa', 1) }}" min="1" max="20" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                            </div>
                        </div>

                        <script>
                            const mesaInput = document.getElementById('ase_mesa');
                            const tipoSelect = document.getElementById('ase_tipo_asesor');

                            function updateTipo() {
                                const mesa = parseInt(mesaInput.value);
                                if (mesa >= 16 && mesa <= 19) {
                                    tipoSelect.value = 'V';
                                } else if (mesa === 20) {
                                    tipoSelect.value = 'E'; // Suponiendo que la 20 es Empresario, o dejar en G
                                } else {
                                    tipoSelect.value = 'G';
                                }
                            }

                            mesaInput.addEventListener('input', updateTipo);
                            updateTipo(); // Run on load
                        </script>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Contraseña</label>
                            <input type="password" name="ase_password" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">Confirmar Contraseña</label>
                            <input type="password" name="ase_password_confirmation" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm font-bold focus:border-[#10069f] outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-5 rounded-2xl shadow-xl shadow-[#10069f]/20 transition-all transform active:scale-95 uppercase tracking-widest text-xs border-b-4 border-[#0a0455]">
                        Crear Cuenta de Asesor
                    </button>
                    <a href="{{ route('asesor.login') }}" class="block text-center mt-6 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-ape-blue transition-colors">
                        ¿Ya tienes cuenta? Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
