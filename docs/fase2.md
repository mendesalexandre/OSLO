# OSLO — Fases 06 e 07 (v2) — Transações Simples e Auditoria

---

## Phase 06 — Catálogo de Transações e Configuração

### Objetivo

Criar catálogos auxiliares simples (sem ENUM no banco) e preparar estrutura para auditoria de todas as operações.

### Backend

#### Migrations

##### `tipo_transacao`

```php
Schema::create('tipo_transacao', function (Blueprint $table) {
    $table->id();
    $table->string('descricao', 255)->unique();
    $table->string('tipo', 50); // 'entrada', 'saida', 'caixa' (valores em string)
    $table->string('icone', 50)->nullable();
    $table->string('cor', 10)->nullable();
    $table->boolean('is_ativo')->default(true);
    $table->timestamps();
    $table->timestamp('data_exclusao')->nullable();

    $table->index('tipo');
    $table->index('is_ativo');
});
```

**Seeds:**

```php
DB::table('tipo_transacao')->insert([
    // ENTRADAS
    ['descricao' => 'Depósito', 'tipo' => 'entrada', 'icone' => 'mdi-plus-circle', 'cor' => '#4CAF50', 'is_ativo' => true],
    ['descricao' => 'Transferência Recebida', 'tipo' => 'entrada', 'icone' => 'mdi-arrow-left', 'cor' => '#4CAF50', 'is_ativo' => true],
    ['descricao' => 'Devolução', 'tipo' => 'entrada', 'icone' => 'mdi-undo', 'cor' => '#4CAF50', 'is_ativo' => true],

    // SAÍDAS
    ['descricao' => 'Saque', 'tipo' => 'saida', 'icone' => 'mdi-minus-circle', 'cor' => '#F44336', 'is_ativo' => true],
    ['descricao' => 'Transferência Enviada', 'tipo' => 'saida', 'icone' => 'mdi-arrow-right', 'cor' => '#F44336', 'is_ativo' => true],
    ['descricao' => 'Pagamento', 'tipo' => 'saida', 'icone' => 'mdi-cash-multiple', 'cor' => '#F44336', 'is_ativo' => true],

    // CAIXA
    ['descricao' => 'Ajuste de Caixa', 'tipo' => 'caixa', 'icone' => 'mdi-plus-minus', 'cor' => '#2196F3', 'is_ativo' => true],
]);
```

##### `motivo_transacao`

```php
Schema::create('motivo_transacao', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tipo_transacao_id')->constrained('tipo_transacao')->cascadeOnDelete();
    $table->string('descricao', 255);
    $table->text('descricao_expandida')->nullable();
    $table->boolean('exige_documento')->default(false);
    $table->boolean('exige_beneficiario')->default(false);
    $table->boolean('is_ativo')->default(true);
    $table->timestamps();
    $table->timestamp('data_exclusao')->nullable();

    $table->index('tipo_transacao_id');
    $table->unique(['tipo_transacao_id', 'descricao']);
});
```

**Seeds:**

```php
// ENTRADAS
$this->seed([
    ['tipo_transacao_id' => 1, 'descricao' => 'Depósito em Conta'],
    ['tipo_transacao_id' => 2, 'descricao' => 'Transferência de Pessoa Física'],
    ['tipo_transacao_id' => 2, 'descricao' => 'Transferência de Pessoa Jurídica'],
    ['tipo_transacao_id' => 3, 'descricao' => 'Devolução de Saque'],
    ['tipo_transacao_id' => 3, 'descricao' => 'Devolução de Pagamento'],

    // SAÍDAS
    ['tipo_transacao_id' => 4, 'descricao' => 'Saque em Espécie'],
    ['tipo_transacao_id' => 4, 'descricao' => 'Saque via Cartão'],
    ['tipo_transacao_id' => 5, 'descricao' => 'Transferência para Pessoa Física'],
    ['tipo_transacao_id' => 5, 'descricao' => 'Transferência para Pessoa Jurídica'],
    ['tipo_transacao_id' => 6, 'descricao' => 'Pagamento de Despesa'],
    ['tipo_transacao_id' => 6, 'descricao' => 'Pagamento de Honorários'],

    // CAIXA
    ['tipo_transacao_id' => 7, 'descricao' => 'Ajuste por Erro'],
    ['tipo_transacao_id' => 7, 'descricao' => 'Ajuste por Conciliação'],
]);
```

##### `banco`

```php
Schema::create('banco', function (Blueprint $table) {
    $table->id();
    $table->integer('codigo_bcb')->nullable()->unique();
    $table->string('nome', 255)->unique();
    $table->string('sigla', 20);
    $table->boolean('is_ativo')->default(true);
    $table->timestamps();
    $table->timestamp('data_exclusao')->nullable();

    $table->index('is_ativo');
});
```

**Seeds:** Caixa, Banco do Brasil, Itaú, Bradesco, Santander, Inter, Nubank, etc.

#### Enums (PHP)

Criar arquivo `app/Enums/TipoTransacaoEnum.php`:

```php
<?php

namespace App\Enums;

enum TipoTransacaoEnum: string
{
    case ENTRADA = 'entrada';
    case SAIDA = 'saida';
    case CAIXA = 'caixa';

    public function label(): string
    {
        return match($this) {
            self::ENTRADA => 'Entrada',
            self::SAIDA => 'Saída',
            self::CAIXA => 'Caixa',
        };
    }

    public function cor(): string
    {
        return match($this) {
            self::ENTRADA => '#4CAF50',
            self::SAIDA => '#F44336',
            self::CAIXA => '#2196F3',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
```

Criar arquivo `app/Enums/SituacaoTransacaoEnum.php`:

```php
<?php

namespace App\Enums;

enum SituacaoTransacaoEnum: string
{
    case PENDENTE = 'pendente';
    case CONFIRMADA = 'confirmada';
    case LIQUIDADA = 'liquidada';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::CONFIRMADA => 'Confirmada',
            self::LIQUIDADA => 'Liquidada',
            self::CANCELADA => 'Cancelada',
        };
    }

    public function classe(): string
    {
        return match($this) {
            self::PENDENTE => 'warning',
            self::CONFIRMADA => 'info',
            self::LIQUIDADA => 'success',
            self::CANCELADA => 'error',
        };
    }
}
```

