<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoUnificado extends Model
{
    use HasFactory;
    
    protected $table = 'turno';
    protected $primaryKey = 'tur_id';
    public $timestamps = false;
    
    protected $fillable = [
        'tur_hora_fecha',
        'tur_numero',
        'tur_tipo',
        'USUARIO_user_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'USUARIO_user_id', 'user_id');
    }

    public function atencion()
    {
        return $this->hasOne(Atencion::class, 'TURNO_tur_id', 'tur_id');
    }
}
