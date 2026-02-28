# OSLO — Dashboard Minimalista

## Dependências

```bash
npm install dayjs
# Sem FullCalendar, calendar será custom
```

---

## Store Dashboard

**Arquivo:** `stores/dashboard/index.js`

```javascript
import { defineStore } from "pinia";
import axios from "axios";

export const useDashboard = defineStore("dashboard", {
  state: () => ({
    resumoFinanceiro: {
      total_entrada: 0,
      total_saida: 0,
      saldo: 0,
      transacoes_pendentes: 0,
    },
    eventosTimeline: [],
    eventosCalendario: [],
    indicadores: {
      transacoes_mes: 0,
      indicadores_ativos: 0,
      indisponibilidades_ativas: 0,
      depositos_judiciais: 0,
    },
    graficoAtividade: [],
    loading: false,
    errors: {},
  }),

  getters: {
    estaCarregando: (state) => state.loading,

    temErros: (state) => Object.keys(state.errors).length > 0,
  },

  actions: {
    async fetchDashboard() {
      this.loading = true;
      this.errors = {};
      try {
        const { data } = await axios.get("/api/v1/dashboard");

        this.resumoFinanceiro = data.resumo_financeiro;
        this.eventosTimeline = data.eventos_timeline;
        this.eventosCalendario = data.eventos_calendario;
        this.indicadores = data.indicadores;
        this.graficoAtividade = data.grafico_atividade;
      } catch (error) {
        this.errors = error.response?.data?.errors || {
          message: "Erro ao carregar dashboard",
        };
        console.error("Erro ao carregar dashboard:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchEventosPorMes(ano, mes) {
      try {
        const { data } = await axios.get("/api/v1/dashboard/eventos", {
          params: { ano, mes },
        });
        this.eventosCalendario = data.eventos;
        return data.eventos;
      } catch (error) {
        console.error("Erro ao buscar eventos:", error);
        return [];
      }
    },
  },
});
```

---

## Backend - DashboardController

