<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissao', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();     // ex: PROTOCOLO_CRIAR
            $table->string('descricao', 255)->nullable();
            $table->string('modulo', 100);             // ex: Protocolo
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();

            $table->index('modulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissao');
    }
};
