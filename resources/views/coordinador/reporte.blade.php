<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal — APE Digiturno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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
</head>
<body class="bg-gray-50 min-h-screen font-sans p-6 text-gray-900">

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('coordinador.dashboard') }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center hover:bg-gray-100 transition-all">
                    <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-black text-black uppercase tracking-tight">Reporte <span class="text-[#10069f]">Semanal</span></h1>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Resumen de atenciones APE</p>
                </div>
            </div>
            <button onclick="window.print()" class="bg-[#0a0455] text-white text-[10px] font-black uppercase tracking-widest px-6 py-4 rounded-xl hover:bg-[#10069f] transition-all shadow-lg border-b-4 border-black">Imprimir Reporte</button>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-[#10069f]"></div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-8 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Asesor</th>
                            <th class="px-8 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Lun</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Mar</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Mié</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Juv</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Vie</th>
                            <th class="px-4 py-8 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Sáb</th>
                            <th class="px-8 py-8 text-[10px] font-black text-black uppercase tracking-widest text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($reporte as $reg)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-8 py-7">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400 text-xs shadow-inner">{{ substr($reg['asesor'], 0, 1) }}</div>
                                    <span class="text-sm font-black text-black">{{ $reg['asesor'] }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-7">
                                <span class="bg-blue-50 text-[#10069f] text-[9px] font-black px-3 py-1.5 rounded-full uppercase tracking-tighter">
                                    {{ $reg['tipo'] == 'G' ? 'General' : ($reg['tipo'] == 'V' ? 'Víctimas' : 'Prioritario') }}
                                </span>
                            </td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Monday'] }}</td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Tuesday'] }}</td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Wednesday'] }}</td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Thursday'] }}</td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Friday'] }}</td>
                            <td class="px-4 py-7 text-sm font-bold text-gray-500 text-center">{{ $reg['atenciones']['Saturday'] }}</td>
                            <td class="px-8 py-7 text-right">
                                <span class="bg-[#ffb500] text-[#0a0455] px-3 py-1 rounded-lg text-lg font-black shadow-sm">{{ $reg['total'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(count($reporte) == 0)
            <div class="py-24 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.3em]">No hay datos registrados para esta semana</p>
            </div>
            @endif
        </div>

        <p class="text-center text-gray-400 text-[9px] font-black uppercase tracking-[0.5em] mt-12 pb-12">
            Sistema de Gestión Institucional — APE Digiturno
        </p>
    </div>

</body>
</html>
