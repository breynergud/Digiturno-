<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión Finalizada - APE Digiturno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <!-- Logo APE -->
        <div class="flex justify-center mb-12">
            <div class="px-6 py-2 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center">
                <div class="text-3xl font-black tracking-tighter text-[#10069f] flex flex-col items-center">
                    <span class="leading-none">APE</span>
                    <span class="text-[7px] uppercase tracking-[0.2em] font-bold text-[#ffb500]">Agencia Pública de Empleo</span>
                </div>
            </div>
        </div>

        <!-- Mensaje de Finalización -->
        <div class="flex flex-col items-center mb-8">
            <div class="bg-blue-50 p-5 rounded-3xl mb-4 border border-blue-100">
                <svg class="w-10 h-10 text-[#10069f]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-black text-[#0a0455] uppercase tracking-tighter">SU SESIÓN HA FINALIZADO</h1>
            <p class="text-[10px] text-[#ffb500] font-black uppercase tracking-[0.2em] mt-2">Seguridad APE Digiturno</p>
        </div>

        <p class="text-gray-600 mb-10 text-lg font-medium leading-relaxed">
            Inactividad detectada. Por motivos de seguridad su sesión ha sido cerrada. Por favor, ingrese de nuevo para continuar.
        </p>

        <!-- Botón de Ingreso -->
        <a href="{{ route('asesor.login') }}" class="inline-block bg-[#10069f] hover:bg-[#0a0455] text-white font-black py-5 px-20 rounded-2xl text-xl uppercase transition-all shadow-xl border-b-4 border-[#0a0455] active:scale-95">
            INGRESAR AHORA
        </a>

        <div class="mt-12 text-gray-400 text-[10px] font-bold uppercase tracking-widest">
            Agencia Pública de Empleo — Sistema de Turnos
        </div>
    </div>
</body>
</html>
