<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshMaterializedViewCommand extends Command
{
    protected $signature   = 'db:refresh-mv';
    protected $description = 'Atualiza a materialized view mv_transacao_resumo';

    public function handle(): int
    {
        $this->info('Atualizando mv_transacao_resumo...');

        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_transacao_resumo');

        $this->info('Materialized view atualizada com sucesso.');

        return self::SUCCESS;
    }
}