**Arquivo:** `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use App\Models\IndicadorPessoal;
use App\Models\IndisponibilidadeParte;
use App\Models\DepositoJudicial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $resumoFinanceiro = $this->obterResumoFinanceiro();
        $eventosTimeline = $this->obterEventosTimeline();
        $eventosCalendario = $this->obterEventosCalendario();
        $indicadores = $this->obterIndicadores();
        $graficoAtividade = $this->obterGraficoAtividade();

        return response()->json([
            'resumo_financeiro' => $resumoFinanceiro,
            'eventos_timeline' => $eventosTimeline,
            'eventos_calendario' => $eventosCalendario,
            'indicadores' => $indicadores,
            'grafico_atividade' => $graficoAtividade,
        ]);
    }

    private function obterResumoFinanceiro()
    {
        $inicio = now()->startOfMonth();
        $fim = now()->endOfMonth();

        $entrada = Transacao::where('tipo_transacao_id', 1)
            ->ativo()
            ->whereBetween('data_transacao', [$inicio, $fim])
            ->sum('valor');

        $saida = Transacao::where('tipo_transacao_id', 2)
            ->ativo()
            ->whereBetween('data_transacao', [$inicio, $fim])
            ->sum('valor');

        $pendentes = Transacao::ativo()
            ->where('situacao', 'pendente')
            ->count();

        return [
            'total_entrada' => (float) $entrada,
            'total_saida' => (float) $saida,
            'saldo' => (float) ($entrada - $saida),
            'transacoes_pendentes' => $pendentes,
        ];
    }

    private function obterEventosTimeline()
    {
        // Últimos 8 eventos (mix de transações, indisponibilidades, etc)
        $transacoes = Transacao::ativo()
            ->with('tipoTransacao')
            ->latest('data_transacao')
            ->take(8)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => "transacao-{$t->id}",
                    'tipo' => 'transacao',
                    'titulo' => $t->descricao,
                    'descricao' => $t->tipoTransacao->descricao,
                    'valor' => $t->valor,
                    'data' => $t->data_transacao->toIso8601String(),
                    'icone' => $t->tipoTransacao->icone,
                    'cor' => $t->tipoTransacao->cor,
                ];
            })
            ->toArray();

        return collect($transacoes)
            ->sortByDesc('data')
            ->values()
            ->all();
    }

    private function obterEventosCalendario()
    {
        $ano = now()->year;
        $mes = now()->month;

        // Transações do mês
        $transacoes = Transacao::ativo()
            ->whereYear('data_transacao', $ano)
            ->whereMonth('data_transacao', $mes)
            ->get()
            ->groupBy(function ($t) {
                return $t->data_transacao->day;
            })
            ->map(function ($items, $dia) {
                $entrada = $items->where('tipo_transacao_id', 1)->sum('valor');
                $saida = $items->where('tipo_transacao_id', 2)->sum('valor');

                return [
                    'dia' => $dia,
                    'count' => $items->count(),
                    'entrada' => (float) $entrada,
                    'saida' => (float) $saida,
                    'saldo' => (float) ($entrada - $saida),
                ];
            })
            ->values()
            ->all();

        return $transacoes;
    }

    private function obterIndicadores()
    {
        return [
            'transacoes_mes' => Transacao::ativo()
                ->whereMonth('data_transacao', now()->month)
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

            'depositos_judiciais' => DepositoJudicial::ativo()->count(),
        ];
    }

    private function obterGraficoAtividade()
    {
        $dias = collect();
        $inicio = now()->startOfMonth();
        $fim = now()->endOfMonth();

        for ($data = clone $inicio; $data <= $fim; $data->addDay()) {
            $data_str = $data->format('Y-m-d');

            $count = Transacao::ativo()
                ->whereDate('data_transacao', $data_str)
                ->count();

            if ($count > 0) {
                $dias->push([
                    'data' => $data->format('d/m'),
                    'valor' => $count,
                    'semana' => $data->format('w'),
                ]);
            }
        }

        return $dias->values()->all();
    }

    public function eventosPorMes(Request $request)
    {
        $ano = $request->query('ano', now()->year);
        $mes = $request->query('mes', now()->month);

        $transacoes = Transacao::ativo()
            ->whereYear('data_transacao', $ano)
            ->whereMonth('data_transacao', $mes)
            ->get()
            ->groupBy(function ($t) {
                return $t->data_transacao->day;
            })
            ->map(function ($items, $dia) {
                return [
                    'dia' => $dia,
                    'count' => $items->count(),
                    'entrada' => (float) $items->where('tipo_transacao_id', 1)->sum('valor'),
                    'saida' => (float) $items->where('tipo_transacao_id', 2)->sum('valor'),
                ];
            })
            ->values()
            ->all();

        return response()->json(['eventos' => $transacoes]);
    }
}
```

**Rotas:**

```php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/eventos', [DashboardController::class, 'eventosPorMes']);
});
```

---

## Página Dashboard - Minimalista

**Arquivo:** `pages/DashboardPage.vue`

