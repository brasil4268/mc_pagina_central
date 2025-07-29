<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'tipo', // 'loja' ou 'snack'
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
