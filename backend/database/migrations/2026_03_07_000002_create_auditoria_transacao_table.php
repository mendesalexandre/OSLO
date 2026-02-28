<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria_transacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transacao_id')->constrained('transacao');
            $table->foreignId('usuario_id')->nullable()->constrained('usuario');

            $table->string('campo_alterado')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_novo')->nullable();
            $table->string('acao', 50);
            $table->text('observacao')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('transacao_id');
            $table->index('usuario_id');
            $table->index('created_at');
            $table->index('acao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_transacao');
    }
};