#### Models

```php
// app/Models/TipoTransacao.php
class TipoTransacao extends Model
{
    protected $table = 'tipo_transacao';
    protected $fillable = ['descricao', 'tipo', 'icone', 'cor', 'is_ativo'];
    protected $casts = [
        'is_ativo' => 'boolean',
    ];

    public function motivosTransacao()
    {
        return $this->hasMany(MotivoTransacao::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true)->whereNull('data_exclusao');
    }

    public function scopePorTipo($query, string $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}

// app/Models/MotivoTransacao.php
class MotivoTransacao extends Model
{
    protected $table = 'motivo_transacao';
    protected $fillable = ['tipo_transacao_id', 'descricao', 'descricao_expandida', 'exige_documento', 'exige_beneficiario', 'is_ativo'];
    protected $casts = [
        'is_ativo' => 'boolean',
        'exige_documento' => 'boolean',
        'exige_beneficiario' => 'boolean',
    ];

    public function tipoTransacao()
    {
        return $this->belongsTo(TipoTransacao::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true)->whereNull('data_exclusao');
    }
}

// app/Models/Banco.php
class Banco extends Model
{
    protected $table = 'banco';
    protected $fillable = ['codigo_bcb', 'nome', 'sigla', 'is_ativo'];
    protected $casts = [
        'is_ativo' => 'boolean',
    ];

    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true)->whereNull('data_exclusao');
    }
}
```

#### Endpoints

```php
// routes/api.php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Catálogos
    Route::get('tipos-transacao', [TipoTransacaoController::class, 'index']);
    Route::get('tipos-transacao/{tipo}/motivos', [MotivoTransacaoController::class, 'porTipo']);
    Route::get('bancos', [BancoController::class, 'index']);
});
```

```php
// app/Http/Controllers/TipoTransacaoController.php
class TipoTransacaoController extends Controller
{
    public function index()
    {
        $tipos = TipoTransacao::ativo()
            ->with('motivosTransacao')
            ->get();

        return response()->json($tipos);
    }
}

// app/Http/Controllers/MotivoTransacaoController.php
class MotivoTransacaoController extends Controller
{
    public function porTipo($tipoId)
    {
        $motivos = MotivoTransacao::ativo()
            ->where('tipo_transacao_id', $tipoId)
            ->get();

        return response()->json($motivos);
    }
}

// app/Http/Controllers/BancoController.php
class BancoController extends Controller
{
    public function index()
    {
        $bancos = Banco::ativo()->get();
        return response()->json($bancos);
    }
}
```

#### Testes (Pest)

```php
test('pode listar tipos de transação', function () {
    $tipos = TipoTransacao::ativo()->get();
    expect($tipos)->toHaveCount(7); // 3 entrada + 3 saida + 1 caixa
});

test('pode buscar motivos por tipo', function () {
    $tipo = TipoTransacao::where('tipo', 'entrada')->first();
    $motivos = MotivoTransacao::ativo()
        ->where('tipo_transacao_id', $tipo->id)
        ->get();

    expect($motivos->count())->toBeGreaterThan(0);
});

test('endpoint retorna tipos com motivos', function () {
    $response = $this->getJson('/api/v1/tipos-transacao');
    $response->assertSuccessful()
        ->assertJsonStructure([
            '*' => ['id', 'descricao', 'tipo', 'motivos_transacao']
        ]);
});
```

### Frontend

#### Store (Pinia) — `stores/catalogo/index.js`

```javascript
import { defineStore } from "pinia";

export const useCatalogo = defineStore("catalogo", {
  state: () => ({
    tipos: [],
    motivos: {},
    bancos: [],
    loading: false,
  }),

  getters: {
    obterMotivosPorTipo: (state) => (tipoId) => {
      return state.motivos[tipoId] || [];
    },

    obterTipoPorId: (state) => (id) => {
      return state.tipos.find((t) => t.id === id);
    },
  },

  actions: {
    async fetchTipos() {
      this.loading = true;
      try {
        const { data } = await axios.get("/api/v1/tipos-transacao");
        this.tipos = data;
      } catch (error) {
        console.error("Erro ao buscar tipos:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchMotivosPorTipo(tipoId) {
      if (this.motivos[tipoId]) return; // Cache

      try {
        const { data } = await axios.get(
          `/api/v1/tipos-transacao/${tipoId}/motivos`,
        );
        this.motivos[tipoId] = data;
      } catch (error) {
        console.error("Erro ao buscar motivos:", error);
      }
    },

    async fetchBancos() {
      try {
        const { data } = await axios.get("/api/v1/bancos");
        this.bancos = data;
      } catch (error) {
        console.error("Erro ao buscar bancos:", error);
      }
    },
  },
});
```

#### Componente Global Modal

Criar `components/global/Modal.vue`:

```vue
<template>
  <q-dialog
    v-model="modalOpen"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <q-card style="min-width: 500px">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">{{ title }}</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-separator />

      <q-card-section class="q-pa-md">
        <slot />
      </q-card-section>

      <q-separator />

      <q-card-actions align="right">
        <slot name="actions">
          <q-btn label="Cancelar" flat v-close-popup />
          <q-btn label="Salvar" color="primary" @click="$emit('save')" />
        </slot>
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  modelValue: Boolean,
  title: {
    type: String,
    default: "Modal",
  },
});

const emit = defineEmits(["update:modelValue", "save"]);

const modalOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});
</script>
```

Registrar em `main.js`:

```javascript
import Modal from "@/components/global/Modal.vue";

app.component("Modal", Modal);
```

#### Componentes Reutilizáveis

`components/transacao/SelectTipoTransacao.vue`:

