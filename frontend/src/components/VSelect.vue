<template>
  <q-select :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" :options="options"
    :label="label" :clearable="clearable" :input-debounce="inputDebounce" :emit-value="emitValue"
    :option-label="optionLabel" :option-value="optionValue" :map-options="mapOptions" :disable="disable"
    :readonly="readonly" :outlined="outlined" :filled="filled" :dense="dense" :rules="rules" :hint="hint" :error="error"
    :error-message="errorMessage" :hide-bottom-space="hideBottomSpace">
    <!-- Slot para customizar mensagem quando não há opções -->
    <template v-slot:no-option>
      <slot name="no-option">
        <q-item>
          <q-item-section class="text-grey">
            {{ noOptionText }}
          </q-item-section>
        </q-item>
      </slot>
    </template>

    <!-- Slot para customizar item selecionado -->
    <template v-slot:selected-item="scope">
      <slot name="selected-item" v-bind="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section>
            <q-item-label>
              <q-chip :icon-remove="showRemoveIcon ? '' : 'cancel'" :color="chipColor"
                :text-color="chipTextColor">
                {{ scope.opt.label || scope.opt.nome }}
              </q-chip>
            </q-item-label>
          </q-item-section>
        </q-item>
      </slot>
    </template>

    <!-- Slot para customizar opções da lista -->
    <template v-slot:option="scope">
      <slot name="option" v-bind="scope">
        <q-item v-bind="scope.itemProps">
          <q-item-section v-if="showIcon && scope.opt.icon" avatar>
            <q-icon :name="scope.opt.icon" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ scope.opt.label || scope.opt.nome }}</q-item-label>
            <q-item-label v-if="scope.opt.description || scope.opt.nome" caption>
              {{ scope.opt.description || scope.opt.nome }}
            </q-item-label>
          </q-item-section>
        </q-item>
      </slot>
    </template>

    <!-- Outros slots padrão do q-select -->
    <template v-if="$slots.prepend" v-slot:prepend>
      <slot name="prepend"></slot>
    </template>

    <template v-if="$slots.append" v-slot:append>
      <slot name="append"></slot>
    </template>
  </q-select>
</template>

<script setup>
defineOptions({
  name: 'CustomSelect'
})

const props = defineProps({
  modelValue: {
    type: [String, Number, Object, Array],
    default: null
  },
  options: {
    type: Array,
    required: true,
    default: () => []
  },
  label: {
    type: String,
    default: ''
  },
  clearable: {
    type: Boolean,
    default: true
  },
  inputDebounce: {
    type: [String, Number],
    default: 500
  },
  emitValue: {
    type: Boolean,
    default: true
  },
  mapOptions: {
    type: Boolean,
    default: true
  },
  optionLabel: {
    type: String,
    default: 'nome'
  },

  optionValue: {
    type: String,
    default: 'id'
  },

  disable: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  outlined: {
    type: Boolean,
    default: false
  },
  filled: {
    type: Boolean,
    default: false
  },
  dense: {
    type: Boolean,
    default: false
  },
  rules: {
    type: Array,
    default: () => []
  },
  hint: {
    type: String,
    default: ''
  },
  error: {
    type: Boolean,
    default: false
  },
  errorMessage: {
    type: String,
    default: ''
  },
  noOptionText: {
    type: String,
    default: 'Nenhuma opção encontrada'
  },
  showRemoveIcon: {
    type: Boolean,
    default: false
  },
  chipColor: {
    type: String,
    default: undefined
  },
  chipTextColor: {
    type: String,
    default: undefined
  },
  showIcon: {
    type: Boolean,
    default: false
  },

  hideBottomSpace: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])
</script>

<style scoped>
/* Estilos customizados, se necessário */
</style>