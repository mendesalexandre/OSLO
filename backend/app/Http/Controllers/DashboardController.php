<?php

namespace App\Http\Controllers;

use App\Models\IndicadorPessoal;
use App\Models\Transacao;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use RespostaApi;

    public function index(): JsonResponse
    {
        return $this->sucesso([
            'resumo_financeiro'  => $this->obterResumoFinanceiro(),
            'eventos_timeline'   => $this->obterEventosTimeline(),
            'eventos_calendario' => $this->obterEventosCalendario(),
            'indicadores'        => $this->obterIndicadores(),
            'grafico_atividade'  => $this->obterGraficoAtividade(),
        ]);
    }

    public function eventosPorMes(Request $request): JsonResponse
    {
        $ano = (int) $request->query('ano', now()->year);
        $mes = (int) $request->query('mes', now()->month);

        return $this->sucesso([
            'eventos' => $this->eventosMes($ano, $mes),
        ]);
    }

    // ── Resumo financeiro ──────────────────────────────────────────────────

    private function obterResumoFinanceiro(): array
    {
        $inicio = now()->startOfMonth()->toDateString();
        $fim    = now()->endOfMonth()->toDateString();

        $entrada = Transacao::ativo()
            ->porTipo('entrada')
            ->whereBetween('data_transacao', [$inicio, $fim])
            ->where('situacao', '!=', 'cancelada')
            ->sum('valor');

        $saida = Transacao::ativo()
            ->porTipo('saida')
            ->whereBetween('data_transacao', [$inicio, $fim])
            ->where('situacao', '!=', 'cancelada')
            ->sum('valor');

        $pendentes = Transacao::ativo()
            ->where('situacao', 'pendente')
            ->count();

        return [
            'total_entrada'         => (float) $entrada,
            'total_saida'           => (float) $saida,
            'saldo'                 => (float) ($entrada - $saida),
            'transacoes_pendentes'  => $pendentes,
        ];
    }

    // ── Timeline ───────────────────────────────────────────────────────────

    private function obterEventosTimeline(): array
    {
        return Transacao::ativo()
            ->with('tipoTransacao')
            ->latest('data_transacao')
            ->take(8)
            ->get()
            ->map(fn ($t) => [
                'id'        => "transacao-{$t->id}",
                'tipo'      => 'transacao',
                'titulo'    => $t->descricao,
                'descricao' => $t->tipoTransacao?->descricao ?? '',
                'valor'     => $t->valor,
                'data'      => $t->data_transacao?->toIso8601String(),
                'icone'     => $t->tipoTransacao?->icone ?? 'receipt',
                'cor'       => $this->hexCor($t->tipoTransacao?->cor),
            ])
            ->sortByDesc('data')
            ->values()
            ->all();
    }

    // ── Calendário ─────────────────────────────────────────────────────────

    private function obterEventosCalendario(): array
    {
        return $this->eventosMes(now()->year, now()->month);
    }

    private function eventosMes(int $ano, int $mes): array
    {
        return DB::table('transacao')
            ->join('tipo_transacao', 'tipo_transacao.id', '=', 'transacao.tipo_transacao_id')
            ->selectRaw("
                EXTRACT(DAY FROM transacao.data_transacao)::int AS dia,
                COUNT(*) AS count,
                COALESCE(SUM(transacao.valor) FILTER (WHERE tipo_transacao.tipo = 'entrada'), 0) AS entrada,
                COALESCE(SUM(transacao.valor) FILTER (WHERE tipo_transacao.tipo = 'saida'), 0)   AS saida
            ")
            ->whereYear('transacao.data_transacao', $ano)
            ->whereMonth('transacao.data_transacao', $mes)
            ->whereNull('transacao.data_exclusao')
            ->where('transacao.is_ativo', true)
            ->where('transacao.situacao', '!=', 'cancelada')
            ->groupByRaw("EXTRACT(DAY FROM transacao.data_transacao)")
            ->orderByRaw("dia")
            ->get()
            ->map(fn ($r) => [
                'dia'    => (int)   $r->dia,
                'count'  => (int)   $r->count,
                'entrada'=> (float) $r->entrada,
                'saida'  => (float) $r->saida,
                'saldo'  => (float) ($r->entrada - $r->saida),
            ])
            ->all();
    }

    // ── Indicadores ────────────────────────────────────────────────────────

    private function obterIndicadores(): array
    {
        return [
            'transacoes_mes' => Transacao::ativo()
                ->whereMonth('data_transacao', now()->month)
                ->whereYear('data_transacao', now()->year)
                ->count(),

            'indicadores_ativos' => IndicadorPessoal::ativo()
                ->where('is_atual', true)
                ->count(),

            'indisponibilidades_ativas' => DB::table('indisponibilidade_parte')
                ->join('indisponibilidade', 'indisponibilidade_parte.indisponibilidade_id', '=', 'indisponibilidade.id')
                ->where('indisponibilidade.status', '!=', 'cancelada')
                ->whereNull('indisponibilidade.data_exclusao')
                ->distinct('indisponibilidade_parte.cpf_cnpj')
                ->count('indisponibilidade_parte.cpf_cnpj'),

            'depositos_judiciais' => 0, // Phase futura — modelo não implementado
        ];
    }

    // ── Gráfico de atividade ───────────────────────────────────────────────

    private function obterGraficoAtividade(): array
    {
        $inicio = now()->startOfMonth()->toDateString();
        $fim    = now()->endOfMonth()->toDateString();

        return DB::table('transacao')
            ->selectRaw("
                TO_CHAR(data_transacao, 'DD/MM') AS data,
                COUNT(*) AS valor,
                EXTRACT(DOW FROM data_transacao)::int AS semana
            ")
            ->whereBetween('data_transacao', [$inicio, $fim])
            ->whereNull('data_exclusao')
            ->where('is_ativo', true)
            ->groupByRaw("data_transacao, EXTRACT(DOW FROM data_transacao)")
            ->orderBy('data_transacao')
            ->get()
            ->map(fn ($r) => [
                'data'   => $r->data,
                'valor'  => (int) $r->valor,
                'semana' => (int) $r->semana,
            ])
            ->all();
    }

    // ── Utilitários ────────────────────────────────────────────────────────

    /**
     * Converte o valor da coluna `cor` (Quasar color name) em hex CSS
     * para o frontend. Cores não mapeadas retornam cinza.
     */
    private function hexCor(?string $cor): string
    {
        return match($cor) {
            'positive' => '#4caf50',
            'negative' => '#f44336',
            'warning'  => '#ff9800',
            'info'     => '#2196f3',
            'primary'  => '#667eea',
            default    => '#9e9e9e',
        };
    }
}
