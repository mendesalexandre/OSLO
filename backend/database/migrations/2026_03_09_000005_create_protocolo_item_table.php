<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolo_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocolo_id')->constrained('protocolo')->cascadeOnDelete();
            $table->foreignId('ato_id')->constrained('ato');
            $table->string('descricao', 300)->nullable();
            $table->integer('quantidade')->default(1);
            $table->decimal('base_calculo', 15, 2)->nullable();
            $table->decimal('valor_unitario', 15, 2)->default(0);
            $table->decimal('valor_total', 15, 2)->default(0);
            $table->text('observacao')->nullable();
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolo_item');
    }
};
