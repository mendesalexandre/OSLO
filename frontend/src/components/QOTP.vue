<template>
  <div class="row q-gutter-sm justify-center">
    <q-input
      v-for="(i, index) in length"
      outlined
      v-model="fieldValues[index]"
      @keyup="onKeyUp($event, i - 1)"
      @update:model-value="onUpdate($event, i - 1)"
      :key="i"
      :ref="(el) => updateFieldRef(el, i - 1)"
      maxlength="1"
      input-class="text-center"
      style="width: 6ch"
    >
      <!-- v-bind="$attrs" -->
    </q-input>
  </div>
  <q-separator class="q-my-sm"></q-separator>

  <p>
    <span class="text-bold">Individual field values: </span>
    <span>{{ JSON.stringify(fieldValues) }}</span>
  </p>

  <p>
    <span class="text-bold">Composite value: </span>
    <span>{{ JSON.stringify(composite) }}</span>
  </p>
</template>

<script>
import { Notify } from "quasar";
import { computed, onBeforeUpdate, ref, watch, watchEffect } from "vue";

export default {
  emits: ["update:modelValue"],
  props: {
    inputLength: {
      type: Number,
      default: 6,
    },
  },
  setup(props, { emit }) {
    // const length = ref(props.inputLength);

    const length = computed(() => props.inputLength);

    const fields = ref([]);
    const fieldValues = ref([]);

    const composite = computed(() => {
      const nonNullFields = fieldValues.value.filter((value) => value);
      if (length.value !== nonNullFields.length) {
        return "";
      }

      return nonNullFields.join("");
    });

    watch(composite, () => {
      if (composite.value) {
        emit("update:modelValue", composite.value);
      }
    });
    // make sure to reset the refs before each update
    onBeforeUpdate(() => {
      fields.value = [];
    });

    const updateFieldRef = (element, index) => {
      if (element) {
        fields.value[index] = element;
      }
    };

    const focus = (index) => {
      if (index >= 0) {
        if (index < length.value) {
          fields.value[index].select();
        } else {
          if (composite.value) {
            fields.value[index - 1].blur();
          }
        }
      }
    };

    const onUpdate = (value, index) => {
      if (value) {
        focus(index + 1);
      }
    };

    const onKeyUp = (evnt, index) => {
      const key = evnt.key;

      if (["Tab", "Shift", "Meta", "Control", "Alt"].includes(key)) {
        return;
      }

      if (["Delete"].includes(key)) {
        return;
      }

      if (key === "ArrowLeft" || key === "Backspace") {
        // SE FOR O PRIMEIRO CAMPO ELE PARA E NÃO RETORNA PARA -1
        if (index === 0) {
          return;
        }

        focus(index - 1);
      }

      if (key === "ArrowRight") {
        // SE FOR O ULTIMO CAMPO ELE PARA DE AVANÇAR
        if (index === length.value - 1) {
          return;
        }

        focus(index + 1);
      }
    };

    return {
      composite,
      fields,
      fieldValues,
      focus,
      length,
      onKeyUp,
      onUpdate,
      updateFieldRef,
    };
  },
};
</script>
