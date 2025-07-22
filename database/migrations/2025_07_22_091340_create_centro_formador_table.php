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
        Schema::create('centro_formador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centro_id')->constrained('centros')->onDelete('cascade');
            $table->foreignId('formador_id')->constrained('formadores')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centro_formador');
    }
};
