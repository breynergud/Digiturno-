<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coordinador extends Model
{
    use HasFactory;

    protected $table = 'coordinador';
    protected $primaryKey = 'coor_id';
    public $timestamps = false;

    protected $fillable = [
        'coor_vigencia',
        'coor_correo',
        'coor_password',
        'coor_estado',
        'PERSONA_pers_doc',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'PERSONA_pers_doc', 'pers_doc');
    }
}
