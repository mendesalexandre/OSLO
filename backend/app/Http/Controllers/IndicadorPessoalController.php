<?php

namespace App\Http\Controllers;

use App\Models\CapacidadeCivil;
use App\Models\EstadoCivil;
use App\Models\Indisponibilidade;
use App\Models\IndicadorPessoal;
use App\Rules\ValidarDocumento;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IndicadorPessoalController extends Controller
{
    use RespostaApi;

    public function index(Request $request): JsonResponse
    {
        $query = IndicadorPessoal::atual()->ativo()
            ->with(['estadoCivil', 'profissao', 'nacionalidade'])
            ->withCount([
                'indisponibilidades as indisponibilidades_count' => function ($q) {
                    $q->whereNotIn('status', ['cancelada'])
                      ->whereNull('data_exclusao');
                },
            ]);

        if ($busca = $request->input('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'ilike', "%{$busca}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$busca}%");
            });
        }

        if ($tipo = $request->input('tipo_pessoa')) {
            $query->where('tipo_pessoa', $tipo);
        }

        return $this->sucesso($query->orderBy('nome')->paginate(15));
    }

    public function show(int $id): JsonResponse
    {
        $indicador = IndicadorPessoal::with([
            'estadoCivil', 'regimeBem', 'conjuge', 'capacidadeCivil',
            'nacionalidade', 'profissao', 'tipoEmpresa', 'porteEmpresa',
            'socios.socio',
        ])->find($id);

        if (!$indicador) {
            return $this->naoEncontrado('Indicador pessoal não encontrado');
        }

        $indisponibilidades = Indisponibilidade::with('partes.matriculas')
            ->whereNull('data_exclusao')
            ->whereNotIn('status', ['cancelada'])
            ->whereHas('partes', fn ($q) => $q->where('cpf_cnpj', $indicador->cpf_cnpj))
            ->orderByDesc('data_cadastro')
            ->get();

        return $this->sucesso(
            array_merge($indicador->toArray(), ['indisponibilidades' => $indisponibilidades])
        );
    }

    public function versoes(string $cpfCnpj): JsonResponse
    {
        $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);

        $versoes = IndicadorPessoal::where('cpf_cnpj', $cpfCnpj)
            ->orderByDesc('versao')
            ->get();

        if ($versoes->isEmpty()) {
            return $this->naoEncontrado('Nenhum registro encontrado para este CPF/CNPJ');
        }

        return $this->sucesso($versoes);
    }

    public function store(Request $request): JsonResponse
    {
        $resultado = $this->realizarValidacao($request);
        if ($resultado instanceof JsonResponse) {
            return $resultado;
        }

        $dados             = $resultado;
        $dados['cpf_cnpj'] = preg_replace('/\D/', '', $dados['cpf_cnpj']);

        if (IndicadorPessoal::atual()->where('cpf_cnpj', $dados['cpf_cnpj'])->exists()) {
            return $this->erro(
                'Já existe um cadastro ativo com este CPF/CNPJ. Use a edição para criar uma nova versão.',
                422
            );
        }

        $dados['versao']    = 1;
        $dados['is_atual']  = true;
        $dados['data_versao'] = now();

        $socios = $dados['socios'] ?? [];
        unset($dados['socios']);

        $indicador = IndicadorPessoal::create($dados);

        foreach ($socios as $socio) {
            $indicador->socios()->create($socio);
        }

        return $this->criado(
            $indicador->load(['estadoCivil', 'regimeBem', 'conjuge', 'capacidadeCivil',
                'nacionalidade', 'profissao', 'tipoEmpresa', 'porteEmpresa', 'socios.socio'])
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $indicador = IndicadorPessoal::atual()->find($id);

        if (!$indicador) {
            return $this->naoEncontrado('Indicador pessoal não encontrado');
        }

        $resultado = $this->realizarValidacao($request, $id);
        if ($resultado instanceof JsonResponse) {
            return $resultado;
        }

        $dados  = $resultado;
        $motivo = $dados['motivo_versao'];
        unset($dados['motivo_versao']);

        $dados['cpf_cnpj'] = preg_replace('/\D/', '', $dados['cpf_cnpj']);

        $socios = $dados['socios'] ?? [];
        unset($dados['socios']);

        $novaVersao = $indicador->criarNovaVersao($dados, $motivo);

        foreach ($socios as $socio) {
            $novaVersao->socios()->create($socio);
        }

        return $this->sucesso(
            $novaVersao->load(['estadoCivil', 'regimeBem', 'conjuge', 'capacidadeCivil',
                'nacionalidade', 'profissao', 'tipoEmpresa', 'porteEmpresa', 'socios.socio']),
            'Nova versão criada com sucesso'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $indicador = IndicadorPessoal::find($id);

        if (!$indicador) {
            return $this->naoEncontrado('Indicador pessoal não encontrado');
        }

        $indicador->delete();

        return $this->sucesso(null, 'Registro excluído com sucesso');
    }

    public function duplicar(Request $request, int $id): JsonResponse
    {
        $versao = IndicadorPessoal::find($id);

        if (!$versao) {
            return $this->naoEncontrado('Versão não encontrada');
        }

        $validated = $request->validate([
            'motivo_versao' => 'required|string|max:500',
        ]);

        $versaoAtual = IndicadorPessoal::atual()
            ->where('cpf_cnpj', $versao->cpf_cnpj)
            ->first();

        if (!$versaoAtual) {
            return $this->naoEncontrado('Versão atual não encontrada para este CPF/CNPJ');
        }

        $dados = $versao->only([
            'tipo_pessoa', 'ficha', 'nome', 'nome_fantasia',
            'rg', 'orgao_expedidor', 'data_expedicao_rg', 'data_nascimento', 'data_obito',
            'sexo', 'nome_pai', 'nome_mae',
            'estado_civil_id', 'regime_bem_id', 'data_casamento', 'anterior_lei_6515', 'conjuge_id',
            'capacidade_civil_id', 'representante_legal',
            'nacionalidade_id', 'naturalidade', 'profissao_id',
            'data_abertura', 'data_encerramento', 'sede', 'objeto_social',
            'tipo_empresa_id', 'porte_empresa_id', 'inscricao_estadual', 'inscricao_municipal',
            'pessoa_politicamente_exposta', 'servidor_publico', 'cargo_funcao', 'orgao_entidade',
            'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'pais',
            'observacoes', 'is_ativo',
        ]);

        $novaVersao = $versaoAtual->criarNovaVersao($dados, $validated['motivo_versao']);

        return $this->criado(
            $novaVersao->load(['estadoCivil', 'regimeBem', 'conjuge', 'capacidadeCivil',
                'nacionalidade', 'profissao', 'tipoEmpresa', 'porteEmpresa', 'socios.socio']),
            'Nova versão criada com sucesso'
        );
    }

    public function busca(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $q = $request->input('q');

        $resultados = IndicadorPessoal::atual()->ativo()
            ->where(function ($query) use ($q) {
                $query->where('nome', 'ilike', "%{$q}%")
                      ->orWhere('cpf_cnpj', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'nome', 'cpf_cnpj', 'tipo_pessoa', 'ficha']);

        return $this->sucesso($resultados);
    }

    private function realizarValidacao(Request $request, ?int $id = null): array|JsonResponse
    {
        $tipoPessoa = $request->input('tipo_pessoa', 'F');

        $fichaRule = ['nullable', 'string', 'max:20'];
        if ($id) {
            $fichaRule[] = Rule::unique('indicador_pessoal', 'ficha')
                ->where('is_atual', true)
                ->whereNull('data_exclusao')
                ->ignore($id);
        } else {
            $fichaRule[] = Rule::unique('indicador_pessoal', 'ficha')
                ->where('is_atual', true)
                ->whereNull('data_exclusao');
        }

        $conjugeRule = ['nullable', 'exists:indicador_pessoal,id'];
        if ($id) {
            $conjugeRule[] = Rule::notIn([$id]);
        }

        $rules = [
            'tipo_pessoa'               => 'required|in:F,J',
            'cpf_cnpj'                  => ['required', 'string', new ValidarDocumento($tipoPessoa)],
            'nome'                      => 'required|string|max:255',
            'ficha'                     => $fichaRule,
            'nome_fantasia'             => 'nullable|string|max:255',
            'rg'                        => 'nullable|string|max:30',
            'orgao_expedidor'           => 'nullable|string|max:20',
            'data_expedicao_rg'         => 'nullable|date',
            'data_nascimento'           => 'nullable|date',
            'data_obito'                => 'nullable|date',
            'sexo'                      => 'nullable|in:M,F,O',
            'nome_pai'                  => 'nullable|string|max:255',
            'nome_mae'                  => 'nullable|string|max:255',
            'estado_civil_id'           => 'nullable|exists:estado_civil,id',
            'regime_bem_id'             => 'nullable|exists:regime_bem,id',
            'data_casamento'            => 'nullable|date',
            'anterior_lei_6515'         => 'nullable|boolean',
            'conjuge_id'                => $conjugeRule,
            'capacidade_civil_id'       => 'nullable|exists:capacidade_civil,id',
            'representante_legal'       => 'nullable|string|max:255',
            'nacionalidade_id'          => 'nullable|exists:nacionalidade,id',
            'naturalidade'              => 'nullable|string|max:255',
            'profissao_id'              => 'nullable|exists:profissao,id',
            'data_abertura'             => 'nullable|date',
            'data_encerramento'         => 'nullable|date',
            'sede'                      => 'nullable|string|max:255',
            'objeto_social'             => 'nullable|string',
            'tipo_empresa_id'           => 'nullable|exists:tipo_empresa,id',
            'porte_empresa_id'          => 'nullable|exists:porte_empresa,id',
            'inscricao_estadual'        => 'nullable|string|max:50',
            'inscricao_municipal'       => 'nullable|string|max:50',
            'pessoa_politicamente_exposta' => 'nullable|boolean',
            'servidor_publico'          => 'nullable|boolean',
            'cargo_funcao'              => 'nullable|string|max:255',
            'orgao_entidade'            => 'nullable|string|max:255',
            'cep'                       => 'nullable|string|max:10',
            'logradouro'                => 'nullable|string|max:255',
            'numero'                    => 'nullable|string|max:20',
            'complemento'               => 'nullable|string|max:100',
            'bairro'                    => 'nullable|string|max:100',
            'cidade'                    => 'nullable|string|max:100',
            'uf'                        => 'nullable|string|size:2',
            'pais'                      => 'nullable|string|max:100',
            'observacoes'               => 'nullable|string',
            'is_ativo'                  => 'nullable|boolean',
            'socios'                    => 'nullable|array',
            'socios.*.socio_id'         => 'required_with:socios|exists:indicador_pessoal,id',
            'socios.*.participacao_percentual' => 'nullable|numeric|min:0|max:100',
            'socios.*.cargo'            => 'nullable|string|max:100',
            'socios.*.data_entrada'     => 'nullable|date',
            'socios.*.data_saida'       => 'nullable|date',
        ];

        if ($id !== null) {
            $rules['motivo_versao'] = 'required|string|max:500';
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($v) use ($request) {
            if ($request->estado_civil_id) {
                $ec = EstadoCivil::find($request->estado_civil_id);
                if ($ec && $ec->descricao === 'Casado(a)') {
                    if (!$request->regime_bem_id) {
                        $v->errors()->add('regime_bem_id', 'O regime de bens é obrigatório para estado civil Casado(a).');
                    }
                    if (!$request->conjuge_id) {
                        $v->errors()->add('conjuge_id', 'O cônjuge é obrigatório para estado civil Casado(a).');
                    }
                }
            }

            if ($request->capacidade_civil_id) {
                $cc = CapacidadeCivil::find($request->capacidade_civil_id);
                if ($cc && in_array($cc->descricao, ['Relativamente Incapaz (16 a 18 anos)', 'Absolutamente Incapaz'])) {
                    if (!$request->representante_legal) {
                        $v->errors()->add('representante_legal', 'O representante legal é obrigatório para esta capacidade civil.');
                    }
                }
            }
        });

        if ($validator->fails()) {
            return $this->erro('Dados inválidos', 422, $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
