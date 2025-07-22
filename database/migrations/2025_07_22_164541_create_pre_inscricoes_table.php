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
        Schema::create('pre_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade');
            $table->foreignId('centro_id')->constrained('centros')->onDelete('cascade');
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->onDelete('set null');
            $table->string('nome_completo', 100);
            $table->json('contactos');
            $table->string('email', 100)->nullable();
            $table->enum('status', ['pendente', 'confirmado', 'cancelado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pre_inscricoes');
    }
};
