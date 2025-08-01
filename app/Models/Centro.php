<?php

/* ==============================================
   MODEL: CENTRO
   DESCRIÇÃO: Representa os centros de formação onde os cursos são ministrados
   TABELA: centros
   ============================================== */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{
    use HasFactory;

    /* ==============================================
       CAMPOS PERMITIDOS PARA INSERÇÃO EM MASSA
       ============================================== */
    protected $fillable = [
        'nome',         // Nome do centro (ex: "Centro Viana")
        'localizacao',  // Endereço completo do centro
        'contactos',    // Array JSON com telefones de contacto
        'email',        // Email de contacto do centro
    ];

    /* ==============================================
       CONVERSÕES AUTOMÁTICAS DE TIPOS (CASTING)
       ============================================== */
    protected $casts = [
        'contactos' => 'array',  // Converte JSON ↔ Array automaticamente
    ];

    /* ==============================================
       RELACIONAMENTOS ELOQUENT
       ============================================== */
    
    /**
     * RELACIONAMENTO: MUITOS-PARA-MUITOS com CURSOS
     * DESCRIÇÃO: Um centro pode oferecer vários cursos, e um curso pode estar em vários centros
     * TABELA PIVOT: centro_curso
     * CAMPOS EXTRAS: preco, duracao, data_arranque (específicos por centro)
     */
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'centro_curso')
        ->withPivot(['preco', 'duracao', 'data_arranque'])
        ->withTimestamps();
    }

    /**
     * RELACIONAMENTO: MUITOS-PARA-MUITOS com FORMADORES
     * DESCRIÇÃO: Um centro pode ter vários formadores, e um formador pode trabalhar em vários centros
     * TABELA PIVOT: centro_formador
     */
    public function formadores()
    {
        return $this->belongsToMany(Formador::class, 'centro_formador')->withTimestamps();
    }
}
