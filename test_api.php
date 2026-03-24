<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/api/turnos/store', 'POST', [
    'tipo_atencion' => 'general',
    'pers_tipodoc' => 'CC',
    'numero_documento' => '123456789',
    'pers_nombres' => 'John',
    'pers_apellidos' => 'Doe',
    'telefono' => '3000000000'
]);

$controller = $app->make(App\Http\Controllers\TurnoController::class);

try {
    $response = $controller->store($request);
    echo "Response:\n";
    echo $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Error:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