```vue
<template>
  <q-page class="dashboard-minimalista q-pa-md">
    <!-- Resumo Financeiro -->
    <div class="resumo-section q-mb-lg">
      <h2 class="text-h5 text-weight-bold q-ma-none q-mb-md">
        Resumo Financeiro
      </h2>

      <div class="row q-col-gutter-md">
        <!-- Saldo -->
        <div class="col-12 col-sm-6 col-md-3">
          <ResumoCard
            titulo="Saldo"
            :valor="dashboardStore.resumoFinanceiro.saldo"
            tipo="saldo"
          />
        </div>

        <!-- Entradas -->
        <div class="col-12 col-sm-6 col-md-3">
          <ResumoCard
            titulo="Entradas"
            :valor="dashboardStore.resumoFinanceiro.total_entrada"
            tipo="entrada"
          />
        </div>

        <!-- Saídas -->
        <div class="col-12 col-sm-6 col-md-3">
          <ResumoCard
            titulo="Saídas"
            :valor="dashboardStore.resumoFinanceiro.total_saida"
            tipo="saida"
          />
        </div>

        <!-- Pendentes -->
        <div class="col-12 col-sm-6 col-md-3">
          <ResumoCard
            titulo="Pendentes"
            :valor="dashboardStore.resumoFinanceiro.transacoes_pendentes"
            tipo="pendente"
            :ehNumero="true"
          />
        </div>
      </div>
    </div>

    <!-- Atajos Rápidos -->
    <div class="shortcuts-section q-mb-lg">
      <h2 class="text-h5 text-weight-bold q-ma-none q-mb-md">Ações Rápidas</h2>

      <div class="row q-col-gutter-md">
        <div class="col-12 col-sm-6 col-md-3">
          <ShortcutButton
            icone="add"
            titulo="Nova Transação"
            cor="primary"
            @click="irPara('/transacoes/nova')"
          />
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <ShortcutButton
            icone="person-add"
            titulo="Novo Indicador"
            cor="info"
            @click="irPara('/indicador-pessoal/novo')"
          />
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <ShortcutButton
            icone="list"
            titulo="Ver Transações"
            cor="warning"
            @click="irPara('/transacoes')"
          />
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <ShortcutButton
            icone="search"
            titulo="Consultar"
            cor="secondary"
            @click="irPara('/consulta')"
          />
        </div>
      </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="row q-col-gutter-lg">
      <!-- Calendário + Atividade -->
      <div class="col-12 col-lg-6">
        <div class="q-gutter-md">
          <!-- Calendário -->
          <CalendarioMinimalista
            :eventos="dashboardStore.eventosCalendario"
            @change="aoMudarMes"
          />

          <!-- Gráfico de Atividade -->
          <GraficoAtividade :dados="dashboardStore.graficoAtividade" />
        </div>
      </div>

      <!-- Timeline de Eventos -->
      <div class="col-12 col-lg-6">
        <div class="q-gutter-md">
          <!-- Indicadores -->
          <IndicadoresMinimalista :indicadores="dashboardStore.indicadores" />

          <!-- Timeline -->
          <TimelineEventos :eventos="dashboardStore.eventosTimeline" />
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted } from "vue";
import { useRouter } from "vue-router";
import { useDashboard } from "@/stores/dashboard";
import ResumoCard from "@/components/dashboard/ResumoCard.vue";
import ShortcutButton from "@/components/dashboard/ShortcutButton.vue";
import CalendarioMinimalista from "@/components/dashboard/CalendarioMinimalista.vue";
import GraficoAtividade from "@/components/dashboard/GraficoAtividade.vue";
import IndicadoresMinimalista from "@/components/dashboard/IndicadoresMinimalista.vue";
import TimelineEventos from "@/components/dashboard/TimelineEventos.vue";

const router = useRouter();
const dashboardStore = useDashboard();

onMounted(async () => {
  await dashboardStore.fetchDashboard();
});

const irPara = (rota) => {
  router.push(rota);
};

const aoMudarMes = async ({ ano, mes }) => {
  await dashboardStore.fetchEventosPorMes(ano, mes);
};
</script>

<style scoped lang="scss">
.dashboard-minimalista {
  background: #fafafa;
  max-width: 1600px;
  margin: 0 auto;

  h2 {
    color: #1a1a1a;
    letter-spacing: -0.5px;
  }

  .resumo-section,
  .shortcuts-section {
    animation: fadeInUp 0.3s ease-out;
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
```

---

## Componentes Minimalistas

### 1. ResumoCard

**Arquivo:** `components/dashboard/ResumoCard.vue`