```vue
<template>
  <q-select
    v-model="tipoSelecionado"
    :options="catalogoStore.tipos"
    option-value="id"
    option-label="descricao"
    label="Tipo de Transação"
    outlined
    dense
    @update:model-value="handleTipoChange"
  >
    <template v-slot:prepend>
      <q-icon :name="tipoSelecionado?.icone" :color="tipoSelecionado?.cor" />
    </template>
  </q-select>
</template>

<script setup>
import { ref, watch } from "vue";
import { useCatalogo } from "@/stores/catalogo";

const props = defineProps({
  modelValue: Object,
  tipo: String, // filtro: 'entrada', 'saida', 'caixa'
});

const emit = defineEmits(["update:modelValue"]);

const catalogoStore = useCatalogo();
const tipoSelecionado = ref(props.modelValue);

watch(
  () => props.modelValue,
  (val) => {
    tipoSelecionado.value = val;
  },
);

const handleTipoChange = (val) => {
  emit("update:modelValue", val);
  catalogoStore.fetchMotivosPorTipo(val.id);
};

await catalogoStore.fetchTipos();
</script>
```

`components/transacao/SelectMotivoTransacao.vue`:

```vue
<template>
  <q-select
    v-model="motivoSelecionado"
    :options="motivos"
    option-value="id"
    option-label="descricao"
    label="Motivo"
    outlined
    dense
    :disable="!tipoTransacao"
  />
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useCatalogo } from "@/stores/catalogo";

const props = defineProps({
  tipoTransacao: Object,
  modelValue: Object,
});

const emit = defineEmits(["update:modelValue"]);

const catalogoStore = useCatalogo();
const motivoSelecionado = ref(props.modelValue);

const motivos = computed(() => {
  if (!props.tipoTransacao) return [];
  return catalogoStore.obterMotivosPorTipo(props.tipoTransacao.id);
});

watch(
  () => props.modelValue,
  (val) => {
    motivoSelecionado.value = val;
  },
);

watch(
  () => props.tipoTransacao,
  () => {
    motivoSelecionado.value = null;
  },
);

const handleChange = (val) => {
  emit("update:modelValue", val);
};
</script>
```

---

## Phase 07 — Transações Simples com Auditoria

### Objetivo

Implementar transações simples (entrada/saida/caixa) com auditoria integrada e Materialized Views para performance.

### Backend

#### Migrations

##### `transacao`

```php
Schema::create('transacao', function (Blueprint $table) {
    $table->id();
    $table->foreignId('indicador_pessoal_id')->constrained('indicador_pessoal')->cascadeOnDelete();
    $table->foreignId('tipo_transacao_id')->constrained('tipo_transacao');
    $table->foreignId('motivo_transacao_id')->nullable()->constrained('motivo_transacao');
    $table->foreignId('banco_id')->nullable()->constrained('banco');

    // Identificação
    $table->string('numero_transacao', 50)->unique();
    $table->string('referencia', 100)->nullable();
    $table->text('descricao');

    // Valores
    $table->decimal('valor', 14, 2);
    $table->string('moeda', 3)->default('BRL');

    // Datas
    $table->date('data_transacao');
    $table->date('data_efetivacao')->nullable();
    $table->timestamp('data_confirmacao')->nullable();

    // Dados bancários
    $table->string('agencia', 10)->nullable();
    $table->string('conta', 20)->nullable();
    $table->string('tipo_conta', 50)->nullable();
    $table->string('documento_numero', 50)->nullable();
    $table->string('beneficiario', 255)->nullable();

    // Controle
    $table->string('situacao', 50)->default('pendente'); // 'pendente', 'confirmada', 'liquidada', 'cancelada'
    $table->text('observacoes')->nullable();
    $table->string('anexo_url', 500)->nullable();
    $table->boolean('is_ativo')->default(true);
    $table->timestamps();
    $table->timestamp('data_exclusao')->nullable();

    // Índices
    $table->index('numero_transacao');
    $table->index('indicador_pessoal_id');
    $table->index('tipo_transacao_id');
    $table->index(['data_transacao', 'is_ativo']);
    $table->index('situacao');
});
```

##### `auditoria_transacao` (tabela de auditoria)

```php
Schema::create('auditoria_transacao', function (Blueprint $table) {
    $table->id();
    $table->foreignId('transacao_id')->constrained('transacao')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

    // O que foi alterado
    $table->string('campo_alterado', 255);
    $table->text('valor_anterior')->nullable();
    $table->text('valor_novo')->nullable();

    // Ação
    $table->string('acao', 50); // 'criacao', 'atualizacao', 'exclusao', 'confirmacao'
    $table->text('observacao')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->string('user_agent', 500)->nullable();

    $table->timestamps();

    // Índices
    $table->index('transacao_id');
    $table->index('user_id');
    $table->index('created_at');
    $table->index('acao');
});
```

##### `materialized_view_transacao_resumo`

```php
// Migration para criar Materialized View
Schema::create('mv_transacao_resumo', function (Blueprint $table) {
    $table->id();
    $table->foreignId('indicador_pessoal_id');
    $table->string('indicador_nome', 255);
    $table->string('tipo_transacao', 50);
    $table->decimal('total_entrada', 14, 2)->default(0);
    $table->decimal('total_saida', 14, 2)->default(0);
    $table->decimal('saldo', 14, 2)->default(0);
    $table->integer('total_transacoes')->default(0);
    $table->integer('total_confirmadas')->default(0);
    $table->integer('total_pendentes')->default(0);
    $table->date('primeira_transacao')->nullable();
    $table->date('ultima_transacao')->nullable();
    $table->timestamp('atualizado_em')->useCurrent();
});
```

Migração para criar a view:

