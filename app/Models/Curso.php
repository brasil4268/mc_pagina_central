<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

        protected $fillable = [
        'centro_id',
        'nome',
        'descricao',
        'programa',
        'duracao',
        'preco',
        'area',
        'modalidade',
        'imagem_url',
        'ativo'
    ];


    // Um curso pertence a um centro
    public function centro()
    {
        return $this->belongsTo(Centro::class);
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
}
