<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regime_bem', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->text('observacao')->nullable();
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regime_bem');
    }
};