```php
class CreateMaterializedViewTransacaoResumo extends Migration
{
    public function up()
    {
        DB::statement($this->getCreateViewSql());
    }

    public function down()
    {
        DB::statement('DROP MATERIALIZED VIEW IF EXISTS mv_transacao_resumo');
    }

    private function getCreateViewSql(): string
    {
        return "
        CREATE MATERIALIZED VIEW mv_transacao_resumo AS
        SELECT
            ROW_NUMBER() OVER () as id,
            t.indicador_pessoal_id,
            ip.nome as indicador_nome,
            tt.tipo as tipo_transacao,
            COALESCE(SUM(CASE WHEN tt.tipo = 'entrada' THEN t.valor ELSE 0 END), 0) as total_entrada,
            COALESCE(SUM(CASE WHEN tt.tipo = 'saida' THEN t.valor ELSE 0 END), 0) as total_saida,
            COALESCE(SUM(CASE WHEN tt.tipo = 'entrada' THEN t.valor ELSE -t.valor END), 0) as saldo,
            COUNT(t.id) as total_transacoes,
            SUM(CASE WHEN t.situacao = 'confirmada' OR t.situacao = 'liquidada' THEN 1 ELSE 0 END) as total_confirmadas,
            SUM(CASE WHEN t.situacao = 'pendente' THEN 1 ELSE 0 END) as total_pendentes,
            MIN(t.data_transacao) as primeira_transacao,
            MAX(t.data_transacao) as ultima_transacao,
            NOW() as atualizado_em
        FROM transacao t
        INNER JOIN indicador_pessoal ip ON ip.id = t.indicador_pessoal_id
        INNER JOIN tipo_transacao tt ON tt.id = t.tipo_transacao_id
        WHERE t.is_ativo = true AND t.data_exclusao IS NULL
        GROUP BY t.indicador_pessoal_id, ip.nome, tt.tipo;
        ";
    }
}
```

Command para atualizar a view:

```php
// app/Console/Commands/RefreshMaterializedViewCommand.php
class RefreshMaterializedViewCommand extends Command
{
    protected $signature = 'db:refresh-mv';
    protected $description = 'Atualiza Materialized Views';

    public function handle()
    {
        DB::statement('REFRESH MATERIALIZED VIEW CONCURRENTLY mv_transacao_resumo');
        $this->info('Materialized View atualizada com sucesso!');
    }
}
```

#### Models

```php
// app/Models/Transacao.php
class Transacao extends Model
{
    use SoftDeletes;

    protected $table = 'transacao';
    protected $fillable = [
        'indicador_pessoal_id', 'tipo_transacao_id', 'motivo_transacao_id',
        'banco_id', 'numero_transacao', 'referencia', 'descricao',
        'valor', 'moeda', 'data_transacao', 'data_efetivacao',
        'data_confirmacao', 'agencia', 'conta', 'tipo_conta',
        'documento_numero', 'beneficiario', 'situacao', 'observacoes',
        'anexo_url', 'is_ativo'
    ];

    protected $casts = [
        'data_transacao' => 'date',
        'data_efetivacao' => 'date',
        'data_confirmacao' => 'datetime',
        'valor' => 'decimal:2',
        'is_ativo' => 'boolean',
    ];

    // Relacionamentos
    public function indicadorPessoal()
    {
        return $this->belongsTo(IndicadorPessoal::class);
    }

    public function tipoTransacao()
    {
        return $this->belongsTo(TipoTransacao::class);
    }

    public function motivoTransacao()
    {
        return $this->belongsTo(MotivoTransacao::class);
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class);
    }

    public function auditorias()
    {
        return $this->hasMany(AuditoriaTransacao::class);
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('is_ativo', true)->whereNull('data_exclusao');
    }

    public function scopePorIndicador($query, $indicadorId)
    {
        return $query->where('indicador_pessoal_id', $indicadorId);
    }

    public function scopePorTipo($query, $tipoId)
    {
        return $query->where('tipo_transacao_id', $tipoId);
    }

    public function scopeEntrePeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_transacao', [$dataInicio, $dataFim]);
    }

    public function scopePorSituacao($query, $situacao)
    {
        return $query->where('situacao', $situacao);
    }

    public function scopeEntrada($query)
    {
        return $query->whereHas('tipoTransacao', fn($q) => $q->where('tipo', 'entrada'));
    }

    public function scopeSaida($query)
    {
        return $query->whereHas('tipoTransacao', fn($q) => $q->where('tipo', 'saida'));
    }

    // Métodos
    public function confirmar()
    {
        $this->update([
            'situacao' => 'confirmada',
            'data_confirmacao' => now(),
        ]);

        $this->registrarAuditoria('confirmacao', 'situacao', 'pendente', 'confirmada', 'Transação confirmada');
    }

    public function cancelar($motivo = null)
    {
        $this->update([
            'situacao' => 'cancelada',
        ]);

        $this->registrarAuditoria('exclusao', 'situacao', 'anterior', 'cancelada', $motivo);
    }

    public function registrarAuditoria($acao, $campo, $valorAnterior, $valorNovo, $observacao = null)
    {
        AuditoriaTransacao::create([
            'transacao_id' => $this->id,
            'user_id' => auth()->id(),
            'campo_alterado' => $campo,
            'valor_anterior' => $valorAnterior,
            'valor_novo' => $valorNovo,
            'acao' => $acao,
            'observacao' => $observacao,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    protected static function booted()
    {
        static::created(function ($model) {
            $model->registrarAuditoria(
                'criacao',
                'transacao_completa',
                null,
                json_encode($model->toArray()),
                'Transação criada'
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            foreach ($changes as $campo => $novoValor) {
                if (!in_array($campo, ['updated_at'])) {
                    $model->registrarAuditoria(
                        'atualizacao',
                        $campo,
                        $model->getOriginal($campo),
                        $novoValor,
                        'Campo alterado'
                    );
                }
            }
        });

        static::deleted(function ($model) {
            $model->registrarAuditoria(
                'exclusao',
                'is_ativo',
                true,
                false,
                'Transação marcada como inativa'
            );
        });
    }
}

// app/Models/AuditoriaTransacao.php
class AuditoriaTransacao extends Model
{
    protected $table = 'auditoria_transacao';
    protected $fillable = [
        'transacao_id', 'user_id', 'campo_alterado', 'valor_anterior',
        'valor_novo', 'acao', 'observacao', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function transacao()
    {
        return $this->belongsTo(Transacao::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

// app/Models/MvTransacaoResumo.php (para a view materializada)
class MvTransacaoResumo extends Model
{
    protected $table = 'mv_transacao_resumo';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'total_entrada' => 'decimal:2',
        'total_saida' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];
}
```

