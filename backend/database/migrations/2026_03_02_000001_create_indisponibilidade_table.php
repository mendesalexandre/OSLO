<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indisponibilidade', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_ativo')->default(true);
            $table->string('status', 50);
            $table->string('tipo', 10)->nullable();
            $table->string('protocolo_indisponibilidade', 100)->unique();
            $table->string('numero_processo', 50)->nullable();
            $table->string('usuario', 255)->nullable();
            $table->string('ordem_status', 50)->nullable();
            $table->string('forum_vara', 255)->nullable();
            $table->text('nome_instituicao')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('telefone', 50)->nullable();
            $table->timestamp('data_pedido')->nullable();
            $table->boolean('ordem_prioritaria')->nullable();
            $table->boolean('segredo_justica')->nullable();
            $table->string('cancelamento_protocolo', 100)->nullable();
            $table->integer('cancelamento_tipo')->nullable();
            $table->timestamp('cancelamento_data')->nullable();
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indisponibilidade');
    }
};
