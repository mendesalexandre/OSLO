<?php

namespace App\Jobs\Doi;

use App\Models\Doi;
use App\Models\Doi\Alienante;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DoiImportarAlienanteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $timeout = 60;
    public $tries = 2;

    public function __construct(public Doi $doi, public $alienante)
    {
        $this->onQueue('doi-normal');
    }

    public function handle(): void
    {
        Log::info("🔄 Iniciando importação de alienante para DOI {$this->doi->numero_controle}", [
            'alienante' => $this->alienante,
        ]);
        try {
            // Remover alienantes existentes para este DOI
            Alienante::where('doi_id', $this->doi->id)->delete();

            // Inserir novo alienante
            Alienante::create([
                'doi_id' => $this->doi->id,
                'data' => $this->alienante
            ]);

            Log::debug("Alienante salvo para DOI {$this->doi->numero_controle}");

            // Verificar se todas as partes foram processadas
            $this->verificarSeCompletou();
        } catch (\Exception $e) {
            Log::error("Erro ao salvar alienante para DOI {$this->doi->numero_controle}", [
                'erro' => $e->getMessage(),
                'doi_id' => $this->doi->id
            ]);

            throw $e;
        }
    }

    private function verificarSeCompletou(): void
    {
        // Recarregar o DOI para ter dados atualizados
        $this->doi->refresh();

        // Se tanto alienantes quanto adquirentes existem, marcar como concluído
        if ($this->doi->alienantes()->exists() && $this->doi->adquirentes()->exists()) {
            $this->doi->update([
                'status_processamento' => 'concluido',
                'processado_em' => now()
            ]);

            Log::info("DOI {$this->doi->numero_controle} marcado como concluído - todas as partes processadas");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job de alienante falhou definitivamente", [
            'doi_id' => $this->doi->id,
            'doi_numero' => $this->doi->numero_controle,
            'erro' => $exception->getMessage()
        ]);
    }
}
