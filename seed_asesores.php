<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Crear persona asesor de prueba
$persona = App\Models\Persona::updateOrCreate(
    ['pers_doc' => 100000001],
    [
        'pers_tipodoc'    => 'CC',
        'pers_nombres'    => 'Asesor',
        'pers_apellidos'  => 'Prueba Victimas',
        'pers_telefono'   => 3000000001,
    ]
);

$asesorV = App\Models\Asesor::updateOrCreate(
    ['PERSONA_pers_doc' => 100000001],
    [
        'ase_nrocontrato'  => '001-V',
        'ase_tipo_asesor'  => 'V',
        'ase_vigencia'     => '2026',
        'ase_password'     => 'asesor123',
        'ase_correo'       => 'victimas@sena.gov.co',
        'ase_estado'       => 'disponible',
    ]
);
echo "Asesor Victimas ID: {$asesorV->ase_id} - victimas@sena.gov.co / asesor123\n";

// Asesor General
$personaG = App\Models\Persona::updateOrCreate(
    ['pers_doc' => 100000002],
    [
        'pers_tipodoc'    => 'CC',
        'pers_nombres'    => 'Asesor',
        'pers_apellidos'  => 'Prueba General',
        'pers_telefono'   => 3000000002,
    ]
);
$asesorG = App\Models\Asesor::updateOrCreate(
    ['PERSONA_pers_doc' => 100000002],
    [
        'ase_nrocontrato'  => '002-G',
        'ase_tipo_asesor'  => 'G',
        'ase_vigencia'     => '2026',
        'ase_password'     => 'asesor123',
        'ase_correo'       => 'general@sena.gov.co',
        'ase_estado'       => 'disponible',
    ]
);
echo "Asesor General ID: {$asesorG->ase_id} - general@sena.gov.co / asesor123\n";

// Asesor Prioritario
$personaP = App\Models\Persona::updateOrCreate(
    ['pers_doc' => 100000003],
    [
        'pers_tipodoc'    => 'CC',
        'pers_nombres'    => 'Asesor',
        'pers_apellidos'  => 'Prueba Prioritario',
        'pers_telefono'   => 3000000003,
    ]
);
$asesorP = App\Models\Asesor::updateOrCreate(
    ['PERSONA_pers_doc' => 100000003],
    [
        'ase_nrocontrato'  => '003-P',
        'ase_tipo_asesor'  => 'P',
        'ase_vigencia'     => '2026',
        'ase_password'     => 'asesor123',
        'ase_correo'       => 'prioritario@sena.gov.co',
        'ase_estado'       => 'disponible',
    ]
);
echo "Asesor Prioritario ID: {$asesorP->ase_id} - prioritario@sena.gov.co / asesor123\n";
echo "\nDone!\n";
