<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Aplica ou remove triggers de auditoria diretamente no banco.
 *
 * Executor direto — as alterações são imediatas (sem criar migration).
 */
class AuditoriaAplicarCommand extends Command
{
    protected $signature = 'auditoria:aplicar
        {tabelas? : Nomes das tabelas separados por vírgula}
        {--schema=public : Schema das tabelas}
        {--remover : Remove a auditoria ao invés de aplicar}
        {--todas : Aplica em todas as tabelas do schema}
        {--listar : Lista tabelas com auditoria ativa}';

    protected $description = 'Aplicar ou remover triggers de auditoria nas tabelas';

    /**
     * Tabelas do framework/infraestrutura que devem ser ignoradas com --todas.
     */
    private array $tabelasIgnoradas = [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'personal_access_tokens',
        'failed_jobs',
        'jobs',
        'job_batches',
        'cache',
        'cache_locks',
        'sessions',
    ];

    public function handle(): int
    {
        $schema = $this->option('schema');

        // --listar
        if ($this->option('listar')) {
            return $this->listarTabelasAuditadas();
        }

        // Determinar tabelas
        $tabelas = $this->resolverTabelas($schema);

        if (empty($tabelas)) {
            $this->error('Nenhuma tabela especificada. Use {tabelas}, --todas ou --listar.');
            return self::FAILURE;
        }

        $remover = $this->option('remover');
        $acao = $remover ? 'Removendo' : 'Aplicando';
        $this->info("{$acao} auditoria...\n");

        $resultados = [];

        foreach ($tabelas as $tabela) {
            // Verificar se a tabela existe
            if (!$this->tabelaExiste($schema, $tabela)) {
                $this->line("  <fg=red>✗</> {$tabela} — tabela não encontrada no schema {$schema}");
                $resultados[] = [$tabela, $remover ? 'Remover' : 'Aplicar', 'Não encontrada'];
                continue;
            }

            try {
                if ($remover) {
                    DB::statement("SELECT auditoria.fn_remover_auditoria(?, ?)", [$schema, $tabela]);
                    $this->line("  <fg=green>✓</> {$tabela} — trigger removido");
                    $resultados[] = [$tabela, 'Remover', 'OK'];
                } else {
                    DB::statement("SELECT auditoria.fn_aplicar_auditoria(?, ?)", [$schema, $tabela]);
                    $this->line("  <fg=green>✓</> {$tabela} — trigger aplicado");
                    $resultados[] = [$tabela, 'Aplicar', 'OK'];
                }
            } catch (\Throwable $e) {
                $this->line("  <fg=red>✗</> {$tabela} — erro: {$e->getMessage()}");
                $resultados[] = [$tabela, $remover ? 'Remover' : 'Aplicar', 'Erro'];
            }
        }

        $this->newLine();
        $this->table(['Tabela', 'Ação', 'Status'], $resultados);

        return self::SUCCESS;
    }

    /**
     * Lista as tabelas que possuem trigger de auditoria ativo.
     */
    private function listarTabelasAuditadas(): int
    {
        $tabelas = DB::select("
            SELECT event_object_schema AS schema, event_object_table AS tabela
            FROM information_schema.triggers
            WHERE trigger_name LIKE 'trg_auditoria_%'
            GROUP BY event_object_schema, event_object_table
            ORDER BY event_object_schema, event_object_table
        ");

        if (empty($tabelas)) {
            $this->info('Nenhuma tabela com auditoria ativa.');
            return self::SUCCESS;
        }

        $this->info('Tabelas com auditoria ativa:');
        $this->newLine();

        $this->table(
            ['Schema', 'Tabela'],
            array_map(fn ($t) => [$t->schema, $t->tabela], $tabelas)
        );

        return self::SUCCESS;
    }

    /**
     * Resolve a lista de tabelas com base nos argumentos/opções.
     */
    private function resolverTabelas(string $schema): array
    {
        // --todas
        if ($this->option('todas')) {
            $tabelas = DB::select("
                SELECT table_name
                FROM information_schema.tables
                WHERE table_schema = ?
                  AND table_type = 'BASE TABLE'
                ORDER BY table_name
            ", [$schema]);

            return array_filter(
                array_map(fn ($t) => $t->table_name, $tabelas),
                fn ($t) => !in_array($t, $this->tabelasIgnoradas)
            );
        }

        // tabelas explícitas
        $argTabelas = $this->argument('tabelas');

        if ($argTabelas) {
            return array_map('trim', explode(',', $argTabelas));
        }

        return [];
    }

    /**
     * Verifica se uma tabela existe no schema.
     */
    private function tabelaExiste(string $schema, string $tabela): bool
    {
        $resultado = DB::select("
            SELECT 1 FROM information_schema.tables
            WHERE table_schema = ? AND table_name = ?
            LIMIT 1
        ", [$schema, $tabela]);

        return !empty($resultado);
    }
}
