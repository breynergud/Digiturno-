<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear 3 personas para los asesores
        $personas = [
            ['pers_doc' => 100000001, 'pers_tipodoc' => 'CC', 'pers_nombres' => 'Asesor', 'pers_apellidos' => 'Víctimas', 'pers_telefono' => 3000000001],
            ['pers_doc' => 100000002, 'pers_tipodoc' => 'CC', 'pers_nombres' => 'Asesor', 'pers_apellidos' => 'General', 'pers_telefono' => 3000000002],
            ['pers_doc' => 100000003, 'pers_tipodoc' => 'CC', 'pers_nombres' => 'Asesor', 'pers_apellidos' => 'Prioritario', 'pers_telefono' => 3000000003],
        ];

        foreach ($personas as $p) {
            \App\Models\Persona::firstOrCreate(['pers_doc' => $p['pers_doc']], $p);
        }

        // Crear los 3 tipos de asesores
        $asesores = [
            ['ase_nrocontrato' => '001', 'ase_tipo_asesor' => 'V', 'PERSONA_pers_doc' => 100000001, 'ase_vigencia' => '2026', 'ase_password' => 'asesor123', 'ase_correo' => 'victimas@sena.gov.co'],
            ['ase_nrocontrato' => '002', 'ase_tipo_asesor' => 'G', 'PERSONA_pers_doc' => 100000002, 'ase_vigencia' => '2026', 'ase_password' => 'asesor123', 'ase_correo' => 'general@sena.gov.co'],
            ['ase_nrocontrato' => '003', 'ase_tipo_asesor' => 'P', 'PERSONA_pers_doc' => 100000003, 'ase_vigencia' => '2026', 'ase_password' => 'asesor123', 'ase_correo' => 'prioritario@sena.gov.co'],
        ];

        foreach ($asesores as $a) {
            \App\Models\Asesor::firstOrCreate(['ase_correo' => $a['ase_correo']], $a);
        }
    }
}