```vue
<template>
  <div class="resumo-card">
    <div class="resumo-titulo text-caption text-uppercase letter-spacing">
      {{ titulo }}
    </div>
    <div class="resumo-valor text-h4 text-weight-bold">
      {{ valorFormatado }}
    </div>
    <div class="resumo-bar" :class="`bar-${tipo}`"></div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  titulo: String,
  valor: [Number, String],
  tipo: {
    type: String,
    default: "saldo",
    validator: (v) => ["saldo", "entrada", "saida", "pendente"].includes(v),
  },
  ehNumero: {
    type: Boolean,
    default: false,
  },
});

const valorFormatado = computed(() => {
  if (props.ehNumero) return props.valor;

  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 0,
  }).format(props.valor);
});
</script>

<style scoped lang="scss">
.resumo-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  transition: all 0.2s ease;

  &:hover {
    border-color: #ccc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .resumo-titulo {
    color: #999;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
  }

  .resumo-valor {
    color: #1a1a1a;
    margin-bottom: 12px;
    line-height: 1.2;
  }

  .resumo-bar {
    height: 3px;
    border-radius: 2px;

    &.bar-saldo {
      background: #667eea;
    }

    &.bar-entrada {
      background: #4caf50;
    }

    &.bar-saida {
      background: #f44336;
    }

    &.bar-pendente {
      background: #ff9800;
    }
  }
}
</style>
```

### 2. ShortcutButton

**Arquivo:** `components/dashboard/ShortcutButton.vue`

```vue
<template>
  <button
    class="shortcut-btn"
    :class="`shortcut-${cor}`"
    @click="$emit('click')"
  >
    <q-icon :name="icone" size="24px" />
    <div class="shortcut-titulo text-caption">{{ titulo }}</div>
  </button>
</template>

<script setup>
defineProps({
  icone: String,
  titulo: String,
  cor: {
    type: String,
    default: "primary",
  },
});

defineEmits(["click"]);
</script>

<style scoped lang="scss">
.shortcut-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 24px 16px;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  color: inherit;

  &:hover {
    border-color: #ccc;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
  }

  &:active {
    transform: translateY(0);
  }

  .shortcut-titulo {
    color: #666;
    font-weight: 500;
    text-align: center;
    line-height: 1.3;
  }

  &.shortcut-primary {
    border-color: #667eea20;
    color: #667eea;

    &:hover {
      background: #667eea08;
    }
  }

  &.shortcut-info {
    border-color: #2196f320;
    color: #2196f3;

    &:hover {
      background: #2196f308;
    }
  }

  &.shortcut-warning {
    border-color: #ff980020;
    color: #ff9800;

    &:hover {
      background: #ff980008;
    }
  }

  &.shortcut-secondary {
    border-color: #9c27b020;
    color: #9c27b0;

    &:hover {
      background: #9c27b008;
    }
  }
}
</style>
```

### 3. CalendarioMinimalista

**Arquivo:** `components/dashboard/CalendarioMinimalista.vue`

