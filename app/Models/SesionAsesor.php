<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesionAsesor extends Model
{
    use HasFactory;

    protected $table = 'sesion_asesor';
    protected $primaryKey = 'ses_id';
    public $timestamps = false;

    protected $fillable = [
        'ses_inicio',
        'ses_fin',
        'ASESOR_ase_id',
    ];

    protected $casts = [
        'ses_inicio' => 'datetime',
        'ses_fin'    => 'datetime',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'ASESOR_ase_id', 'ase_id');
    }

    public function pausas()
    {
        return $this->hasMany(PausaAsesor::class, 'SESION_ses_id', 'ses_id');
    }
}
