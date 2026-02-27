<?php

namespace App\Console\Commands\ONR;

use App\Jobs\ONR\Certidao\ProcessarTodasCertidoesJob;
use App\Services\ONR\Certidao\CertidaoService;
use Illuminate\Console\Command;

class SincronizarCertidaoCommand extends Command
{
    protected $signature = 'onr:sincronizar-certidao {--async : Executar de forma assíncrona}';
    protected $description = 'Sincroniza as certidões ONR usando batch jobs';

    public function handle(): void
    {
        $this->info('🔄 Buscando certidões do ONR...');

        $certidoes = (new CertidaoService)->listarPedidos();
        $total = count($certidoes['PEDIDO_CERTIDAO']);

        if ($total === 0) {
            $this->info('Nenhuma certidão encontrada.');
            return;
        }

        $this->info("📋 Encontradas {$total} certidões para processar");

        if ($this->option('async')) {
            // Executar de forma assíncrona (recomendado)
            ProcessarTodasCertidoesJob::dispatch($certidoes['PEDIDO_CERTIDAO']);
            $this->info("🚀 Jobs disparados de forma assíncrona para {$total} certidões");
            $this->info("📊 Use 'php artisan queue:work' para processar ou monitore via interface web");
        } else {
            // Executar de forma síncrona (para teste)
            $this->warn("⚠️  Executando de forma síncrona - pode demorar...");
            ProcessarTodasCertidoesJob::dispatchSync($certidoes['PEDIDO_CERTIDAO']);
            $this->info("✅ Processamento síncrono concluído");
        }
    }
}
