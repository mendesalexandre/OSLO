<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motivo_transacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_transacao_id')->constrained('tipo_transacao');
            $table->string('descricao');
            $table->text('descricao_expandida')->nullable();
            $table->boolean('exige_documento')->default(false);
            $table->boolean('exige_beneficiario')->default(false);
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->unique(['tipo_transacao_id', 'descricao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motivo_transacao');
    }
};
