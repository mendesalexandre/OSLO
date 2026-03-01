<?php

namespace Database\Seeders;

use App\Models\Permissao;
use Illuminate\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    public function run(): void
    {
        $permissoes = [
            // Protocolo
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_LISTAR',           'descricao' => 'Listar protocolos'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_CRIAR',            'descricao' => 'Criar protocolos'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_VISUALIZAR',       'descricao' => 'Visualizar protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_EDITAR',           'descricao' => 'Editar protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_CANCELAR',         'descricao' => 'Cancelar protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_PAGAR',            'descricao' => 'Registrar pagamento no protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_ESTORNAR',         'descricao' => 'Estornar pagamento no protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_PAGAMENTO_EXCLUIR','descricao' => 'Excluir pagamento no protocolo'],
            ['modulo' => 'Protocolo', 'nome' => 'PROTOCOLO_ISENTAR',          'descricao' => 'Registrar isenção no protocolo'],

            // Contrato
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_LISTAR',    'descricao' => 'Listar contratos'],
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_CRIAR',     'descricao' => 'Criar contrato'],
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_VISUALIZAR','descricao' => 'Visualizar contrato'],
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_EDITAR',    'descricao' => 'Editar contrato'],
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_CONCLUIR',  'descricao' => 'Concluir contrato'],
            ['modulo' => 'Contrato', 'nome' => 'CONTRATO_CANCELAR',  'descricao' => 'Cancelar contrato'],

            // Recibo
            ['modulo' => 'Recibo', 'nome' => 'RECIBO_LISTAR',    'descricao' => 'Listar recibos'],
            ['modulo' => 'Recibo', 'nome' => 'RECIBO_VISUALIZAR','descricao' => 'Visualizar recibo'],
            ['modulo' => 'Recibo', 'nome' => 'RECIBO_GERAR',     'descricao' => 'Gerar recibo'],

            // Arquivo
            ['modulo' => 'Arquivo', 'nome' => 'ARQUIVO_LISTAR',    'descricao' => 'Listar arquivos'],
            ['modulo' => 'Arquivo', 'nome' => 'ARQUIVO_VISUALIZAR','descricao' => 'Visualizar arquivo'],
            ['modulo' => 'Arquivo', 'nome' => 'ARQUIVO_EXCLUIR',   'descricao' => 'Excluir arquivo'],

            // Ato
            ['modulo' => 'Ato', 'nome' => 'ATO_LISTAR',    'descricao' => 'Listar atos'],
            ['modulo' => 'Ato', 'nome' => 'ATO_CRIAR',     'descricao' => 'Criar ato'],
            ['modulo' => 'Ato', 'nome' => 'ATO_VISUALIZAR','descricao' => 'Visualizar ato'],
            ['modulo' => 'Ato', 'nome' => 'ATO_EDITAR',    'descricao' => 'Editar ato'],
            ['modulo' => 'Ato', 'nome' => 'ATO_EXCLUIR',   'descricao' => 'Excluir ato'],

            // Indicador Pessoal
            ['modulo' => 'Indicador Pessoal', 'nome' => 'INDICADOR_PESSOAL_LISTAR',    'descricao' => 'Listar indicadores pessoais'],
            ['modulo' => 'Indicador Pessoal', 'nome' => 'INDICADOR_PESSOAL_CRIAR',     'descricao' => 'Criar indicador pessoal'],
            ['modulo' => 'Indicador Pessoal', 'nome' => 'INDICADOR_PESSOAL_VISUALIZAR','descricao' => 'Visualizar indicador pessoal'],
            ['modulo' => 'Indicador Pessoal', 'nome' => 'INDICADOR_PESSOAL_EDITAR',    'descricao' => 'Editar indicador pessoal'],
            ['modulo' => 'Indicador Pessoal', 'nome' => 'INDICADOR_PESSOAL_EXCLUIR',   'descricao' => 'Excluir indicador pessoal'],

            // Indisponibilidade
            ['modulo' => 'Indisponibilidade', 'nome' => 'INDISPONIBILIDADE_LISTAR',    'descricao' => 'Listar indisponibilidades'],
            ['modulo' => 'Indisponibilidade', 'nome' => 'INDISPONIBILIDADE_CRIAR',     'descricao' => 'Criar indisponibilidade'],
            ['modulo' => 'Indisponibilidade', 'nome' => 'INDISPONIBILIDADE_VISUALIZAR','descricao' => 'Visualizar indisponibilidade'],
            ['modulo' => 'Indisponibilidade', 'nome' => 'INDISPONIBILIDADE_EDITAR',    'descricao' => 'Editar indisponibilidade'],
            ['modulo' => 'Indisponibilidade', 'nome' => 'INDISPONIBILIDADE_CANCELAR',  'descricao' => 'Cancelar indisponibilidade'],

            // Financeiro — Forma de Pagamento
            ['modulo' => 'Financeiro', 'nome' => 'FORMA_PAGAMENTO_LISTAR',    'descricao' => 'Listar formas de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'FORMA_PAGAMENTO_CRIAR',     'descricao' => 'Criar forma de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'FORMA_PAGAMENTO_VISUALIZAR','descricao' => 'Visualizar forma de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'FORMA_PAGAMENTO_EDITAR',    'descricao' => 'Editar forma de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'FORMA_PAGAMENTO_EXCLUIR',   'descricao' => 'Excluir forma de pagamento'],

            // Financeiro — Meio de Pagamento
            ['modulo' => 'Financeiro', 'nome' => 'MEIO_PAGAMENTO_LISTAR',    'descricao' => 'Listar meios de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'MEIO_PAGAMENTO_CRIAR',     'descricao' => 'Criar meio de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'MEIO_PAGAMENTO_VISUALIZAR','descricao' => 'Visualizar meio de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'MEIO_PAGAMENTO_EDITAR',    'descricao' => 'Editar meio de pagamento'],
            ['modulo' => 'Financeiro', 'nome' => 'MEIO_PAGAMENTO_EXCLUIR',   'descricao' => 'Excluir meio de pagamento'],

            // Financeiro — Transação
            ['modulo' => 'Financeiro', 'nome' => 'TRANSACAO_LISTAR',    'descricao' => 'Listar transações'],
            ['modulo' => 'Financeiro', 'nome' => 'TRANSACAO_CRIAR',     'descricao' => 'Criar transação'],
            ['modulo' => 'Financeiro', 'nome' => 'TRANSACAO_VISUALIZAR','descricao' => 'Visualizar transação'],
            ['modulo' => 'Financeiro', 'nome' => 'TRANSACAO_EDITAR',    'descricao' => 'Editar transação'],
            ['modulo' => 'Financeiro', 'nome' => 'TRANSACAO_EXCLUIR',   'descricao' => 'Excluir transação'],

            // Caixa
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_LISTAR',           'descricao' => 'Listar caixas'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_ABRIR',            'descricao' => 'Abrir caixa'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_FECHAR',           'descricao' => 'Fechar caixa'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_CONFERIR',         'descricao' => 'Conferir caixa'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_SANGRIA',          'descricao' => 'Realizar sangria'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_MOVIMENTO_LISTAR', 'descricao' => 'Listar movimentos de caixa'],
            ['modulo' => 'Caixa', 'nome' => 'CAIXA_OPERACAO_LISTAR',  'descricao' => 'Listar operações de caixa'],

            // Administração
            ['modulo' => 'Administração', 'nome' => 'NATUREZA_LISTAR',         'descricao' => 'Listar naturezas'],
            ['modulo' => 'Administração', 'nome' => 'NATUREZA_CRIAR',          'descricao' => 'Criar natureza'],
            ['modulo' => 'Administração', 'nome' => 'NATUREZA_EDITAR',         'descricao' => 'Editar natureza'],
            ['modulo' => 'Administração', 'nome' => 'NATUREZA_EXCLUIR',        'descricao' => 'Excluir natureza'],
            ['modulo' => 'Administração', 'nome' => 'DOMINIO_LISTAR',          'descricao' => 'Listar domínios'],
            ['modulo' => 'Administração', 'nome' => 'DOMINIO_CRIAR',           'descricao' => 'Criar domínio'],
            ['modulo' => 'Administração', 'nome' => 'DOMINIO_EDITAR',          'descricao' => 'Editar domínio'],
            ['modulo' => 'Administração', 'nome' => 'DOMINIO_EXCLUIR',         'descricao' => 'Excluir domínio'],
            ['modulo' => 'Administração', 'nome' => 'FERIADO_LISTAR',          'descricao' => 'Listar feriados'],
            ['modulo' => 'Administração', 'nome' => 'TABELA_CUSTA_LISTAR',     'descricao' => 'Listar tabelas de custa'],
            ['modulo' => 'Administração', 'nome' => 'ESTADO_LISTAR',           'descricao' => 'Listar estados'],
            ['modulo' => 'Administração', 'nome' => 'CIDADE_LISTAR',           'descricao' => 'Listar cidades'],
            ['modulo' => 'Administração', 'nome' => 'CATEGORIA_LISTAR',        'descricao' => 'Listar categorias'],
            ['modulo' => 'Administração', 'nome' => 'CATEGORIA_CRIAR',         'descricao' => 'Criar categoria'],
            ['modulo' => 'Administração', 'nome' => 'CATEGORIA_EDITAR',        'descricao' => 'Editar categoria'],
            ['modulo' => 'Administração', 'nome' => 'CATEGORIA_EXCLUIR',       'descricao' => 'Excluir categoria'],
            ['modulo' => 'Administração', 'nome' => 'GRUPO_LISTAR',            'descricao' => 'Listar grupos'],
            ['modulo' => 'Administração', 'nome' => 'GRUPO_CRIAR',             'descricao' => 'Criar grupo'],
            ['modulo' => 'Administração', 'nome' => 'GRUPO_EDITAR',            'descricao' => 'Editar grupo'],
            ['modulo' => 'Administração', 'nome' => 'GRUPO_EXCLUIR',           'descricao' => 'Excluir grupo'],
            ['modulo' => 'Administração', 'nome' => 'PERMISSAO_LISTAR',        'descricao' => 'Listar permissões'],
            ['modulo' => 'Administração', 'nome' => 'USUARIO_PERMISSAO_LISTAR','descricao' => 'Gerenciar permissões de usuários'],
            ['modulo' => 'Administração', 'nome' => 'AUDITORIA_LISTAR',        'descricao' => 'Visualizar auditoria'],
            ['modulo' => 'Administração', 'nome' => 'DOI_LISTAR',              'descricao' => 'Listar DOI'],
        ];

        foreach ($permissoes as $perm) {
            Permissao::updateOrCreate(
                ['nome' => $perm['nome']],
                $perm
            );
        }
    }
}
