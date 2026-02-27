<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicador_pessoal_socio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_pessoal_id')->constrained('indicador_pessoal')->cascadeOnDelete();
            $table->foreignId('socio_id')->constrained('indicador_pessoal')->restrictOnDelete();
            $table->decimal('participacao_percentual', 5, 2)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->date('data_entrada')->nullable();
            $table->date('data_saida')->nullable();
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicador_pessoal_socio');
    }
};
