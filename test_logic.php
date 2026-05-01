<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Turno;

try {
    $t = Turno::create([
        'tipo_atencion' => 'General',
        'tipo_documento' => 'Cédula',
        'numero_documento' => '1098765432',
        'codigo_turno' => 'G-001',
        'mesa' => 1
    ]);
    echo "SUCCESS: Turno creado: " . $t->codigo_turno . "\n";
    $t->delete(); // Clean up
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
