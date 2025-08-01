<?php

/* ==============================================
   MIGRAÇÃO: TABELA CURSOS
   DATA: 2025-07-21
   DESCRIÇÃO: Cria tabela para armazenar cursos de formação
   ============================================== */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==============================================
     * MÉTODO: UP - CRIAR ESTRUTURA DA TABELA
     * ============================================== 
     */
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            // CHAVE PRIMÁRIA
            $table->id();                                           // ID auto-incremental
            
            // DADOS BÁSICOS DO CURSO
            $table->string('nome', 100);                           // Nome do curso (ex: "Informática Básica")
            $table->text('descricao')->nullable();                 // Descrição detalhada do curso
            $table->text('programa')->nullable();                  // Programa/conteúdo programático
            $table->string('area', 100);                          // Área de conhecimento (ex: "Informática")
            
            // MODALIDADE E CONFIGURAÇÕES
            $table->enum('modalidade', ['presencial', 'online']);  // Tipo de ensino
            $table->string('imagem_url')->nullable();             // URL da imagem representativa
            $table->boolean('ativo')->default(true);              // Status do curso (ativo/inativo)
            
            // TIMESTAMPS AUTOMÁTICOS
            $table->timestamps();                                  // created_at e updated_at
        });
    }

    /**
     * ==============================================
     * MÉTODO: DOWN - REVERTER MIGRAÇÃO
     * ============================================== 
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
