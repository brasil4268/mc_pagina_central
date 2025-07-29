<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'imagem',
        'categoria_id',
        'ativo',
        'em_destaque'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
        'em_destaque' => 'boolean'
    ];

    protected $appends = [
        'preco_formatado'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeEmDestaque($query)
    {
        return $query->where('em_destaque', true);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function getPrecoFormatadoAttribute()
    {
        if ($this->preco == 0) {
            return 'Sob Consulta';
        }
        return number_format($this->preco, 0, ',', '.') . ' Kz';
    }
}