```vue
<template>
  <div class="calendario-card">
    <div class="calendario-header">
      <button class="btn-nav" @click="mesAnterior">
        <q-icon name="chevron_left" />
      </button>
      <div class="calendario-titulo text-weight-bold">
        {{ mesFormatado }} {{ ano }}
      </div>
      <button class="btn-nav" @click="proximoMes">
        <q-icon name="chevron_right" />
      </button>
    </div>

    <!-- Dias da semana -->
    <div class="calendario-semana">
      <div class="dia-semana" v-for="dia in diasSemana" :key="dia">
        {{ dia }}
      </div>
    </div>

    <!-- Dias do mês -->
    <div class="calendario-dias">
      <div
        v-for="dia in diasMes"
        :key="dia.data"
        class="dia-cell"
        :class="{
          'dia-outro-mes': !dia.ehMesAtual,
          'dia-com-evento': dia.count > 0,
          'dia-hoje': dia.ehHoje,
        }"
        @click="selecionarDia(dia)"
      >
        <div class="dia-numero">{{ dia.dia }}</div>
        <div v-if="dia.count > 0" class="dia-evento">
          <span class="evento-badge">{{ dia.count }}</span>
        </div>
      </div>
    </div>

    <!-- Legenda -->
    <div class="calendario-legenda q-mt-md">
      <div class="legenda-item">
        <span class="legenda-box" style="background: #4caf50"></span>
        <span class="text-caption">Entradas</span>
      </div>
      <div class="legenda-item">
        <span class="legenda-box" style="background: #f44336"></span>
        <span class="text-caption">Saídas</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/pt-br";

dayjs.locale("pt-br");

const props = defineProps({
  eventos: Array,
});

const emit = defineEmits(["change"]);

const ano = ref(dayjs().year());
const mes = ref(dayjs().month() + 1);

const diasSemana = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"];

const mesFormatado = computed(() => {
  return dayjs()
    .month(mes.value - 1)
    .format("MMMM");
});

const diasMes = computed(() => {
  const primeroDia = dayjs(
    `${ano.value}-${String(mes.value).padStart(2, "0")}-01`,
  );
  const ultimoDia = primeroDia.endOf("month");
  const diasAnterior = primeroDia.day();

  const diasArray = [];

  // Dias do mês anterior
  for (let i = diasAnterior - 1; i >= 0; i--) {
    const dia = primeroDia.subtract(i + 1, "day");
    diasArray.push({
      dia: dia.date(),
      data: dia.format("YYYY-MM-DD"),
      ehMesAtual: false,
      count: 0,
    });
  }

  // Dias do mês atual
  for (let i = 1; i <= ultimoDia.date(); i++) {
    const data = primeroDia.date(i);
    const dataStr = data.format("YYYY-MM-DD");
    const evento = props.eventos?.find((e) => e.dia === i);

    diasArray.push({
      dia: i,
      data: dataStr,
      ehMesAtual: true,
      ehHoje: data.isSame(dayjs(), "day"),
      count: evento?.count || 0,
      entrada: evento?.entrada || 0,
      saida: evento?.saida || 0,
    });
  }

  // Dias do mês seguinte
  const diasSeguinte = 42 - diasArray.length;
  for (let i = 1; i <= diasSeguinte; i++) {
    const dia = ultimoDia.add(i, "day");
    diasArray.push({
      dia: dia.date(),
      data: dia.format("YYYY-MM-DD"),
      ehMesAtual: false,
      count: 0,
    });
  }

  return diasArray;
});

const mesAnterior = () => {
  mes.value--;
  if (mes.value < 1) {
    mes.value = 12;
    ano.value--;
  }
  emit("change", { ano: ano.value, mes: mes.value });
};

const proximoMes = () => {
  mes.value++;
  if (mes.value > 12) {
    mes.value = 1;
    ano.value++;
  }
  emit("change", { ano: ano.value, mes: mes.value });
};

const selecionarDia = (dia) => {
  console.log("Dia selecionado:", dia);
};
</script>

<style scoped lang="scss">
.calendario-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .calendario-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;

    .btn-nav {
      background: none;
      border: none;
      padding: 4px 8px;
      cursor: pointer;
      border-radius: 4px;
      transition: background 0.2s ease;

      &:hover {
        background: #f5f5f5;
      }
    }

    .calendario-titulo {
      font-size: 16px;
      text-transform: capitalize;
      color: #1a1a1a;
    }
  }

  .calendario-semana {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 8px;

    .dia-semana {
      text-align: center;
      font-size: 12px;
      font-weight: 600;
      color: #999;
      padding: 8px 0;
    }
  }

  .calendario-dias {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;

    .dia-cell {
      aspect-ratio: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      cursor: pointer;
      position: relative;
      transition: all 0.2s ease;
      font-size: 13px;
      font-weight: 500;

      .dia-numero {
        color: #1a1a1a;
      }

      .dia-evento {
        position: absolute;
        top: 2px;
        right: 2px;

        .evento-badge {
          display: inline-block;
          width: 18px;
          height: 18px;
          background: #667eea;
          color: white;
          border-radius: 50%;
          font-size: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: 600;
        }
      }

      &.dia-outro-mes {
        color: #ccc;
      }

      &.dia-com-evento {
        background: #f0f0f0;

        &:hover {
          background: #e8e8e8;
        }
      }

      &.dia-hoje {
        background: #667eea;
        color: white;

        .dia-numero {
          color: white;
        }
      }

      &:hover {
        background: #f5f5f5;
      }
    }
  }

  .calendario-legenda {
    display: flex;
    gap: 16px;
    justify-content: center;

    .legenda-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: #666;

      .legenda-box {
        width: 12px;
        height: 12px;
        border-radius: 2px;
      }
    }
  }
}
</style>
```

