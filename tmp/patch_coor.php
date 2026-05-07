<?php
$file = 'C:/Users/BREYNER/Herd/laravel_pb/resources/views/coordinador/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Update status bar color logic to include inactivo (gris oscuro, distinto de en_espera)
$old1 = "class=\"absolute top-0 left-0 w-full h-1 {{ \$a->ase_estado === 'disponible' ? 'bg-green-500' : (\$a->ase_estado === 'ocupado' ? 'bg-blue-500' : 'bg-gray-400') }}\"";
$new1 = "class=\"absolute top-0 left-0 w-full h-1 {{ \$a->ase_estado === 'disponible' ? 'bg-green-500' : (\$a->ase_estado === 'ocupado' ? 'bg-blue-500' : (\$a->ase_estado === 'inactivo' ? 'bg-gray-600' : 'bg-gray-400')) }}\"";
$content = str_replace($old1, $new1, $content);
echo strpos($content, 'bg-gray-600') !== false ? "Status bar: OK\n" : "Status bar: FAIL\n";

// 2. Update badge color logic to include inactivo
$old2 = "class=\"inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ \$a->ase_estado === 'disponible' ? 'bg-green-100 text-green-700' : (\$a->ase_estado === 'ocupado' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500') }}\"";
$new2 = "class=\"inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter {{ \$a->ase_estado === 'disponible' ? 'bg-green-100 text-green-700' : (\$a->ase_estado === 'ocupado' ? 'bg-blue-100 text-blue-700' : (\$a->ase_estado === 'inactivo' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-500')) }}\"";
$content = str_replace($old2, $new2, $content);
echo strpos($content, 'bg-gray-800 text-white') !== false ? "Badge class: OK\n" : "Badge class: FAIL\n";

// 3. Update badge text to show inactivo label
$old3 = "{{ \$a->ase_estado === 'disponible' ? 'Disponible' : (\$a->ase_estado === 'ocupado' ? 'Ocupado' : 'En Espera') }}";
$new3 = "{{ \$a->ase_estado === 'disponible' ? 'Disponible' : (\$a->ase_estado === 'ocupado' ? 'Ocupado' : (\$a->ase_estado === 'inactivo' ? 'Inactivo' : 'En Espera')) }}";
$content = str_replace($old3, $new3, $content);
echo strpos($content, "'inactivo' ? 'Inactivo'") !== false ? "Badge text: OK\n" : "Badge text: FAIL\n";

// 4. Update JS polling to handle inactivo in badge/bar
$old4 = "                        const labels = { disponible: 'Disponible', ocupado: 'Ocupado', en_espera: 'En Espera' };
                        const badgeClasses = {
                            disponible: 'bg-green-100 text-green-700',
                            ocupado:    'bg-blue-100 text-blue-700',
                            en_espera:  'bg-gray-100 text-gray-500',
                        };
                        const barClasses = {
                            disponible: 'bg-green-500',
                            ocupado:    'bg-blue-500',
                            en_espera:  'bg-gray-400',
                        };";

$new4 = "                        const labels = { disponible: 'Disponible', ocupado: 'Ocupado', en_espera: 'En Espera', inactivo: 'Inactivo' };
                        const badgeClasses = {
                            disponible: 'bg-green-100 text-green-700',
                            ocupado:    'bg-blue-100 text-blue-700',
                            en_espera:  'bg-gray-100 text-gray-500',
                            inactivo:   'bg-gray-800 text-white',
                        };
                        const barClasses = {
                            disponible: 'bg-green-500',
                            ocupado:    'bg-blue-500',
                            en_espera:  'bg-gray-400',
                            inactivo:   'bg-gray-600',
                        };";

$content = str_replace($old4, $new4, $content);
echo strpos($content, "inactivo:   'bg-gray-600'") !== false ? "JS polling: OK\n" : "JS polling: FAIL\n";

file_put_contents($file, $content);
echo "Done.\n";
