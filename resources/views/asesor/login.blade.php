<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APE Digiturno — Acceso Asesor</title>
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
<body class="min-h-screen flex items-center justify-center p-4" style="font-family: 'Montserrat', sans-serif;">

    <div class="w-full max-w-md">
        {{-- Logo + Título --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center bg-white rounded-2xl mb-6 shadow-xl border border-gray-100 px-6 py-2">
                <div class="text-3xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[6px] uppercase tracking-[0.2em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                </div>
            </div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">
                APE <span class="text-[#10069f]">Digiturno</span>
            </h1>
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em] mt-3">Portal de Asesores</p>
        </div>

        {{-- Tarjeta de Login --}}
        <div class="bg-white rounded-[32px] p-10 shadow-2xl border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-[#10069f]"></div>
            
            <h2 class="text-2xl font-black text-black mb-1">Bienvenido</h2>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-10">Ingrese sus credenciales APE</p>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl px-4 py-3 mb-8 text-sm font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('asesor.login.post') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">
                        Correo Institucional
                    </label>
                    <input
                        type="email"
                        name="ase_correo"
                        value="{{ old('ase_correo') }}"
                        required
                        placeholder="ejemplo@ape.gov.co"
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-black font-bold focus:border-[#10069f] focus:ring-4 focus:ring-[#10069f]/5 outline-none transition-all placeholder:text-gray-300"
                    >
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 px-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        name="ase_password"
                        required
                        placeholder="••••••••"
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-5 py-4 text-black font-bold focus:border-[#10069f] focus:ring-4 focus:ring-[#10069f]/5 outline-none transition-all placeholder:text-gray-300"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-5 rounded-2xl shadow-xl shadow-[#10069f]/20 transition-all transform active:scale-95 uppercase tracking-widest text-xs mt-4 border-b-4 border-[#0a0455]"
                >
                    Entrar al Sistema
                </button>
            </form>
        </div>

        <p class="text-center text-gray-400 text-[9px] font-black uppercase tracking-[0.5em] mt-10">
            Agencia Pública de Empleo — APE
        </p>
    </div>

</body>
</html>
