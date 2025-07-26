<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'centro_id', 
        'dia_semana',
        'periodo',
        'hora_inicio', 
        'hora_fim'
    ];

    // Um horário pertence a um curso
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // Um horário pode ter muitas pré-inscrições
    public function preInscricoes()
    {
        return $this->hasMany(PreInscricao::class);
    }
}
