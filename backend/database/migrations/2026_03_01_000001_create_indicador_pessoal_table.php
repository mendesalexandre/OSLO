<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicador_pessoal', function (Blueprint $table) {
            $table->id();

            // Controle de versão
            $table->string('cpf_cnpj', 20)->index();
            $table->integer('versao')->default(1);
            $table->boolean('is_atual')->default(true)->index();
            $table->text('motivo_versao')->nullable();
            $table->timestamp('data_versao')->useCurrent();

            // Identificação
            $table->char('tipo_pessoa', 1); // 'F' ou 'J'
            $table->string('ficha', 20)->nullable();
            $table->string('nome', 255);
            $table->string('nome_fantasia', 255)->nullable(); // PJ

            // Pessoa Física
            $table->string('rg', 30)->nullable();
            $table->string('orgao_expedidor', 20)->nullable();
            $table->date('data_expedicao_rg')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->date('data_obito')->nullable();
            $table->char('sexo', 1)->nullable(); // 'M', 'F', 'O'
            $table->string('nome_pai', 255)->nullable();
            $table->string('nome_mae', 255)->nullable();

            // Estado civil (PF)
            $table->foreignId('estado_civil_id')->nullable()->constrained('estado_civil')->nullOnDelete();
            $table->foreignId('regime_bem_id')->nullable()->constrained('regime_bem')->nullOnDelete();
            $table->date('data_casamento')->nullable();
            $table->boolean('anterior_lei_6515')->nullable();
            $table->foreignId('conjuge_id')->nullable()->constrained('indicador_pessoal')->nullOnDelete();

            // Capacidade civil
            $table->foreignId('capacidade_civil_id')->nullable()->constrained('capacidade_civil')->nullOnDelete();
            $table->string('representante_legal', 255)->nullable();

            // Nacionalidade / Profissão
            $table->foreignId('nacionalidade_id')->nullable()->constrained('nacionalidade')->nullOnDelete();
            $table->string('naturalidade', 255)->nullable();
            $table->foreignId('profissao_id')->nullable()->constrained('profissao')->nullOnDelete();

            // Pessoa Jurídica
            $table->date('data_abertura')->nullable();
            $table->date('data_encerramento')->nullable();
            $table->string('sede', 255)->nullable();
            $table->text('objeto_social')->nullable();
            $table->foreignId('tipo_empresa_id')->nullable()->constrained('tipo_empresa')->nullOnDelete();
            $table->foreignId('porte_empresa_id')->nullable()->constrained('porte_empresa')->nullOnDelete();
            $table->string('inscricao_estadual', 50)->nullable();
            $table->string('inscricao_municipal', 50)->nullable();

            // COAF / Compliance
            $table->boolean('pessoa_politicamente_exposta')->default(false);
            $table->boolean('servidor_publico')->default(false);
            $table->string('cargo_funcao', 255)->nullable();
            $table->string('orgao_entidade', 255)->nullable();

            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('logradouro', 255)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->char('uf', 2)->nullable();
            $table->string('pais', 100)->default('Brasil');

            // Controle
            $table->text('observacoes')->nullable();
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            // Índices
            $table->index('nome');
            $table->unique(['cpf_cnpj', 'versao']);
            $table->index(['cpf_cnpj', 'is_atual']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicador_pessoal');
    }
};