### 4. GraficoAtividade

**Arquivo:** `components/dashboard/GraficoAtividade.vue`

```vue
<template>
  <div class="grafico-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Atividade do Mês
    </h3>

    <div class="grafico-barras">
      <div v-for="item in dados" :key="item.data" class="barra-item">
        <div class="barra-wrapper">
          <div
            class="barra"
            :style="{ height: `${(item.valor / maxValor) * 100}%` }"
          ></div>
        </div>
        <div class="barra-label text-caption">{{ item.data }}</div>
      </div>
    </div>

    <div class="grafico-info text-caption q-mt-md">
      <span>Total: {{ dados.length }} dias com transações</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  dados: Array,
});

const maxValor = computed(() => {
  if (!props.dados || props.dados.length === 0) return 1;
  return Math.max(...props.dados.map((d) => d.valor));
});
</script>

<style scoped lang="scss">
.grafico-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .grafico-barras {
    display: flex;
    gap: 6px;
    align-items: flex-end;
    justify-content: flex-start;
    height: 120px;
    overflow-x: auto;
    padding-bottom: 8px;

    .barra-item {
      flex: 0 0 calc(100% / 8);
      min-width: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;

      .barra-wrapper {
        width: 100%;
        height: 100%;
        background: #f5f5f5;
        border-radius: 3px;
        overflow: hidden;
        display: flex;
        align-items: flex-end;

        .barra {
          width: 100%;
          background: #667eea;
          transition: all 0.2s ease;
          cursor: pointer;

          &:hover {
            background: #764ba2;
          }
        }
      }

      .barra-label {
        color: #999;
        font-size: 10px;
        white-space: nowrap;
        transform: rotate(-45deg);
        transform-origin: left;
        margin-top: 4px;
      }
    }
  }

  .grafico-info {
    color: #999;
  }
}
</style>
```

### 5. IndicadoresMinimalista

**Arquivo:** `components/dashboard/IndicadoresMinimalista.vue`

```vue
<template>
  <div class="indicadores-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Indicadores
    </h3>

    <div class="indicador-list">
      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="swap_horiz" size="20px" color="#667eea" />
          <span>Transações</span>
        </div>
        <div class="indicador-valor">{{ indicadores.transacoes_mes }}</div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="person" size="20px" color="#4caf50" />
          <span>Indicadores</span>
        </div>
        <div class="indicador-valor">{{ indicadores.indicadores_ativos }}</div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="warning" size="20px" color="#ff9800" />
          <span>Indisponibilidades</span>
        </div>
        <div class="indicador-valor">
          {{ indicadores.indisponibilidades_ativas }}
        </div>
      </div>

      <q-separator class="q-my-md" />

      <div class="indicador-row">
        <div class="indicador-label">
          <q-icon name="gavel" size="20px" color="#2196f3" />
          <span>Depósitos Judiciais</span>
        </div>
        <div class="indicador-valor">{{ indicadores.depositos_judiciais }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  indicadores: Object,
});
</script>

<style scoped lang="scss">
.indicadores-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .indicador-list {
    .indicador-row {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .indicador-label {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: #666;
        font-weight: 500;
      }

      .indicador-valor {
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
      }
    }
  }
}
</style>
```

### 6. TimelineEventos

**Arquivo:** `components/dashboard/TimelineEventos.vue`

