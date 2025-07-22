<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->enum('dia_semana', ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']);
            $table->enum('periodo', ['manhã', 'tarde', 'noite']);
            $table->time('hora_inicio')->nullable();   // Hora de início da aula
            $table->time('hora_fim')->nullable();      // Hora de fim da aula
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }

    
};
