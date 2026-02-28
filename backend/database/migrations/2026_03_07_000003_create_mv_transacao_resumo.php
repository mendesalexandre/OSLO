<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE MATERIALIZED VIEW mv_transacao_resumo AS
            SELECT
                t.indicador_pessoal_id,
                COUNT(*)                                                       AS total,
                COUNT(*) FILTER (WHERE t.situacao = 'pendente')                AS total_pendente,
                COUNT(*) FILTER (WHERE t.situacao = 'confirmada')              AS total_confirmada,
                COUNT(*) FILTER (WHERE t.situacao = 'liquidada')               AS total_liquidada,
                COUNT(*) FILTER (WHERE t.situacao = 'cancelada')               AS total_cancelada,
                COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'entrada' AND t.situacao != 'cancelada'), 0) AS total_entradas,
                COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'saida'   AND t.situacao != 'cancelada'), 0) AS total_saidas,
                COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'entrada' AND t.situacao != 'cancelada'), 0)
                    - COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'saida' AND t.situacao != 'cancelada'), 0) AS saldo
            FROM transacao t
            JOIN tipo_transacao tt ON tt.id = t.tipo_transacao_id
            WHERE t.data_exclusao IS NULL
              AND t.is_ativo = TRUE
            GROUP BY t.indicador_pessoal_id
            WITH DATA
        ");

        DB::statement('CREATE UNIQUE INDEX ON mv_transacao_resumo (indicador_pessoal_id)');
    }

    public function down(): void
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_transacao_resumo');
    }
};
