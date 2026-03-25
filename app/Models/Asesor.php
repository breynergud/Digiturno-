<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asesor extends Model
{
    use HasFactory;

    protected $table = 'asesor';
    protected $primaryKey = 'ase_id';
    public $timestamps = false;

    protected $fillable = [
        'ase_nrocontrato',
        'ase_tipo_asesor',
        'PERSONA_pers_doc',
        'ase_vigencia',
        'ase_password',
        'ase_correo',
        'ase_estado',
        'ase_turno_actual_id',
        'ase_turno_actual_tipo',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'PERSONA_pers_doc', 'pers_doc');
    }
}
