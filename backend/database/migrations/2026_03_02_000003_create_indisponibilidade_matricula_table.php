<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indisponibilidade_matricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indisponibilidade_parte_id')->constrained('indisponibilidade_parte')->cascadeOnDelete();
            $table->string('matricula', 100);
            $table->timestamp('data_cadastro')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indisponibilidade_matricula');
    }
};