#### Controllers

```php
// app/Http/Controllers/TransacaoController.php
class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Transacao::with(['indicadorPessoal', 'tipoTransacao', 'banco'])
            ->ativo();

        // Filtros
        if ($request->indicador_pessoal_id) {
            $query->where('indicador_pessoal_id', $request->indicador_pessoal_id);
        }
        if ($request->tipo) {
            $query->whereHas('tipoTransacao', fn($q) => $q->where('tipo', $request->tipo));
        }
        if ($request->situacao) {
            $query->where('situacao', $request->situacao);
        }
        if ($request->data_inicio && $request->data_fim) {
            $query->entrePeriodo($request->data_inicio, $request->data_fim);
        }

        $transacoes = $query->orderBy('data_transacao', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($transacoes);
    }

    public function store(StoreTransacaoRequest $request)
    {
        $transacao = Transacao::create([
            ...$request->validated(),
            'numero_transacao' => $this->gerarNumeroTransacao(),
            'situacao' => 'pendente',
        ]);

        return response()->json($transacao, 201);
    }

    public function show($id)
    {
        $transacao = Transacao::with([
            'indicadorPessoal', 'tipoTransacao', 'motivoTransacao',
            'banco', 'auditorias.usuario'
        ])->findOrFail($id);

        return response()->json($transacao);
    }

    public function update(UpdateTransacaoRequest $request, Transacao $transacao)
    {
        if ($transacao->situacao !== 'pendente') {
            return response()->json(
                ['message' => 'Apenas transações pendentes podem ser editadas'],
                422
            );
        }

        $transacao->update($request->validated());

        return response()->json($transacao);
    }

    public function destroy(Transacao $transacao)
    {
        $transacao->cancelar('Transação cancelada pelo usuário');
        $transacao->delete();

        return response()->json(['message' => 'Transação cancelada']);
    }

    public function confirmar(Transacao $transacao)
    {
        if ($transacao->situacao !== 'pendente') {
            return response()->json(
                ['message' => 'Apenas transações pendentes podem ser confirmadas'],
                422
            );
        }

        $transacao->confirmar();

        return response()->json($transacao);
    }

    public function resumo(Request $request)
    {
        $resumo = MvTransacaoResumo::where('indicador_pessoal_id', $request->indicador_pessoal_id)
            ->first();

        return response()->json($resumo ?? [
            'total_entrada' => 0,
            'total_saida' => 0,
            'saldo' => 0,
            'total_transacoes' => 0,
        ]);
    }

    private function gerarNumeroTransacao(): string
    {
        $ano = date('Y');
        $mes = date('m');
        $dia = date('d');

        $ultimo = Transacao::where('numero_transacao', 'like', "TRX-$ano-$mes-$dia-%")
            ->latest('numero_transacao')
            ->first();

        $seq = $ultimo ? intval(substr($ultimo->numero_transacao, -4)) + 1 : 1;

        return sprintf("TRX-%s-%s-%s-%04d", $ano, $mes, $dia, $seq);
    }
}
```

#### Requests (Validação)

```php
// app/Http/Requests/StoreTransacaoRequest.php
class StoreTransacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indicador_pessoal_id' => 'required|exists:indicador_pessoal,id',
            'tipo_transacao_id' => 'required|exists:tipo_transacao,id',
            'motivo_transacao_id' => 'nullable|exists:motivo_transacao,id',
            'banco_id' => 'nullable|exists:banco,id',
            'descricao' => 'required|string|max:1000',
            'valor' => 'required|numeric|min:0.01',
            'moeda' => 'required|string|size:3',
            'data_transacao' => 'required|date',
            'data_efetivacao' => 'nullable|date|after_or_equal:data_transacao',
            'beneficiario' => 'nullable|string|max:255',
            'referencia' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'indicador_pessoal_id.required' => 'Indicador pessoal é obrigatório',
            'tipo_transacao_id.required' => 'Tipo de transação é obrigatório',
            'valor.min' => 'Valor deve ser maior que 0',
            'valor.numeric' => 'Valor deve ser um número',
        ];
    }
}
```

#### Endpoints

```php
// routes/api.php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Transações
    Route::get('transacoes', [TransacaoController::class, 'index']);
    Route::post('transacoes', [TransacaoController::class, 'store']);
    Route::get('transacoes/{id}', [TransacaoController::class, 'show']);
    Route::put('transacoes/{id}', [TransacaoController::class, 'update']);
    Route::delete('transacoes/{id}', [TransacaoController::class, 'destroy']);
    Route::post('transacoes/{id}/confirmar', [TransacaoController::class, 'confirmar']);
    Route::get('transacoes/resumo', [TransacaoController::class, 'resumo']);
    Route::get('transacoes/{id}/auditoria', [AuditoriaTransacaoController::class, 'porTransacao']);
});
```

#### Testes (Pest)

```php
test('pode criar transação de entrada', function () {
    $response = $this->postJson('/api/v1/transacoes', [
        'indicador_pessoal_id' => IndicadorPessoal::first()->id,
        'tipo_transacao_id' => TipoTransacao::where('tipo', 'entrada')->first()->id,
        'descricao' => 'Depósito teste',
        'valor' => 1000.00,
        'moeda' => 'BRL',
        'data_transacao' => now()->toDateString(),
    ]);

    $response->assertCreated()
        ->assertJsonPath('situacao', 'pendente');
});

test('número de transação é único', function () {
    $transacao = Transacao::factory()->create();

    expect($transacao->numero_transacao)
        ->toBeTruthy()
        ->not->toBe(Transacao::factory()->create()->numero_transacao);
});

test('pode confirmar transação', function () {
    $transacao = Transacao::factory()->create(['situacao' => 'pendente']);

    $this->postJson("/api/v1/transacoes/{$transacao->id}/confirmar");

    expect($transacao->fresh()->situacao)->toBe('confirmada');
});

test('auditoria registra criação', function () {
    $transacao = Transacao::factory()->create();

    expect($transacao->auditorias()->where('acao', 'criacao')->count())
        ->toBeGreaterThan(0);
});

test('resumo calcula saldos corretamente', function () {
    $indicador = IndicadorPessoal::first();

    Transacao::factory()->create([
        'indicador_pessoal_id' => $indicador->id,
        'tipo_transacao_id' => TipoTransacao::where('tipo', 'entrada')->first()->id,
        'valor' => 1000,
    ]);

    Transacao::factory()->create([
        'indicador_pessoal_id' => $indicador->id,
        'tipo_transacao_id' => TipoTransacao::where('tipo', 'saida')->first()->id,
        'valor' => 300,
    ]);

    $response = $this->getJson("/api/v1/transacoes/resumo?indicador_pessoal_id={$indicador->id}");

    $response->assertJsonPath('saldo', 700);
});
```

