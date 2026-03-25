<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $primaryKey = 'pers_doc';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'pers_doc',
        'pers_tipodoc',
        'pers_nombres',
        'pers_apellidos',
        'pers_telefono',
        'pers_fecha_nac',
    ];
}
