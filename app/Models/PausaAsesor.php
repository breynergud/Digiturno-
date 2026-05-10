<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PausaAsesor extends Model
{
    use HasFactory;

    protected $table = 'pausa_asesor';
    protected $primaryKey = 'pau_id';
    public $timestamps = false;

    protected $fillable = [
        'pau_inicio',
        'pau_fin',
        'SESION_ses_id',
        'pau_motivo',
    ];

    protected $casts = [
        'pau_inicio' => 'datetime',
        'pau_fin' => 'datetime',
    ];

    public function sesion()
    {
        return $this->belongsTo(SesionAsesor::class, 'SESION_ses_id', 'ses_id');
    }
}
