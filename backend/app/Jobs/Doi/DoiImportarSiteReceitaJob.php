<?php

namespace App\Jobs\Doi;

use App\Models\Doi;
use App\Services\Doi\DoiTokenService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DoiImportarSiteReceitaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $timeout = 300;
    public $tries = 2;
    public $backoff = [60];

    public function __construct(public $data)
    {
        $this->onQueue('doi-normal');
    }

    public function handle(DoiTokenService $tokenService): void
    {
        Log::info("🔄 Iniciando importação do DOI", [
            'data' => $this->data,
        ]);
        if ($this->batch()?->cancelled()) {
            return;
        }

        // Criar/buscar DOI a partir dos dados
        $doi = $this->createOrUpdateDoi();

        try {
            $this->atualizarProgresso('Iniciando', 'Verificando token...', $doi->numero_controle);

            $cookie = $tokenService->obterTokenValido();

            // Marcar como processando
            $doi->update(['status_processamento' => 'processando']);
            $this->atualizarProgresso('Processando', "Buscando dados detalhados", $doi->numero_controle);

            $declaracao = $this->fetchDeclaracao($doi, $cookie);
            $doi->update(['debug' => $declaracao]);

            $this->atualizarProgresso('Processando alienantes/adquirentes', "DOI {$doi->numero_controle}", $doi->numero_controle);
            $this->processRelatedData($doi, $cookie);

            // Marcar como concluído
            $doi->update([
                'status_processamento' => 'concluido',
                'processado_em' => now()
            ]);

            $this->atualizarProgresso('Concluído', "DOI {$doi->numero_controle} processado com sucesso", $doi->numero_controle, true);
            Log::info("DOI {$doi->numero_controle} processado com sucesso");
        } catch (\Exception $e) {
            $this->atualizarProgresso('Erro', "Falha no DOI {$doi->numero_controle}: " . $e->getMessage(), $doi->numero_controle);

            $isTokenError = $this->isTokenRelatedError($e);

            if ($isTokenError) {
                // Erro de token - marcar como pausado e FINALIZAR o job normalmente
                $doi->update(['status_processamento' => 'pausado_sessao']);
                Log::warning("DOI {$doi->numero_controle} marcado como pausado_sessao devido ao erro 401");

                // Job completa normalmente (não lança exceção)
                return;
            }

            // Para outros erros, continuar com o tratamento normal
            $this->handleJobError($e, $doi);
        }
    }

    private function handleJobError(\Exception $e, Doi $doi): void
    {
        $isTokenError = $this->isTokenRelatedError($e);

        Log::error("Erro ao processar DOI {$doi->numero_controle}", [
            'erro' => $e->getMessage(),
            'tentativa' => $this->attempts(),
            'erro_token' => $isTokenError,
            'batch_id' => $this->batch()?->id
        ]);

        // Se erro de token - marcar como pausado e NÃO relançar exceção
        if ($isTokenError) {
            $doi->update(['status_processamento' => 'pausado_sessao']);
            Log::warning("DOI {$doi->numero_controle} marcado como pausado_sessao devido ao erro 401");

            // NÃO cancelar batch, NÃO relançar exceção - apenas "engolir" o erro
            return;
        }

        // Para outros erros, relançar exceção normalmente
        throw $e;
    }

    private function isTokenRelatedError(\Exception $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, '401') || str_contains($message, 'unauthorized');
    }

    private function fetchDeclaracao(Doi $doi, string $cookie): array
    {
        $url = "https://doi.rfb.gov.br/api/declaracoes/{$doi->doi_importacao_id}";
        return $this->makeRequest($url, $cookie);
    }

    private function processRelatedData(Doi $doi, string $cookie): void
    {
        $urlAlienantes = "https://doi.rfb.gov.br/api/declaracoes/{$doi->doi_importacao_id}/alienantes";
        $alienante = $this->makeRequest($urlAlienantes, $cookie);

        DoiImportarAlienanteJob::dispatch($doi, $alienante);

        $urlAdquirentes = "https://doi.rfb.gov.br/api/declaracoes/{$doi->doi_importacao_id}/adquirentes";
        $adquirente = $this->makeRequest($urlAdquirentes, $cookie);
        DoiImportarAdquirenteJob::dispatch($doi, $adquirente);
    }

    private function makeRequest(string $url, string $cookie): array
    {
        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'cookie' => $cookie,
            'priority' => 'u=1, i',
            'referer' => 'https://doi.rfb.gov.br/',
            'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36'
        ])
            ->timeout(30)
            ->retry(2, 1000)
            ->get($url);

        if (in_array($response->status(), [401, 403])) {
            throw new \Exception("Token DOI inválido ou expirado - Status: {$response->status()}");
        }

        return $response->throw()->json();
    }

    private function atualizarProgresso(string $status, string $detalhe, string $numeroControle, bool $concluido = false): void
    {
        $batchId = $this->batch()?->id;
        if (!$batchId) return;

        $progressKey = "batch_progress:{$batchId}";
        $progressData = [
            'batch_id' => $batchId,
            'job_id' => $this->job->uuid(),
            'doi_numero' => $numeroControle,
            'status' => $status,
            'detalhe' => $detalhe,
            'timestamp' => now()->toISOString(),
            'concluido' => $concluido
        ];

        Cache::put("job_progress:{$this->job->uuid()}", $progressData, 3600);

        $batchProgress = Cache::get($progressKey, []);
        $batchProgress[$this->job->uuid()] = $progressData;

        if (count($batchProgress) > 10) {
            $batchProgress = array_slice($batchProgress, -10, 10, true);
        }

        Cache::put($progressKey, $batchProgress, 3600);
    }

    private function createOrUpdateDoi(): Doi
    {
        return Doi::query()->updateOrCreate(
            ['doi_importacao_id' => $this->data['id']],
            [
                'numero_controle' => $this->data['numeroControle'],
                'data_ato' => $this->data['dataLavraturaRegistroAverbacao'],
                'data' => $this->data,
                'matricula' => $this->data['matricula'],
                'data_importacao' => now()->setTimezone('America/Cuiaba'),
                'status_processamento' => 'pendente' // Sempre inicia como pendente
            ]
        );
    }

    public function failed(\Throwable $exception): void
    {
        // Tentar buscar/criar DOI para atualizar status
        try {
            $doi = $this->createOrUpdateDoi();
            $this->atualizarProgresso('Falhou', "DOI {$doi->numero_controle} falhou: " . $exception->getMessage(), $doi->numero_controle);

            // Se não foi erro de token, marcar como pendente para tentar depois
            if (!$this->isTokenRelatedError($exception)) {
                $doi->update(['status_processamento' => 'pendente']);
            }

            Log::error("Job DOI falhou definitivamente", [
                'doi' => $doi->numero_controle,
                'batch_id' => $this->batch()?->id,
                'erro' => $exception->getMessage(),
                'tentativas' => $this->attempts()
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao processar falha do job DOI", [
                'erro_original' => $exception->getMessage(),
                'erro_fallback' => $e->getMessage(),
                'data' => $this->data
            ]);
        }
    }
}
