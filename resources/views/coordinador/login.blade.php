<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENA Digiturno — Acceso Coordinador</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { sena: { green: '#39a900', dark: '#000', gray: '#707070', light: '#f4f4f4' } },
                    fontFamily: { sans: ['Montserrat', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f4f4f4;
            background-image: radial-gradient(#39a900 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="font-family: 'Montserrat', sans-serif;">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl mb-5 shadow-sm border border-gray-100 p-3">
                <img src="{{ asset('images/logosena.png') }}" alt="SENA" class="w-full h-auto">
            </div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">
                SENA <span class="text-[#39a900]">Coordinador</span>
            </h1>
            <p class="text-[#707070] text-xs font-bold uppercase tracking-[0.3em] mt-2">Módulo Administrativo</p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100">
            <h2 class="text-xl font-extrabold text-black mb-1">Acceso Principal</h2>
            <p class="text-[#707070] text-xs font-semibold mb-8">Ingrese sus credenciales de super-usuario</p>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 text-sm font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('coordinador.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold text-[#707070] uppercase tracking-widest mb-2 px-1">
                        Correo de Coordinador
                    </label>
                    <input
                        type="email"
                        name="coor_correo"
                        value="{{ old('coor_correo') }}"
                        required
                        placeholder="coordinador@sena.gov.co"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-black font-semibold focus:border-[#39a900] focus:ring-1 focus:ring-[#39a900] outline-none transition-all"
                    >
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#707070] uppercase tracking-widest mb-2 px-1">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        name="coor_password"
                        required
                        placeholder="••••••••"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-4 text-black font-semibold focus:border-[#39a900] focus:ring-1 focus:ring-[#39a900] outline-none transition-all"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-black hover:bg-[#39a900] text-white font-extrabold py-4 rounded-xl shadow-lg transition-all transform active:scale-95 uppercase tracking-widest mt-2"
                >
                    Entrar al Panel
                </button>
            </form>
        </div>

        <p class="text-center text-[#707070] text-[10px] font-bold uppercase tracking-[0.3em] mt-8">
            Coordinación — SENA Digiturno
        </p>
    </div>

</body>
</html>
