<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_ativo')->default(true)->index();
            $table->foreignId('categoria_pai_id')->nullable()->constrained('categoria')->nullOnDelete();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('tipo', 20); // RECEITA | DESPESA
            $table->string('icone', 100)->nullable();
            $table->string('cor', 30)->nullable();
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->index('tipo');
            $table->index(['categoria_pai_id', 'is_ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
