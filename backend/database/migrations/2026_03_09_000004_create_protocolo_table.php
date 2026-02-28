<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolo', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->unique();
            $table->integer('ano');
            $table->foreignId('atendente_id')->nullable()->constrained('usuario');
            $table->foreignId('natureza_id')->nullable()->constrained('natureza');

            // Solicitante (string — indicador pessoal pode não estar cadastrado)
            $table->string('solicitante_nome', 200)->nullable();
            $table->string('solicitante_cpf_cnpj', 18)->nullable();
            $table->string('solicitante_telefone', 20)->nullable();
            $table->string('solicitante_email', 200)->nullable();

            // Dados do protocolo
            $table->string('tipo', 50)->default('NORMAL'); // NORMAL, ORCAMENTO, PROCESSO_INTERNO, EXAME_CALCULO
            $table->string('matricula', 50)->nullable();
            $table->integer('meio_solicitacao_id')->nullable(); // ID da origem (balcão, online, etc.)
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->text('observacao')->nullable();
            $table->string('motivo_cancelamento', 500)->nullable();

            // Valores financeiros
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->decimal('valor_desconto', 15, 2)->default(0);
            $table->decimal('valor_isento', 15, 2)->default(0);
            $table->decimal('valor_final', 15, 2)->default(0);
            $table->decimal('valor_pago', 15, 2)->default(0);

            // Status e controle
            $table->string('status', 50)->default('aberto'); // aberto, pago, pago_parcial, isento, cancelado
            $table->boolean('is_ativo')->default(true);

            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->index(['status', 'is_ativo']);
            $table->index('atendente_id');
            $table->index('data_cadastro');
        });
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS protocolo CASCADE');
    }
};
