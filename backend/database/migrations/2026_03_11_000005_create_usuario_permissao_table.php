<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_permissao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('permissao_id')->constrained('permissao')->cascadeOnDelete();
            $table->string('tipo', 20)->default('permitir'); // permitir | negar
            $table->timestamps();

            $table->unique(['usuario_id', 'permissao_id']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_permissao');
    }
};
