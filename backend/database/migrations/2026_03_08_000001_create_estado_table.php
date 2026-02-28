<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_ativo')->default(true)->index();
            $table->string('nome', 100);
            $table->string('sigla', 2)->unique();
            $table->unsignedInteger('ibge_codigo')->nullable()->unique();
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado');
    }
};
