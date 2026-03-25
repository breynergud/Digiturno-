<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atencion extends Model
{
    use HasFactory;
    
    protected $table = 'atencion';
    protected $primaryKey = 'atnc_id';
    public $timestamps = false;
    
    protected $fillable = [
        'atnc_hora_inicio',
        'atnc_hora_fin',
        'atnc_tipo',
        'ASESOR_ase_id',
        'COORDINADOR_coor_id',
        'TURNO_tur_id',
    ];

    public function asesor()
    {
        return $this->belongsTo(Asesor::class, 'ASESOR_ase_id', 'ase_id');
    }

    public function turno()
    {
        return $this->belongsTo(TurnoUnificado::class, 'TURNO_tur_id', 'tur_id');
    }

    public function coordinador()
    {
        return $this->belongsTo(Coordinador::class, 'COORDINADOR_coor_id', 'coor_id');
    }
}
