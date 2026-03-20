<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $fillable = [
        'tipo_atencion',
        'tipo_documento',
        'numero_documento',
        'telefono',
        'codigo_turno',
        'mesa'
    ];
}
