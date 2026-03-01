<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Permissao;
use App\Models\User;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        // Criar grupos padrão
        $grupos = [
            [
                'nome'      => 'Administrador',
                'descricao' => 'Acesso total ao sistema — bypass automático de permissões',
            ],
            [
                'nome'      => 'Registrador',
                'descricao' => 'Operacional completo — protocolo, atos, financeiro',
            ],
            [
                'nome'      => 'Atendente',
                'descricao' => 'Criação e acompanhamento de protocolos e contratos',
            ],
            [
                'nome'      => 'Caixa',
                'descricao' => 'Operações financeiras e de caixa',
            ],
            [
                'nome'      => 'Consulta',
                'descricao' => 'Somente leitura em todos os módulos',
            ],
        ];

        foreach ($grupos as $dados) {
            Grupo::updateOrCreate(['nome' => $dados['nome']], $dados);
        }

        // Vincular permissões ao grupo Registrador
        $registrador = Grupo::where('nome', 'Registrador')->first();
        if ($registrador) {
            $permissoesRegistrador = Permissao::whereIn('nome', [
                'PROTOCOLO_LISTAR', 'PROTOCOLO_CRIAR', 'PROTOCOLO_VISUALIZAR', 'PROTOCOLO_EDITAR',
                'PROTOCOLO_CANCELAR', 'PROTOCOLO_PAGAR', 'PROTOCOLO_ESTORNAR', 'PROTOCOLO_ISENTAR',
                'CONTRATO_LISTAR', 'CONTRATO_CRIAR', 'CONTRATO_VISUALIZAR', 'CONTRATO_EDITAR', 'CONTRATO_CONCLUIR',
                'RECIBO_LISTAR', 'RECIBO_VISUALIZAR', 'RECIBO_GERAR',
                'ARQUIVO_LISTAR', 'ARQUIVO_VISUALIZAR',
                'ATO_LISTAR', 'ATO_VISUALIZAR',
                'INDICADOR_PESSOAL_LISTAR', 'INDICADOR_PESSOAL_CRIAR', 'INDICADOR_PESSOAL_VISUALIZAR', 'INDICADOR_PESSOAL_EDITAR',
                'INDISPONIBILIDADE_LISTAR', 'INDISPONIBILIDADE_CRIAR', 'INDISPONIBILIDADE_VISUALIZAR',
                'TRANSACAO_LISTAR', 'TRANSACAO_CRIAR', 'TRANSACAO_VISUALIZAR',
                'CAIXA_LISTAR', 'CAIXA_MOVIMENTO_LISTAR',
            ])->pluck('id');
            $registrador->permissoes()->sync($permissoesRegistrador);
        }

        // Vincular permissões ao grupo Atendente
        $atendente = Grupo::where('nome', 'Atendente')->first();
        if ($atendente) {
            $permissoesAtendente = Permissao::whereIn('nome', [
                'PROTOCOLO_LISTAR', 'PROTOCOLO_CRIAR', 'PROTOCOLO_VISUALIZAR', 'PROTOCOLO_EDITAR',
                'CONTRATO_LISTAR', 'CONTRATO_CRIAR', 'CONTRATO_VISUALIZAR', 'CONTRATO_EDITAR',
                'INDICADOR_PESSOAL_LISTAR', 'INDICADOR_PESSOAL_CRIAR', 'INDICADOR_PESSOAL_VISUALIZAR', 'INDICADOR_PESSOAL_EDITAR',
                'INDISPONIBILIDADE_LISTAR', 'INDISPONIBILIDADE_CRIAR', 'INDISPONIBILIDADE_VISUALIZAR',
                'RECIBO_LISTAR', 'RECIBO_VISUALIZAR',
            ])->pluck('id');
            $atendente->permissoes()->sync($permissoesAtendente);
        }

        // Vincular permissões ao grupo Caixa
        $caixa = Grupo::where('nome', 'Caixa')->first();
        if ($caixa) {
            $permissoesCaixa = Permissao::whereIn('nome', [
                'CAIXA_LISTAR', 'CAIXA_ABRIR', 'CAIXA_FECHAR', 'CAIXA_CONFERIR', 'CAIXA_SANGRIA',
                'CAIXA_MOVIMENTO_LISTAR', 'CAIXA_OPERACAO_LISTAR',
                'TRANSACAO_LISTAR', 'TRANSACAO_CRIAR', 'TRANSACAO_VISUALIZAR',
                'FORMA_PAGAMENTO_LISTAR', 'MEIO_PAGAMENTO_LISTAR',
                'PROTOCOLO_PAGAR', 'PROTOCOLO_ESTORNAR',
            ])->pluck('id');
            $caixa->permissoes()->sync($permissoesCaixa);
        }

        // Vincular permissões ao grupo Consulta (somente listar/visualizar)
        $consulta = Grupo::where('nome', 'Consulta')->first();
        if ($consulta) {
            $permissoesConsulta = Permissao::where('nome', 'like', '%_LISTAR')
                ->orWhere('nome', 'like', '%_VISUALIZAR')
                ->pluck('id');
            $consulta->permissoes()->sync($permissoesConsulta);
        }

        // Vincular o primeiro usuário ao grupo Administrador
        $admin = Grupo::where('nome', 'Administrador')->first();
        $primeirousuario = User::orderBy('data_cadastro')->first();
        if ($admin && $primeirousuario) {
            $admin->usuarios()->syncWithoutDetaching([$primeirousuario->id]);
        }
    }
}
