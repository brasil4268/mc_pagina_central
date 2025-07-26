<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{
    use HasFactory;

    // Libera os campos para inserção em massa (mass assignment)
    protected $fillable = [
        'nome',
        'localizacao',
        'contactos',
        'email',
    ];

    // Converte automaticamente JSON para array e vice-versa
    protected $casts = [
        'contactos' => 'array',
    ];

    // Relacionamento N:N com cursos (preferencial para múltiplos centros por curso)
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'centro_curso')
        ->withPivot(['preco', 'duracao', 'data_arranque'])
        ->withTimestamps();
    }

    // Relacionamento N:N com formadores
    public function formadores()
    {
        return $this->belongsToMany(Formador::class, 'centro_formador')->withTimestamps();
    }
}
