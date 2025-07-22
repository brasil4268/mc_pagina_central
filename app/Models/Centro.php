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

    // Um centro tem muitos cursos
    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }

    // N:N com formadores
    public function formadores()
    {
        return $this->belongsToMany(Formador::class, 'centro_formador')->withTimestamps();
    }
}
