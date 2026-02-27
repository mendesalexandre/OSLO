# Sistema de Recibos - Guia Rápido

## 🎯 Resumo

Sistema que permite gerar recibos vinculados a pagamentos de protocolos, com opções de:
- Recibo individual por pagamento
- Recibo agrupando múltiplos pagamentos
- Verificação automática de duplicidade

---

## 📍 Como Usar

### 1. Acessar Financeiro do Protocolo

Navegue até: **Protocolo → Financeiro**

### 2. Opções de Geração

#### Opção A: Recibo de Pagamento Individual

1. Localize o pagamento na tabela
2. Clique no botão **🖨️** (printer icon) na coluna de Ações
3. Confirme se deseja visualizar

#### Opção B: Recibo de Múltiplos Pagamentos

1. Marque os checkboxes dos pagamentos desejados
2. Clique no botão **"Gerar Recibo (N)"** no topo da tabela
3. Confirme se deseja visualizar

#### Opção C: Recibo de Todos os Pagamentos

1. Não selecione nenhum pagamento
2. Clique no botão **"Gerar Recibo (Todos)"**
3. Confirme se deseja visualizar

---

## 🔍 Visualização e Download

### Modal de Preview

Após gerar, você pode:
- **Visualizar**: Preview HTML do recibo
- **Imprimir**: Usar Ctrl+P para imprimir
- **Fechar**: Voltar ao protocolo

### Lista de Recibos

Acesse: **Menu → Recibos**

Ações disponíveis:
- 👁️ **Preview HTML**: Visualizar no navegador
- 📥 **Download PDF**: Baixar arquivo PDF
- ℹ️ **Ver Detalhes**: Informações do recibo

---

## ⚠️ Importante

### ✅ O Sistema VERIFICA Duplicatas

- Se você tentar gerar recibo com **exatamente os mesmos pagamentos**, o sistema retorna o recibo existente
- Mensagem: "Recibo já existe para estes pagamentos"

### Exemplo:

```
Pagamento A + B → Cria Recibo #1
Pagamento A + B → Retorna Recibo #1 ✅ (não cria duplicado)
Pagamento A     → Cria Recibo #2 ✅ (combinação diferente)
Pagamento C     → Cria Recibo #3 ✅
```

### ❌ Só Pode Gerar Recibo Se:

- Existir pelo menos 1 pagamento **confirmado**
- O pagamento tiver `status = 'confirmado'`
- OU o protocolo for **isento**

---

## 🎨 Interface

### Botão Principal

```
┌─────────────────────────────────────┐
│ 🖨️ Gerar Recibo (2)                 │  ← Mostra quantos selecionados
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ 🖨️ Gerar Recibo (Todos)             │  ← Se nada selecionado
└─────────────────────────────────────┘
```

### Tabela de Pagamentos

```
┌────────────┬──────────┬───────┬────────────────────────┐
│ [✓] Data   │ Forma    │ Valor │ Ações                  │
├────────────┼──────────┼───────┼────────────────────────┤
│ [✓] 08/02  │ Dinheiro │ 100   │ 🖨️ 🔄 🗑️              │
│ [✓] 08/02  │ PIX      │ 200   │ 🖨️ 🔄 🗑️              │
│ [ ] 09/02  │ Cartão   │ 150   │ 🖨️ 🔄 🗑️              │
└────────────┴──────────┴───────┴────────────────────────┘

🖨️ = Gerar Recibo Individual
🔄 = Estornar
🗑️ = Excluir (Admin)
```

---

## 📋 Formato do Recibo

### Número

```
2026/R000001
│   │└─────── Sequencial (6 dígitos)
│   └──────── Indicador "R" de Recibo
└──────────── Ano
```

### Conteúdo

1. **Cabeçalho**
   - Número do recibo
   - Data de emissão
   - Protocolo vinculado
   - Solicitante

2. **Corpo**
   - Texto: "Recebi(emos) de [Nome] a quantia de..."
   - Tabela de atos/serviços prestados
   - Tabela de formas de pagamento recebidas

3. **Totais**
   - Valor total dos serviços
   - Valor isento (se houver)
   - **VALOR PAGO** (destaque)

4. **Rodapé**
   - Texto legal
   - Data/hora de emissão
   - Linha para assinatura

---

## 🔐 Permissões Necessárias

| Ação | Permissão |
|------|-----------|
| Ver aba Financeiro | `PROTOCOLO_FINANCEIRO_VISUALIZAR` |
| Gerar recibo | `RECIBO_GERAR` |
| Ver lista de recibos | `RECIBO_LISTAR` |
| Visualizar recibo | `RECIBO_VISUALIZAR` |
| Download PDF | `RECIBO_IMPRIMIR` |

---

## 🆘 Problemas Comuns

### "Não há pagamentos confirmados"

**Causa:** Todos os pagamentos estão com status diferente de "confirmado"

**Solução:** Confirme os pagamentos primeiro

### "Recibo já existe"

**Causa:** Você está tentando gerar recibo com exatamente os mesmos pagamentos

**Solução:** Isso é normal! O sistema está evitando duplicatas. Clique "Sim" para visualizar o recibo existente.

### Não vejo o botão de gerar recibo

**Causa:** Falta permissão ou não há valor pago

**Solução:**
1. Verifique se tem permissão `RECIBO_GERAR`
2. Verifique se há pagamentos confirmados no protocolo

---

## 💡 Dicas

1. **Recibos Parciais**: Você pode gerar múltiplos recibos para o mesmo protocolo, cada um com pagamentos diferentes

2. **Organização**: Use recibos individuais para pagamentos parcelados

3. **Auditoria**: Todos os recibos são auditados. Você pode ver quem gerou e quando

4. **Filtros**: Na lista de recibos, use filtros por número, data ou protocolo

5. **Impressão**: O botão "Imprimir" no modal já formata a página para impressão (sem cabeçalho/rodapé do modal)

---

## 📞 Suporte

Para dúvidas ou problemas, consulte a **documentação completa** em:
`docs/SISTEMA_RECIBOS.md`
