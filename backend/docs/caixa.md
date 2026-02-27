# Tabela

etapa

    id (auto increment)
    is_ativo (bool)
    nome (string 100)
    descricao (255)
    saldo_inicial
    saldo_atual
    data_cadastro
    data_alteracao
    data_exclusao

transacao:

-   tipo (ENTRADA/SAIDA)
-   natureza (CONTA_PAGAR, CONTA_RECEBER, TRANSFERENCIA, AJUSTE)
-   categoria_id (FK -> categorias)
-   ...

categorias:

-   id
-   nome (Saúde, Educação, Alimentação...)
-   tipo (DESPESA/RECEITA)
-   cor
-   icone
