<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicador_pessoal_id')->constrained('indicador_pessoal');
            $table->foreignId('tipo_transacao_id')->constrained('tipo_transacao');
            $table->foreignId('motivo_transacao_id')->nullable()->constrained('motivo_transacao');
            $table->foreignId('banco_id')->nullable()->constrained('banco');

            $table->string('numero_transacao', 50)->unique();
            $table->string('referencia', 100)->nullable();
            $table->text('descricao');

            $table->decimal('valor', 14, 2);
            $table->string('moeda', 10)->default('BRL');

            $table->date('data_transacao');
            $table->date('data_efetivacao')->nullable();
            $table->timestamp('data_confirmacao')->nullable();

            $table->string('agencia', 20)->nullable();
            $table->string('conta', 30)->nullable();
            $table->string('tipo_conta', 30)->nullable();
            $table->string('documento_numero', 100)->nullable();
            $table->string('beneficiario', 255)->nullable();

            $table->string('situacao', 50)->default('pendente');
            $table->text('observacoes')->nullable();
            $table->boolean('is_ativo')->default(true);

            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->index('numero_transacao');
            $table->index('indicador_pessoal_id');
            $table->index('tipo_transacao_id');
            $table->index(['data_transacao', 'is_ativo']);
            $table->index('situacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacao');
    }
};
