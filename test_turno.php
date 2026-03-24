<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/turnos', 'POST', [
    'tipo_atencion' => 'general',
    'pers_tipodoc' => 'CC',
    'numero_documento' => '12345678',
    'telefono' => '3001234567',
]);

$controller = new App\Http\Controllers\TurnoController();
try {
    $result = $controller->store($request);
    echo "Success!\n";
    print_r($result);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
