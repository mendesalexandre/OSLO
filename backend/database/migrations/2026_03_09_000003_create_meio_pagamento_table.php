<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meio_pagamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forma_pagamento_id')->constrained('forma_pagamento');
            $table->string('nome', 100);
            $table->boolean('is_ativo')->default(true);
            $table->timestamp('data_cadastro')->nullable()->useCurrent();
            $table->timestamp('data_alteracao')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meio_pagamento');
    }
};
