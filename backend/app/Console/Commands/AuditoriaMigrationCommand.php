<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Gera arquivo de migration para aplicar/remover triggers de auditoria.
 *
 * NÃO executa diretamente — apenas cria o arquivo .php em database/migrations/.
 * O desenvolvedor deve rodar `php artisan migrate` depois.
 */
class AuditoriaMigrationCommand extends Command
{
    protected $signature = 'auditoria:migration
        {tabelas : Nomes das tabelas separados por vírgula}
        {--schema=public : Schema das tabelas}
        {--remover : Gera migration de remoção ao invés de aplicação}';

    protected $description = 'Gerar arquivo de migration para aplicar/remover auditoria em tabelas';

    public function handle(): int
    {
        $schema = $this->option('schema');
        $remover = $this->option('remover');
        $tabelasInput = array_map('trim', explode(',', $this->argument('tabelas')));

        $this->info('Gerando migration de auditoria...');
        $this->newLine();

        $tabelasValidas = [];

        foreach ($tabelasInput as $tabela) {
            // Verificar se a tabela existe
            if (!$this->tabelaExiste($schema, $tabela)) {
                $this->line("  <fg=red>✗</> {$tabela} — tabela não encontrada no schema {$schema}");
                continue;
            }

            // Verificar se já tem trigger
            $temTrigger = $this->tabelaTemTrigger($schema, $tabela);

            if (!$remover && $temTrigger) {
                $this->line("  <fg=yellow>⚠</> {$tabela} — já possui trigger de auditoria (pulada)");
                continue;
            }

            if ($remover && !$temTrigger) {
                $this->line("  <fg=yellow>⚠</> {$tabela} — não possui trigger de auditoria (pulada)");
                continue;
            }

            $acao = $remover ? 'trigger será removido' : 'trigger será aplicado';
            $this->line("  <fg=green>✓</> {$tabela} — {$acao}");
            $tabelasValidas[] = $tabela;
        }

        // Nenhuma tabela válida
        if (empty($tabelasValidas)) {
            $this->newLine();
            $this->warn('Nenhuma tabela válida para gerar migration.');
            return self::SUCCESS;
        }

        // Gerar arquivo de migration
        $timestamp = now()->format('Y_m_d_His');
        $acao = $remover ? 'remover' : 'aplicar';
        $nomeArquivo = $this->gerarNomeArquivo($timestamp, $acao, $tabelasValidas);
        $caminho = database_path("migrations/{$nomeArquivo}");

        $conteudo = $this->gerarConteudoMigration($tabelasValidas, $schema, $remover);
        file_put_contents($caminho, $conteudo);

        $this->newLine();
        $this->info('Migration criada:');
        $this->line("  database/migrations/{$nomeArquivo}");

        // Mostrar tabelas auditadas após migração
        $this->newLine();
        $this->mostrarResumoFinal($schema, $tabelasValidas, $remover);

        $this->newLine();
        $this->info('Próximo passo: php artisan migrate');

        return self::SUCCESS;
    }

    /**
     * Gera o nome do arquivo de migration.
     */
    private function gerarNomeArquivo(string $timestamp, string $acao, array $tabelas): string
    {
        if (count($tabelas) > 5) {
            return "{$timestamp}_auditoria_{$acao}_multiplas_tabelas.php";
        }

        $nomeTabelas = implode('_', $tabelas);

        return "{$timestamp}_auditoria_{$acao}_{$nomeTabelas}.php";
    }

    /**
     * Gera o conteúdo PHP da migration.
     */
    private function gerarConteudoMigration(array $tabelas, string $schema, bool $remover): string
    {
        $tabelasPhp = '';
        foreach ($tabelas as $tabela) {
            $tabelasPhp .= "        ['schema' => '{$schema}', 'tabela' => '{$tabela}'],\n";
        }
        $tabelasPhp = rtrim($tabelasPhp, "\n");

        $listaNomes = implode(', ', $tabelas);
        $dataGeracao = now()->format('Y-m-d H:i:s');

        $acaoDescricao = $remover ? 'Remove' : 'Aplica';
        $fnUp = $remover ? 'fn_remover_auditoria' : 'fn_aplicar_auditoria';
        $fnDown = $remover ? 'fn_aplicar_auditoria' : 'fn_remover_auditoria';

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * {$acaoDescricao} auditoria nas tabelas: {$listaNomes}
 *
 * Gerado automaticamente por: php artisan auditoria:migration
 * Data: {$dataGeracao}
 */
return new class extends Migration
{
    private array \$tabelas = [
{$tabelasPhp}
    ];

    public function up(): void
    {
        foreach (\$this->tabelas as \$item) {
            DB::statement(
                "SELECT auditoria.{$fnUp}(?, ?);",
                [\$item['schema'], \$item['tabela']]
            );
        }
    }

    public function down(): void
    {
        foreach (\$this->tabelas as \$item) {
            DB::statement(
                "SELECT auditoria.{$fnDown}(?, ?);",
                [\$item['schema'], \$item['tabela']]
            );
        }
    }
};

PHP;
    }

    /**
     * Mostra resumo final com tabelas auditadas.
     */
    private function mostrarResumoFinal(string $schema, array $tabelasNovas, bool $remover): void
    {
        // Tabelas já auditadas
        $existentes = DB::select("
            SELECT event_object_schema AS schema, event_object_table AS tabela
            FROM information_schema.triggers
            WHERE trigger_name LIKE 'trg_auditoria_%'
            GROUP BY event_object_schema, event_object_table
            ORDER BY event_object_schema, event_object_table
        ");

        $linhas = [];

        foreach ($existentes as $t) {
            $observacao = in_array($t->tabela, $tabelasNovas) && $remover ? '(será removida)' : '(já existia)';
            $linhas[] = [$t->schema, $t->tabela, $observacao];
        }

        if (!$remover) {
            foreach ($tabelasNovas as $tabela) {
                $jaExiste = collect($existentes)->contains(fn ($t) => $t->tabela === $tabela && $t->schema === $schema);
                if (!$jaExiste) {
                    $linhas[] = [$schema, $tabela, '(nova)'];
                }
            }
        }

        if (!empty($linhas)) {
            $this->info('Tabelas com auditoria ativa após migração:');
            $this->table(['Schema', 'Tabela', 'Observação'], $linhas);
        }
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

    /**
     * Verifica se uma tabela já possui trigger de auditoria.
     */
    private function tabelaTemTrigger(string $schema, string $tabela): bool
    {
        $resultado = DB::select("
            SELECT 1 FROM information_schema.triggers
            WHERE event_object_schema = ?
              AND event_object_table = ?
              AND trigger_name LIKE 'trg_auditoria_%'
            LIMIT 1
        ", [$schema, $tabela]);

        return !empty($resultado);
    }
}
