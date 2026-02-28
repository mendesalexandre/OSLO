<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('natureza', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_ativo')->default(true)->index();
            $table->uuid('uuid')->unique();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('codigo', 50)->nullable()->unique();
            $table->timestamp('data_cadastro')->useCurrent();
            $table->timestamp('data_alteracao')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('data_exclusao')->nullable();

            $table->index('nome');
        });

        // UUID auto-gerado via trigger (PostgreSQL)
        DB::statement("
            CREATE OR REPLACE FUNCTION fn_natureza_uuid()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF NEW.uuid IS NULL OR NEW.uuid = '' THEN
                    NEW.uuid = gen_random_uuid();
                END IF;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::statement("
            CREATE TRIGGER trg_natureza_uuid
            BEFORE INSERT ON natureza
            FOR EACH ROW EXECUTE FUNCTION fn_natureza_uuid();
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS trg_natureza_uuid ON natureza');
        DB::statement('DROP FUNCTION IF EXISTS fn_natureza_uuid');
        Schema::dropIfExists('natureza');
    }
};
