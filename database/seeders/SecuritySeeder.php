<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asesor;
use App\Models\Coordinador;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SecuritySeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Asesor::truncate();
        Coordinador::truncate();
        Persona::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Persona 1
        Persona::create([
            'pers_doc' => '1001',
            'pers_tipodoc' => 'CC',
            'pers_nombres' => 'Asesor',
            'pers_apellidos' => 'General'
        ]);
        Asesor::create([
            'ase_correo'   => 'asesor1@sena.gov.co',
            'ase_password' => Hash::make('asesor123'),
            'ase_tipo_asesor' => 'G',
            'ase_estado' => 'disponible',
            'PERSONA_pers_doc' => '1001',
            'ase_nrocontrato' => 'CONTR-2024-001',
            'ase_vigencia' => '2026'
        ]);

        // Persona 2
        Persona::create([
            'pers_doc' => '1002',
            'pers_tipodoc' => 'CC',
            'pers_nombres' => 'Asesor',
            'pers_apellidos' => 'Víctimas'
        ]);
        Asesor::create([
            'ase_correo'   => 'asesor2@sena.gov.co',
            'ase_password' => Hash::make('asesor123'),
            'ase_tipo_asesor' => 'V',
            'ase_estado' => 'disponible',
            'PERSONA_pers_doc' => '1002',
            'ase_nrocontrato' => 'CONTR-2024-002',
            'ase_vigencia' => '2026'
        ]);

        // Persona 3: Asesor Prioritario
        Persona::create([
            'pers_doc' => '1003',
            'pers_tipodoc' => 'CC',
            'pers_nombres' => 'Asesor',
            'pers_apellidos' => 'Prioritario'
        ]);
        Asesor::create([
            'ase_correo'   => 'asesor3@sena.gov.co',
            'ase_password' => Hash::make('asesor123'),
            'ase_tipo_asesor' => 'P',
            'ase_estado' => 'disponible',
            'PERSONA_pers_doc' => '1003',
            'ase_nrocontrato' => 'CONTR-2024-003',
            'ase_vigencia' => '2026'
        ]);

        // Persona 4: Coordinador
        Persona::create([
            'pers_doc' => '2001',
            'pers_tipodoc' => 'CC',
            'pers_nombres' => 'Juan',
            'pers_apellidos' => 'Coordinador'
        ]);
        Coordinador::create([
            'coor_correo'   => 'coordinador@sena.gov.co',
            'coor_password' => Hash::make('coord123'),
            'coor_estado' => 'disponible',
            'PERSONA_pers_doc' => '2001'
        ]);


        echo "Usuarios recreados con ÉXITO.\n";
    }
}
