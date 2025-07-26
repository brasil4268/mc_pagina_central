<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;



    protected $fillable = [
        'nome',
        'descricao',
        'programa',
        'area',
        'modalidade',
        'imagem_url',
        'ativo'
    ];

    // N:N com centros (caso o curso seja ministrado em vários centros)
    public function centros()
    {
        return $this->belongsToMany(Centro::class, 'centro_curso')
        ->withPivot(['preco', 'duracao', 'data_arranque'])
        ->withTimestamps();
    }

    // Um curso tem muitos horários
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // N:N com formadores
    public function formadores()
    {
        return $this->belongsToMany(Formador::class, 'curso_formador')->withTimestamps();
    }


    // Um curso pode ter muitas pré-inscrições
    public function preInscricoes()
    {
        return $this->hasMany(PreInscricao::class);
    }
}
