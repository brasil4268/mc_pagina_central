<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formador extends Model
{
    use HasFactory;

    protected $table = 'formadores'; // Definir o nome da tabela

    protected $fillable = [
        'nome',
        'email',
        'contactos',
        'especialidade',
        'bio',
        'foto_url'
    ];

    // Converte automaticamente JSON para array e vice-versa
    protected $casts = [
        'contactos' => 'array',
    ];

    // N:N com cursos
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'curso_formador')->withTimestamps();
    }

    // N:N com centros
    public function centros()
    {
        return $this->belongsToMany(Centro::class, 'centro_formador')->withTimestamps();
    }
}
