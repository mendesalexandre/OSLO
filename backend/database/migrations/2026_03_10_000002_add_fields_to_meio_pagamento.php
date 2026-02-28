<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meio_pagamento', function (Blueprint $table) {
            $table->text('descricao')->nullable()->after('nome');
            $table->string('identificador', 100)->nullable()->after('descricao');
            $table->decimal('taxa_percentual', 6, 2)->default(0)->after('identificador');
            $table->decimal('taxa_fixa', 10, 2)->default(0)->after('taxa_percentual');
            $table->unsignedInteger('prazo_compensacao')->default(0)->after('taxa_fixa');
        });
    }

    public function down(): void
    {
        Schema::table('meio_pagamento', function (Blueprint $table) {
            $table->dropColumn(['descricao', 'identificador', 'taxa_percentual', 'taxa_fixa', 'prazo_compensacao']);
        });
    }
};
