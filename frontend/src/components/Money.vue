<template>
  <q-field v-bind="$attrs">
    <template v-slot:control="{ id, floatingLabel, modelValue, emitValue }">
      <Money :id="id" :model-value="modelValue" @update:model-value="
        (val) => {
          emitValue(val);
          emit('update:modelValue', val);
        }
      " v-bind="config" v-show="floatingLabel" :disabled="props.disabled" :class="props.cssClass || 'q-field__input'"
        :aria-label="$attrs.label || 'Campo de moeda'">
        <template v-for="(_, slot) in slots" :key="slot" v-slot:[slot]="scope">
          <slot :name="slot" v-bind="scope" :key="slot" />
        </template>
      </Money>
    </template>
  </q-field>
</template>

<script setup>
import { useSlots, computed } from "vue";
import { Money } from "v-money3";

defineOptions({
  name: "VMoney",
});

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: {
    type: [Number, String],
    default: null,
  },

  disabled: {
    type: Boolean,
    default: false,
  },

  prefix: {
    type: String,
    default: "R$ ",
  },

  focusOnRight: {
    type: Boolean,
    default: true,
  },

  shouldRound: {
    type: Boolean,
    default: true,
  },

  precision: {
    type: Number,
    default: 2,
  },

  cssClass: {
    type: String,
    default: "q-field__input",
  },

  disableNegative: {
    type: Boolean,
    default: false,
  },

  allowBlank: {
    type: Boolean,
    default: false,
  },

  decimal: {
    type: String,
    default: ",",
  },

  thousands: {
    type: String,
    default: ".",
  },

  min: {
    type: Number,
    default: null,
  },

  max: {
    type: Number,
    default: null,
  },
});

const slots = useSlots();

const config = computed(() => ({
  masked: false,
  prefix: props.prefix,
  suffix: "",
  thousands: props.thousands,
  decimal: props.decimal,
  precision: props.precision,
  disableNegative: props.disableNegative,
  disabled: props.disabled,
  min: props.min,
  max: props.max,
  allowBlank: props.allowBlank,
  minimumNumberOfCharacters: 0,
  shouldRound: props.shouldRound,
  focusOnRight: props.focusOnRight,
}));
</script>