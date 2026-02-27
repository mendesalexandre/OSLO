<template>
  <div class="yes-no-option-group">
    <v-label v-if="label" :label="label" :obrigatorio="required" />

    <div class="bootstrap-radio-group">
      <q-radio v-model="localValue" :val="true" :label="yesLabel" :disable="disable" color="positive" />
      <q-radio v-model="localValue" :val="false" :label="noLabel" :disable="disable" color="negative" />
    </div>

    <div v-if="error" class="text-negative text-caption q-mt-xs">{{ error }}</div>
    <div v-if="hint && !error" class="text-grey-7 text-caption q-mt-xs">{{ hint }}</div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: [Boolean, null],
    default: null,
  },
  label: {
    type: String,
    default: "",
  },
  disable: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: "",
  },
  hint: {
    type: String,
    default: "",
  },
  yesLabel: {
    type: String,
    default: "Sim",
  },
  noLabel: {
    type: String,
    default: "Não",
  },
  required: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue", "change"]);

const localValue = ref(props.modelValue);

watch(
  () => props.modelValue,
  (newValue) => {
    localValue.value = newValue;
  }
);

watch(localValue, (newValue) => {
  emit("update:modelValue", newValue);
  emit("change", newValue);
});
</script>

<style lang="scss" scoped>
.yes-no-option-group {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
  width: 100%;
  height: 100%;
  justify-content: center;

  .bootstrap-radio-group {
    display: flex;
    gap: 1rem;
    align-items: center;
  }

  :deep(.q-radio) {

    // Container interno - ajusta o tamanho do círculo
    .q-radio__inner {
      font-size: 14px;
      width: 1em;
      height: 1em;
      min-width: 1em;

      // Remove o ::before do Quasar
      &::before {
        display: none !important;
      }
    }

    // O SVG do Quasar
    .q-radio__bg {
      width: 100%;
      height: 100%;
      top: 0;
      left: 0 !important;
      overflow: visible;
    }

    // Label - alinhamento vertical corrigido
    .q-radio__label {
      font-size: 0.875rem;
      color: var(--text-color);
      padding-left: 4px !important;
      line-height: 1.43;
      margin-top: 0 !important;
      align-self: center;
    }

    // Alinhamento vertical do radio
    &.row {
      align-items: center;
    }

    // Desabilitado
    &.q-radio--disabled {
      opacity: 0.6;

      .q-radio__bg path:first-child {
        fill: var(--bg-subtle);
        stroke: var(--border-color);
      }
    }
  }

  .text-caption {
    font-size: 0.75rem;
    line-height: 1.25rem;
  }
}
</style>