### Frontend

#### Store (Pinia) — `stores/transacao/index.js`

```javascript
import { defineStore } from "pinia";
import axios from "axios";

export const useTransacao = defineStore("transacao", {
  state: () => ({
    transacoes: [],
    paginacao: {
      current_page: 1,
      total: 0,
      per_page: 15,
    },
    atual: null,
    resumo: {
      total_entrada: 0,
      total_saida: 0,
      saldo: 0,
      total_transacoes: 0,
    },
    auditorias: [],
    loading: false,
    errors: {},
    filtros: {
      tipo: null,
      indicador_pessoal_id: null,
      situacao: null,
      data_inicio: null,
      data_fim: null,
      descricao: null,
    },
  }),

  getters: {
    totalPaginas: (state) =>
      Math.ceil(state.paginacao.total / state.paginacao.per_page),

    estaCarregando: (state) => state.loading,

    temErros: (state) => Object.keys(state.errors).length > 0,
  },

  actions: {
    async fetchTransacoes(params = {}) {
      this.loading = true;
      this.errors = {};
      try {
        const queryParams = { ...this.filtros, ...params };
        const { data } = await axios.get("/api/v1/transacoes", {
          params: queryParams,
        });

        this.transacoes = data.data;
        this.paginacao = {
          current_page: data.current_page,
          total: data.total,
          per_page: data.per_page,
        };
      } catch (error) {
        this.errors = error.response?.data?.errors || {
          message: "Erro ao buscar transações",
        };
        console.error("Erro ao buscar transações:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchById(id) {
      this.loading = true;
      this.errors = {};
      try {
        const { data } = await axios.get(`/api/v1/transacoes/${id}`);
        this.atual = data;
        this.auditorias = data.auditorias || [];
      } catch (error) {
        this.errors = { message: "Erro ao buscar transação" };
        console.error("Erro ao buscar transação:", error);
      } finally {
        this.loading = false;
      }
    },

    async criar(dados) {
      this.loading = true;
      this.errors = {};
      try {
        const { data } = await axios.post("/api/v1/transacoes", dados);
        this.transacoes.unshift(data);
        return data;
      } catch (error) {
        this.errors = error.response?.data?.errors || {
          message: "Erro ao criar transação",
        };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async atualizar(id, dados) {
      this.loading = true;
      this.errors = {};
      try {
        const { data } = await axios.put(`/api/v1/transacoes/${id}`, dados);
        const idx = this.transacoes.findIndex((t) => t.id === id);
        if (idx !== -1) this.transacoes[idx] = data;
        this.atual = data;
        return data;
      } catch (error) {
        this.errors = error.response?.data?.errors || {
          message: "Erro ao atualizar transação",
        };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async confirmar(id) {
      this.loading = true;
      this.errors = {};
      try {
        const { data } = await axios.post(`/api/v1/transacoes/${id}/confirmar`);
        const idx = this.transacoes.findIndex((t) => t.id === id);
        if (idx !== -1) this.transacoes[idx] = data;
        this.atual = data;
        return data;
      } catch (error) {
        this.errors = { message: "Erro ao confirmar transação" };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async excluir(id) {
      this.loading = true;
      this.errors = {};
      try {
        await axios.delete(`/api/v1/transacoes/${id}`);
        this.transacoes = this.transacoes.filter((t) => t.id !== id);
      } catch (error) {
        this.errors = { message: "Erro ao excluir transação" };
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchResumo(indicadorId) {
      try {
        const { data } = await axios.get("/api/v1/transacoes/resumo", {
          params: { indicador_pessoal_id: indicadorId },
        });
        this.resumo = data;
      } catch (error) {
        console.error("Erro ao buscar resumo:", error);
      }
    },

    setFiltros(novosFiltros) {
      this.filtros = { ...this.filtros, ...novosFiltros };
    },

    limparFiltros() {
      this.filtros = {
        tipo: null,
        indicador_pessoal_id: null,
        situacao: null,
        data_inicio: null,
        data_fim: null,
        descricao: null,
      };
    },

    setPaginacao(pagina) {
      this.paginacao.current_page = pagina;
    },
  },
});
```

#### Páginas e Componentes

`pages/transacao/ListaTransacoes.vue`:

