<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolo_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protocolo_id')->constrained('protocolo')->cascadeOnDelete();
            $table->foreignId('forma_pagamento_id')->constrained('forma_pagamento');
            $table->foreignId('meio_pagamento_id')->nullable()->constrained('meio_pagamento');
            $table->foreignId('usuario_id')->constrained('usuario');
            $table->decimal('valor', 15, 2);
            $table->timestamp('data_pagamento')->useCurrent();
            $table->string('comprovante', 200)->nullable();
            $table->text('observacao')->nullable();
            $table->string('status', 20)->default('confirmado'); // confirmado, estornado, cancelado
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolo_pagamento');
    }
};