```vue
<template>
  <div class="timeline-card">
    <h3 class="text-subtitle2 text-weight-bold q-ma-none q-mb-md">
      Últimos Eventos
    </h3>

    <div class="timeline-list">
      <div
        v-for="(evento, idx) in eventos"
        :key="evento.id"
        class="timeline-item"
      >
        <div class="timeline-marker" :style="{ background: evento.cor }">
          <q-icon :name="evento.icone" size="16px" color="white" />
        </div>

        <div class="timeline-content">
          <div class="timeline-titulo text-weight-bold text-sm">
            {{ evento.titulo }}
          </div>
          <div class="timeline-desc text-caption">
            {{ evento.descricao }}
          </div>
          <div class="timeline-meta text-caption text-grey-7">
            {{ formatarData(evento.data) }}
          </div>
          <div
            v-if="evento.valor"
            class="timeline-valor text-weight-bold q-mt-xs"
          >
            {{ formatarMoeda(evento.valor) }}
          </div>
        </div>

        <q-separator v-if="idx < eventos.length - 1" class="q-my-md" />
      </div>

      <div v-if="!eventos.length" class="text-center text-grey-7 q-py-lg">
        Nenhum evento recente
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/pt-br";

dayjs.extend(relativeTime);
dayjs.locale("pt-br");

defineProps({
  eventos: Array,
});

const formatarData = (data) => {
  return dayjs(data).fromNow();
};

const formatarMoeda = (valor) => {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 0,
  }).format(valor);
};
</script>

<style scoped lang="scss">
.timeline-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e0e0e0;

  .timeline-list {
    .timeline-item {
      display: flex;
      gap: 16px;

      .timeline-marker {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
      }

      .timeline-content {
        flex: 1;

        .timeline-titulo {
          font-size: 14px;
          color: #1a1a1a;
          margin-bottom: 4px;
        }

        .timeline-desc {
          color: #666;
          margin-bottom: 4px;
        }

        .timeline-meta {
          color: #999;
        }

        .timeline-valor {
          font-size: 13px;
          color: #667eea;
        }
      }
    }
  }
}
</style>
```

---

## Rota

**Arquivo:** `router/index.js`

```javascript
{
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
        {
            path: '',
            name: 'Dashboard',
            component: () => import('pages/DashboardPage.vue'),
            meta: { requiresAuth: true },
        },
    ],
},
```

---

## Layout Final - Minimalista

```
┌────────────────────────────────────────────────────┐
│ Resumo Financeiro                                  │
│ ┌─────────┬─────────┬─────────┬─────────┐         │
│ │ Saldo   │ Entradas│ Saídas  │ Pendente│         │
│ │ R$ 5.2k │ R$ 8.5k │ R$ 3.3k │   12    │         │
│ └─────────┴─────────┴─────────┴─────────┘         │
├────────────────────────────────────────────────────┤
│ Ações Rápidas                                      │
│ ┌─────────┬─────────┬─────────┬─────────┐         │
│ │ + Nova  │+ Novo   │ Ver     │ Consultar         │
│ │Transação│Indicador│Transações         │         │
│ └─────────┴─────────┴─────────┴─────────┘         │
├─────────────────────────────────────────────────────┤
│  Calendário           │  Indicadores               │
│ [Custom Calendar]     │ • Transações: 12           │
│                       │ • Indicadores: 8           │
│                       │ • Indispon: 2              │
│ Atividade do Mês      │ • Depósitos: 3             │
│ [Bar Chart]           │                            │
│                       │  Timeline de Eventos       │
│                       │ [Últimos 8 eventos]        │
│                       │                            │
└─────────────────────────────────────────────────────┘
```

---

## Características Principais

✅ **Minimalista e Limpo** — Sem saudação, sem excesso
✅ **Calendário Custom** — Feito com Day.js, totalmente customizável
✅ **Timeline de Eventos** — Últimos 8 eventos em tempo relativo
✅ **Indicadores em Tempo Real** — KPIs dinâmicos
✅ **Gráfico de Atividade** — Mostra dias com transações
✅ **Ações Rápidas** — Shortcuts para tarefas mais comuns
✅ **Design Elegante** — Cores neutras, tipografia clara
✅ **Responsivo** — Mobile, tablet, desktop

Pronto para Claude CLI implementar! 🚀
