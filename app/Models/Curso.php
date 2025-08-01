<?php

/* ==============================================
   MODEL: CURSO
   DESCRIÇÃO: Representa os cursos oferecidos pelo centro de formação
   TABELA: cursos
   ============================================== */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    /* ==============================================
       CAMPOS PERMITIDOS PARA INSERÇÃO EM MASSA
       ============================================== */
    protected $fillable = [
        'nome',         // Nome do curso (ex: "Informática Básica")
        'descricao',    // Descrição detalhada do curso
        'programa',     // Programa/conteúdo do curso
        'area',         // Área de conhecimento (ex: "Informática", "Gestão")
        'modalidade',   // "presencial" ou "online"
        'imagem_url',   // URL da imagem representativa do curso
        'ativo'         // Boolean - se o curso está ativo ou não
    ];

    /* ==============================================
       RELACIONAMENTOS ELOQUENT
       ============================================== */
    
    /**
     * RELACIONAMENTO: MUITOS-PARA-MUITOS com CENTROS
     * DESCRIÇÃO: Um curso pode ser ministrado em vários centros
     * TABELA PIVOT: centro_curso
     * CAMPOS EXTRAS: preco, duracao, data_arranque (específicos por centro)
     */
    public function centros()
    {
        return $this->belongsToMany(Centro::class, 'centro_curso')
        ->withPivot(['preco', 'duracao', 'data_arranque'])
        ->withTimestamps();
    }

    /**
     * RELACIONAMENTO: UM-PARA-MUITOS com HORÁRIOS
     * DESCRIÇÃO: Um curso tem vários horários de turmas
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * RELACIONAMENTO: MUITOS-PARA-MUITOS com FORMADORES
     * DESCRIÇÃO: Um curso pode ter vários formadores e vice-versa
     * TABELA PIVOT: curso_formador
     */
    public function formadores()
    {
        return $this->belongsToMany(Formador::class, 'curso_formador')->withTimestamps();
    }

    /**
     * RELACIONAMENTO: UM-PARA-MUITOS com PRÉ-INSCRIÇÕES
     * DESCRIÇÃO: Um curso pode ter várias pré-inscrições de interessados
     */
    public function preInscricoes()
    {
        return $this->hasMany(PreInscricao::class);
    }
}
