<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "TurnoGeneral: \n";
foreach(App\Models\TurnoGeneral::take(5)->get() as $t) {
    echo "{$t->tgr_id} | {$t->tgr_numero}\n";
}
echo "TurnoPrioritario: \n";
foreach(App\Models\TurnoPrioritario::take(5)->get() as $t) {
    echo "{$t->tpri_id} | {$t->tpri_numero}\n";
}
echo "TurnoVictimas: \n";
foreach(App\Models\TurnoVictimas::take(5)->get() as $t) {
    echo "{$t->tvic_id} | {$t->tvic_numero}\n";
}
