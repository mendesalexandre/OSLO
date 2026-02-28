<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransacaoRequest;
use App\Http\Requests\UpdateTransacaoRequest;
use App\Models\MvTransacaoResumo;
use App\Models\Transacao;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransacaoController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = Transacao::with(['tipoTransacao', 'motivoTransacao', 'banco'])
            ->whereNull('data_exclusao');

        if ($indicadorId = $request->integer('indicador_pessoal_id')) {
            $query->where('indicador_pessoal_id', $indicadorId);
        }

        if ($tipo = $request->input('tipo')) {
            $query->porTipo($tipo);
        }

        if ($situacao = $request->input('situacao')) {
            $query->where('situacao', $situacao);
        }

        if ($inicio = $request->input('data_inicio')) {
            $query->where('data_transacao', '>=', $inicio);
        }

        if ($fim = $request->input('data_fim')) {
            $query->where('data_transacao', '<=', $fim);
        }

        return $this->sucesso(
            $query->orderByDesc('data_transacao')->orderByDesc('data_cadastro')->paginate(15)
        );
    }

    public function store(StoreTransacaoRequest $request): JsonResponse
    {
        $dados = $request->validated();
        $dados['situacao'] = 'pendente';
        $dados['numero_transacao'] = $this->gerarNumeroTransacao();

        $transacao = Transacao::create($dados);

        return $this->criado(
            $transacao->load(['indicadorPessoal', 'tipoTransacao', 'motivoTransacao', 'banco'])
        );
    }

    public function show(string $id): JsonResponse
    {
        $transacao = Transacao::with([
            'indicadorPessoal',
            'tipoTransacao',
            'motivoTransacao',
            'banco',
            'auditorias.usuario',
        ])->whereNull('data_exclusao')->find($id);

        if (!$transacao) {
            return $this->naoEncontrado('Transação não encontrada');
        }

        return $this->sucesso($transacao);
    }

    public function update(UpdateTransacaoRequest $request, string $id): JsonResponse
    {
        $transacao = Transacao::whereNull('data_exclusao')->find($id);

        if (!$transacao) {
            return $this->naoEncontrado('Transação não encontrada');
        }

        if ($transacao->situacao !== 'pendente') {
            return $this->erro('Apenas transações pendentes podem ser editadas', 422);
        }

        $transacao->update($request->validated());

        return $this->sucesso(
            $transacao->fresh()->load(['tipoTransacao', 'motivoTransacao', 'banco']),
            'Transação atualizada com sucesso'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $transacao = Transacao::whereNull('data_exclusao')->find($id);

        if (!$transacao) {
            return $this->naoEncontrado('Transação não encontrada');
        }

        $transacao->cancelar('Excluída pelo usuário');
        $transacao->delete();

        return $this->sucesso(null, 'Transação excluída com sucesso');
    }

    public function confirmar(string $id): JsonResponse
    {
        $transacao = Transacao::whereNull('data_exclusao')->find($id);

        if (!$transacao) {
            return $this->naoEncontrado('Transação não encontrada');
        }

        if ($transacao->situacao !== 'pendente') {
            return $this->erro('Apenas transações pendentes podem ser confirmadas', 422);
        }

        $transacao->confirmar();

        return $this->sucesso(
            $transacao->fresh()->load(['tipoTransacao', 'motivoTransacao']),
            'Transação confirmada com sucesso'
        );
    }

    public function resumo(Request $request): JsonResponse
    {
        $indicadorId = $request->integer('indicador_pessoal_id');

        if (!$indicadorId) {
            return $this->erro('O parâmetro indicador_pessoal_id é obrigatório', 422);
        }

        try {
            $resumo = MvTransacaoResumo::where('indicador_pessoal_id', $indicadorId)->first();
        } catch (\Throwable) {
            $resumo = null;
        }

        // Se a view não existe ou não tem dados para este indicador, usa query direta
        if ($resumo === null) {
            $resumo = $this->resumoViaQuery($indicadorId);
        }

        return $this->sucesso($resumo);
    }

    private function resumoViaQuery(int $indicadorId): array
    {
        $rows = DB::select("
            SELECT
                COUNT(*)                                                                       AS total,
                COUNT(*) FILTER (WHERE t.situacao = 'pendente')                                AS total_pendente,
                COUNT(*) FILTER (WHERE t.situacao = 'confirmada')                              AS total_confirmada,
                COUNT(*) FILTER (WHERE t.situacao = 'liquidada')                               AS total_liquidada,
                COUNT(*) FILTER (WHERE t.situacao = 'cancelada')                               AS total_cancelada,
                COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'entrada' AND t.situacao != 'cancelada'), 0) AS total_entradas,
                COALESCE(SUM(t.valor) FILTER (WHERE tt.tipo = 'saida'   AND t.situacao != 'cancelada'), 0) AS total_saidas
            FROM transacao t
            JOIN tipo_transacao tt ON tt.id = t.tipo_transacao_id
            WHERE t.data_exclusao IS NULL
              AND t.is_ativo = TRUE
              AND t.indicador_pessoal_id = ?
        ", [$indicadorId]);

        $row = $rows[0] ?? null;

        if (!$row) {
            return [
                'indicador_pessoal_id' => $indicadorId,
                'total'           => 0,
                'total_pendente'  => 0,
                'total_confirmada'=> 0,
                'total_liquidada' => 0,
                'total_cancelada' => 0,
                'total_entradas'  => '0.00',
                'total_saidas'    => '0.00',
                'saldo'           => '0.00',
            ];
        }

        return [
            'indicador_pessoal_id' => $indicadorId,
            'total'           => (int) $row->total,
            'total_pendente'  => (int) $row->total_pendente,
            'total_confirmada'=> (int) $row->total_confirmada,
            'total_liquidada' => (int) $row->total_liquidada,
            'total_cancelada' => (int) $row->total_cancelada,
            'total_entradas'  => number_format((float) $row->total_entradas, 2, '.', ''),
            'total_saidas'    => number_format((float) $row->total_saidas, 2, '.', ''),
            'saldo'           => number_format((float) $row->total_entradas - (float) $row->total_saidas, 2, '.', ''),
        ];
    }

    private function gerarNumeroTransacao(): string
    {
        $prefixo = 'TRX-' . now()->format('Y-m-d');
        $seq     = Transacao::withTrashed()
                       ->where('numero_transacao', 'like', "{$prefixo}-%")
                       ->count() + 1;

        return "{$prefixo}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