```vue
<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-lg">
      <h4 class="q-ma-none">Transações</h4>
      <q-space />
      <q-btn
        color="primary"
        label="Nova Transação"
        icon="add"
        @click="abrirModal"
      />
    </div>

    <!-- Filtros -->
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-sm-6 col-md-3">
            <q-input
              v-model="filtros.descricao"
              label="Buscar"
              outlined
              dense
              @update:model-value="buscar"
            />
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <q-select
              v-model="filtros.tipo"
              :options="[
                { label: 'Entrada', value: 'entrada' },
                { label: 'Saída', value: 'saida' },
                { label: 'Caixa', value: 'caixa' },
              ]"
              label="Tipo"
              outlined
              dense
              clearable
              @update:model-value="buscar"
            />
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <q-select
              v-model="filtros.situacao"
              :options="['pendente', 'confirmada', 'liquidada', 'cancelada']"
              label="Situação"
              outlined
              dense
              clearable
              @update:model-value="buscar"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabela -->
    <q-card>
      <q-table
        :rows="transacaoStore.transacoes"
        :columns="colunas"
        row-key="id"
        flat
        bordered
        :loading="transacaoStore.loading"
        :pagination.sync="paginacao"
        @request="buscar"
      >
        <template v-slot:body-cell-tipo="props">
          <q-td :props="props">
            <q-chip
              :icon="props.row.tipo_transacao.icone"
              :color="props.row.tipo_transacao.cor"
              text-color="white"
              dense
            >
              {{ props.row.tipo_transacao.descricao }}
            </q-chip>
          </q-td>
        </template>

        <template v-slot:body-cell-valor="props">
          <q-td :props="props" class="text-weight-bold">
            <span
              :class="{
                'text-green': props.row.tipo_transacao.tipo === 'entrada',
                'text-red': props.row.tipo_transacao.tipo === 'saida',
              }"
            >
              {{ formatarMoeda(props.row.valor) }}
            </span>
          </q-td>
        </template>

        <template v-slot:body-cell-situacao="props">
          <q-td :props="props">
            <q-chip
              :label="props.row.situacao"
              :color="corSituacao(props.row.situacao)"
              text-color="white"
              dense
            />
          </q-td>
        </template>

        <template v-slot:body-cell-acoes="props">
          <q-td :props="props">
            <q-btn
              flat
              dense
              round
              icon="edit"
              @click="editarTransacao(props.row)"
            />
            <q-btn
              v-if="props.row.situacao === 'pendente'"
              flat
              dense
              round
              icon="check"
              @click="confirmarTransacao(props.row.id)"
            />
            <q-btn
              flat
              dense
              round
              icon="delete"
              @click="excluirTransacao(props.row.id)"
            />
          </q-td>
        </template>
      </q-table>
    </q-card>

    <!-- Modal -->
    <Modal
      v-model="modalAberta"
      :title="formMode === 'criar' ? 'Nova Transação' : 'Editar Transação'"
      @save="salvarTransacao"
    >
      <FormTransacao
        v-model="formulario"
        :mode="formMode"
        @validado="formularioValido = $event"
      />

      <template v-slot:actions>
        <q-btn label="Cancelar" flat v-close-popup />
        <q-btn
          label="Salvar"
          color="primary"
          :disable="!formularioValido"
          @click="salvarTransacao"
        />
      </template>
    </Modal>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useTransacao } from "@/stores/transacao";
import { useQuasar } from "quasar";
import FormTransacao from "@/components/transacao/FormTransacao.vue";

const q = useQuasar();
const transacaoStore = useTransacao();

const modalAberta = ref(false);
const formMode = ref("criar");
const formularioValido = ref(false);
const formulario = ref({});
const filtros = ref({});
const paginacao = ref({ page: 1, rowsPerPage: 15 });

const colunas = [
  {
    name: "numero_transacao",
    label: "Número",
    field: "numero_transacao",
    align: "left",
  },
  {
    name: "data_transacao",
    label: "Data",
    field: "data_transacao",
    align: "left",
  },
  { name: "tipo", label: "Tipo", field: "tipo_transacao" },
  { name: "descricao", label: "Descrição", field: "descricao", align: "left" },
  { name: "valor", label: "Valor", field: "valor", align: "right" },
  { name: "situacao", label: "Situação", field: "situacao" },
  { name: "acoes", label: "Ações", field: "acoes", align: "center" },
];

onMounted(() => {
  buscar();
});

const buscar = async (props = {}) => {
  const pagination = props.pagination || paginacao.value;
  paginacao.value = pagination;
  await transacaoStore.fetchTransacoes({
    page: pagination.page,
    per_page: pagination.rowsPerPage,
    ...filtros.value,
  });
};

const abrirModal = () => {
  formMode.value = "criar";
  formulario.value = {};
  modalAberta.value = true;
};

const editarTransacao = (transacao) => {
  formMode.value = "editar";
  formulario.value = { ...transacao };
  modalAberta.value = true;
};

const salvarTransacao = async () => {
  try {
    if (formMode.value === "criar") {
      await transacaoStore.criar(formulario.value);
      q.notify({
        type: "positive",
        message: "Transação criada com sucesso",
      });
    } else {
      await transacaoStore.atualizar(formulario.value.id, formulario.value);
      q.notify({
        type: "positive",
        message: "Transação atualizada com sucesso",
      });
    }
    modalAberta.value = false;
    buscar();
  } catch (error) {
    q.notify({
      type: "negative",
      message: error.response?.data?.message || "Erro ao salvar transação",
    });
  }
};

const confirmarTransacao = async (id) => {
  q.dialog({
    title: "Confirmar transação?",
    message: "Esta ação é irreversível",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await transacaoStore.confirmar(id);
      q.notify({
        type: "positive",
        message: "Transação confirmada",
      });
      buscar();
    } catch (error) {
      q.notify({
        type: "negative",
        message: "Erro ao confirmar transação",
      });
    }
  });
};

const excluirTransacao = async (id) => {
  q.dialog({
    title: "Excluir transação?",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await transacaoStore.excluir(id);
      q.notify({
        type: "positive",
        message: "Transação excluída",
      });
      buscar();
    } catch (error) {
      q.notify({
        type: "negative",
        message: "Erro ao excluir transação",
      });
    }
  });
};

const formatarMoeda = (valor) => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(valor);
};

const corSituacao = (situacao) => {
  const cores = {
    pendente: "warning",
    confirmada: "info",
    liquidada: "positive",
    cancelada: "negative",
  };
  return cores[situacao] || "grey";
};
</script>
```

`components/transacao/FormTransacao.vue`:

