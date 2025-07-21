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

    public function centro()
    {
        return $this->belongsTo(Centro::class);
    }
}
