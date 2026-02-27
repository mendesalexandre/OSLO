<?php

namespace App\Http\Controllers;

use App\Models\Indisponibilidade;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndisponibilidadeController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = Indisponibilidade::with('partes.matriculas')
            ->whereNull('data_exclusao');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($busca = $request->input('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('protocolo_indisponibilidade', 'ilike', "%{$busca}%")
                  ->orWhere('numero_processo', 'ilike', "%{$busca}%");
            });
        }

        if ($cpfCnpj = $request->input('cpf_cnpj')) {
            $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);
            $query->whereHas('partes', fn ($q) => $q->where('cpf_cnpj', $cpfCnpj));
        }

        return $this->sucesso(
            $query->orderByDesc('data_cadastro')->paginate(15)
        );
    }

    public function show(int $id): JsonResponse
    {
        $indisponibilidade = Indisponibilidade::with('partes.matriculas')
            ->whereNull('data_exclusao')
            ->find($id);

        if (!$indisponibilidade) {
            return $this->naoEncontrado('Indisponibilidade não encontrada');
        }

        return $this->sucesso($indisponibilidade);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status'                      => 'required|in:pendente,cumprida,cancelada,em_analise',
            'tipo'                        => 'nullable|string|max:10',
            'protocolo_indisponibilidade' => 'required|string|max:100|unique:indisponibilidade,protocolo_indisponibilidade',
            'numero_processo'             => 'nullable|string|max:50',
            'usuario'                     => 'nullable|string|max:255',
            'ordem_status'                => 'nullable|string|max:50',
            'forum_vara'                  => 'nullable|string|max:255',
            'nome_instituicao'            => 'nullable|string',
            'email'                       => 'nullable|email|max:255',
            'telefone'                    => 'nullable|string|max:50',
            'data_pedido'                 => 'nullable|date',
            'ordem_prioritaria'           => 'nullable|boolean',
            'segredo_justica'             => 'nullable|boolean',
            'partes'                      => 'nullable|array',
            'partes.*.cpf_cnpj'           => 'required_with:partes|string|max:20',
            'partes.*.nome_razao'         => 'required_with:partes|string|max:255',
            'partes.*.matriculas'         => 'nullable|array',
            'partes.*.matriculas.*.matricula' => 'required_with:partes.*.matriculas|string|max:100',
        ]);

        $partes = $validated['partes'] ?? [];
        unset($validated['partes']);

        $indisponibilidade = DB::transaction(function () use ($validated, $partes) {
            $ind = Indisponibilidade::create($validated);

            foreach ($partes as $parteData) {
                $matriculas = $parteData['matriculas'] ?? [];
                unset($parteData['matriculas']);

                $parteData['cpf_cnpj'] = preg_replace('/\D/', '', $parteData['cpf_cnpj']);
                $parte = $ind->partes()->create($parteData);

                foreach ($matriculas as $mat) {
                    $parte->matriculas()->create($mat);
                }
            }

            return $ind;
        });

        return $this->criado(
            $indisponibilidade->load('partes.matriculas')
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $indisponibilidade = Indisponibilidade::whereNull('data_exclusao')->find($id);

        if (!$indisponibilidade) {
            return $this->naoEncontrado('Indisponibilidade não encontrada');
        }

        $validated = $request->validate([
            'status'           => 'sometimes|required|in:pendente,cumprida,cancelada,em_analise',
            'tipo'             => 'nullable|string|max:10',
            'numero_processo'  => 'nullable|string|max:50',
            'usuario'          => 'nullable|string|max:255',
            'ordem_status'     => 'nullable|string|max:50',
            'forum_vara'       => 'nullable|string|max:255',
            'nome_instituicao' => 'nullable|string',
            'email'            => 'nullable|email|max:255',
            'telefone'         => 'nullable|string|max:50',
            'data_pedido'      => 'nullable|date',
            'ordem_prioritaria' => 'nullable|boolean',
            'segredo_justica'  => 'nullable|boolean',
        ]);

        $indisponibilidade->update($validated);

        return $this->sucesso(
            $indisponibilidade->fresh()->load('partes.matriculas'),
            'Indisponibilidade atualizada com sucesso'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $indisponibilidade = Indisponibilidade::whereNull('data_exclusao')->find($id);

        if (!$indisponibilidade) {
            return $this->naoEncontrado('Indisponibilidade não encontrada');
        }

        $indisponibilidade->delete();

        return $this->sucesso(null, 'Indisponibilidade excluída com sucesso');
    }

    public function porCpfCnpj(string $cpfCnpj): JsonResponse
    {
        $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);

        $indisponibilidades = Indisponibilidade::with('partes.matriculas')
            ->whereNull('data_exclusao')
            ->whereHas('partes', fn ($q) => $q->where('cpf_cnpj', $cpfCnpj))
            ->orderByDesc('data_cadastro')
            ->get();

        return $this->sucesso($indisponibilidades);
    }

    public function cancelar(Request $request, int $id): JsonResponse
    {
        $indisponibilidade = Indisponibilidade::whereNull('data_exclusao')->find($id);

        if (!$indisponibilidade) {
            return $this->naoEncontrado('Indisponibilidade não encontrada');
        }

        $validated = $request->validate([
            'cancelamento_protocolo' => 'required|string|max:100',
            'cancelamento_tipo'      => 'required|integer',
            'cancelamento_data'      => 'required|date',
        ]);

        $indisponibilidade->update(array_merge($validated, ['status' => 'cancelada']));

        return $this->sucesso(
            $indisponibilidade->fresh()->load('partes.matriculas'),
            'Indisponibilidade cancelada com sucesso'
        );
    }
}
