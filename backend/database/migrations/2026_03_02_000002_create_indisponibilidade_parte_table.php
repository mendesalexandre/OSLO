<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indisponibilidade_parte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indisponibilidade_id')->constrained('indisponibilidade')->cascadeOnDelete();
            $table->string('cpf_cnpj', 20);
            $table->string('nome_razao', 255);
            $table->timestamp('data_cadastro')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indisponibilidade_parte');
    }
};
