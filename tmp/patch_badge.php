<?php
$file = 'C:/Users/BREYNER/Herd/laravel_pb/resources/views/asesor/dashboard.blade.php';
$content = file_get_contents($file);

// Fix 1: Update estado-badge to add inactivo
$search1 = "en_espera'  ? 'bg-gray-400 text-white border-gray-400' :\n                                                             'bg-blue-600 text-white border-blue-600') }}\"";
$replace1 = "en_espera'  ? 'bg-gray-400 text-white border-gray-400' :\n                   (\$asesor->ase_estado === 'inactivo'   ? 'bg-gray-700 text-white border-gray-700' :\n                                                           'bg-blue-600 text-white border-blue-600')) }}\"";
$content = str_replace($search1, $replace1, $content);

// Fix 2: Update dot indicator for inactivo
$search2 = "en_espera'  ? 'bg-white' : 'bg-white') }}\"";
$replace2 = "en_espera'  ? 'bg-white' :\n                       (\$asesor->ase_estado === 'inactivo'   ? 'bg-gray-400' : 'bg-white')) }}\"";
$content = str_replace($search2, $replace2, $content);

// Fix 3: Update estado-texto to add inactivo
$search3 = "\$asesor->ase_estado === 'disponible' ? 'Disponible' : (\$asesor->ase_estado === 'en_espera' ? 'En Espera' : 'Ocupado')";
$replace3 = "\$asesor->ase_estado === 'disponible' ? 'Disponible' : (\$asesor->ase_estado === 'en_espera' ? 'En Espera' : (\$asesor->ase_estado === 'inactivo' ? 'Inactivo' : 'Ocupado'))";
$content = str_replace($search3, $replace3, $content);

file_put_contents($file, $content);
echo "Done. Applied " . (strpos($content, 'Inactivo') !== false ? 'OK' : 'FAIL') . PHP_EOL;
