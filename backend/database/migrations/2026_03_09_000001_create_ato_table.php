<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ato', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->nullable()->unique();
            $table->string('nome', 300);
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
            $table->index('is_ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ato');
    }
};
