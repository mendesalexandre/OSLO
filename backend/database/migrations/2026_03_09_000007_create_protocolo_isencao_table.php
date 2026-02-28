<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolo_isencao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocolo_id')->constrained('protocolo')->cascadeOnDelete();
            $table->string('tipo', 50);
            $table->string('numero_processo', 50)->nullable();
            $table->string('vara', 100)->nullable();
            $table->date('data_decisao')->nullable();
            $table->decimal('valor_isento', 15, 2);
            $table->string('documento_path', 500)->nullable();
            $table->text('observacao')->nullable();
            $table->foreignId('usuario_id')->constrained('usuario');
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolo_isencao');
    }
};