```vue
<template>
  <q-form @submit="submitForm" class="q-gutter-md">
    <div class="row q-col-gutter-md">
      <!-- Indicador Pessoal -->
      <div class="col-12">
        <BuscaAutocompleteIndicador
          v-model="form.indicador_pessoal_id"
          label="Indicador Pessoal"
          @update:model-value="validarForm"
        />
      </div>

      <!-- Tipo e Motivo -->
      <div class="col-12 col-sm-6">
        <SelectTipoTransacao
          v-model="form.tipo_transacao_id"
          @update:model-value="aoMudarTipo"
        />
      </div>

      <div class="col-12 col-sm-6">
        <SelectMotivoTransacao
          v-model="form.motivo_transacao_id"
          :tipo-transacao="tipoSelecionado"
        />
      </div>

      <!-- Descrição -->
      <div class="col-12">
        <q-input
          v-model="form.descricao"
          label="Descrição"
          outlined
          dense
          type="textarea"
          rows="3"
          counter
          maxlength="1000"
          @update:model-value="validarForm"
        />
      </div>

      <!-- Valor e Moeda -->
      <div class="col-12 col-sm-6">
        <q-input
          v-model.number="form.valor"
          label="Valor"
          outlined
          dense
          type="number"
          step="0.01"
          min="0"
          prefix="R$"
          @update:model-value="validarForm"
        />
      </div>

      <div class="col-12 col-sm-6">
        <q-select
          v-model="form.moeda"
          :options="['BRL', 'USD', 'EUR']"
          label="Moeda"
          outlined
          dense
        />
      </div>

      <!-- Datas -->
      <div class="col-12 col-sm-6">
        <q-input
          v-model="form.data_transacao"
          label="Data Transação"
          outlined
          dense
          type="date"
          @update:model-value="validarForm"
        />
      </div>

      <div class="col-12 col-sm-6">
        <q-input
          v-model="form.data_efetivacao"
          label="Data Efetivação"
          outlined
          dense
          type="date"
        />
      </div>

      <!-- Dados Bancários -->
      <div class="col-12">
        <q-expansion-item
          icon="mdi-bank"
          label="Dados Bancários (Opcional)"
          header-class="text-primary"
        >
          <div class="row q-col-gutter-md q-pa-md">
            <div class="col-12 col-sm-6">
              <q-select
                v-model="form.banco_id"
                :options="bancos"
                option-value="id"
                option-label="nome"
                label="Banco"
                outlined
                dense
                clearable
              />
            </div>

            <div class="col-12 col-sm-6">
              <q-input
                v-model="form.agencia"
                label="Agência"
                outlined
                dense
                mask="####-#"
              />
            </div>

            <div class="col-12 col-sm-6">
              <q-input v-model="form.conta" label="Conta" outlined dense />
            </div>

            <div class="col-12 col-sm-6">
              <q-select
                v-model="form.tipo_conta"
                :options="['corrente', 'poupanca', 'investimento']"
                label="Tipo Conta"
                outlined
                dense
              />
            </div>

            <div class="col-12">
              <q-input
                v-model="form.beneficiario"
                label="Beneficiário"
                outlined
                dense
              />
            </div>

            <div class="col-12">
              <q-input
                v-model="form.documento_numero"
                label="Documento (DOC/TED/Cheque)"
                outlined
                dense
              />
            </div>
          </div>
        </q-expansion-item>
      </div>

      <!-- Observações -->
      <div class="col-12">
        <q-input
          v-model="form.observacoes"
          label="Observações"
          outlined
          dense
          type="textarea"
          rows="2"
        />
      </div>
    </div>
  </q-form>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useCatalogo } from "@/stores/catalogo";
import SelectTipoTransacao from "./SelectTipoTransacao.vue";
import SelectMotivoTransacao from "./SelectMotivoTransacao.vue";
import BuscaAutocompleteIndicador from "@/components/indicador-pessoal/BuscaAutocomplete.vue";

const props = defineProps({
  modelValue: Object,
  mode: String,
});

const emit = defineEmits(["update:modelValue", "validado"]);

const catalogoStore = useCatalogo();
const form = ref({
  indicador_pessoal_id: null,
  tipo_transacao_id: null,
  motivo_transacao_id: null,
  descricao: "",
  valor: 0,
  moeda: "BRL",
  data_transacao: new Date().toISOString().split("T")[0],
  data_efetivacao: null,
  banco_id: null,
  agencia: "",
  conta: "",
  tipo_conta: null,
  beneficiario: "",
  documento_numero: "",
  observacoes: "",
});

const bancos = computed(() => catalogoStore.bancos);
const tipoSelecionado = computed(() => {
  return catalogoStore.obterTipoPorId(form.value.tipo_transacao_id);
});

onMounted(async () => {
  await catalogoStore.fetchBancos();
  if (props.modelValue) {
    form.value = { ...form.value, ...props.modelValue };
  }
});

watch(
  () => props.modelValue,
  (novoValor) => {
    if (novoValor) {
      form.value = { ...form.value, ...novoValor };
    }
  },
);

const aoMudarTipo = (tipo) => {
  form.value.tipo_transacao_id = tipo?.id;
  form.value.motivo_transacao_id = null;
  validarForm();
};

const validarForm = () => {
  const valido = !!(
    form.value.indicador_pessoal_id &&
    form.value.tipo_transacao_id &&
    form.value.descricao &&
    form.value.valor > 0 &&
    form.value.data_transacao
  );
  emit("validado", valido);
  emit("update:modelValue", form.value);
};

const submitForm = () => {
  // O salvar é feito via modal
};
</script>
```

---

## Resumo das Mudanças

| Aspecto                | Mudança                                                    |
| ---------------------- | ---------------------------------------------------------- |
| **Enum**               | Enums em PHP (TipoTransacaoEnum, SituacaoTransacaoEnum)    |
| **Banco de Dados**     | Strings em vez de ENUM, facilitando alterações futuras     |
| **Modal**              | Componente global `<Modal v-model="modal">` reutilizável   |
| **View Materializada** | `mv_transacao_resumo` para performance em relatórios       |
| **Auditoria**          | Integrada via `SoftDeletes` e hooks de modelo automáticos  |
| **Frontend**           | Modal para criar/editar, sem navegação de página           |
| **Transações**         | Simples: entrada, saída, caixa com confirmação em 2 passos |

---

## Próximos Passos

Após implementar as fases 06 e 07:

1. **Phase 08** — Transações Judiciais (depósitos, penhoras)
2. **Phase 09** — Relatórios e Gráficos (usando Materialized Views)
3. **Phase 10** — Dashboard Integrado (pessoa + transações + auditoria)
