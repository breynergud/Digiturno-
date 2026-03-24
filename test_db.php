<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$turnos = App\Models\Turno::orderBy('id', 'desc')->take(10)->get();
foreach ($turnos as $t) {
    echo "ID: {$t->id} | Tipo: {$t->tipo_atencion} | Codigo: {$t->codigo_turno} | Date: {$t->created_at}\n";
}
