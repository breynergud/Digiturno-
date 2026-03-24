<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $u = App\Models\Usuario::first();
    if (!$u) { echo "No users found.\n"; exit; }
    echo "User found: {$u->user_id}\n";
    
    $t = App\Models\TurnoGeneral::create([
        'tgr_hora_fecha' => now(), 
        'tgr_numero' => 'G-999', 
        'USUARIO_user_id' => $u->user_id
    ]);
    echo "General Saved ID: " . $t->tgr_id . "\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}

try {
    $t2 = App\Models\TurnoPrioritario::create([
        'tpri_hora_fecha' => now(), 
        'tpri_numero' => 'S-999', 
        'USUARIO_user_id' => $u->user_id
    ]);
    echo "Prioritario Saved ID: " . $t2->tpri_id . "\n";
} catch (\Exception $e) {
    echo "Prioritario Error: " . $e->getMessage() . "\n";
}

try {
    $t3 = App\Models\TurnoVictimas::create([
        'tvic_hora_fecha' => now(), 
        'tvic_numero' => 'V-999', 
        'USUARIO_user_id' => $u->user_id
    ]);
    echo "Victimas Saved ID: " . $t3->tvic_id . "\n";
} catch (\Exception $e) {
    echo "Victimas Error: " . $e->getMessage() . "\n";
}
