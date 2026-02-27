<template>
  <q-field v-bind="$attrs">
    <template v-slot:control="{ id, floatingLabel, modelValue, emitValue }">
      <Money
        :id="id"
        class="q-field__input text-left"
        :model-value="modelValue"
        @update:model-value="emitValue"
        v-bind="config"
        v-show="floatingLabel"
      >
        <template v-for="(_, slot) in slots" :key="slot" v-slot:[slot]="scope">
          <slot :name="slot" v-bind="scope" :key="slot" />
        </template>
      </Money>
    </template>
  </q-field>
</template>

<script>
import { defineComponent, ref, computed, useSlots } from "vue";
import { Money } from "v-money3";

export default defineComponent({
  name: "QMoney",
  components: { Money },

  setup() {
    const slots = useSlots();
    return {
      slots,
      config: {
        masked: false,
        prefix: "R$ ",
        suffix: "",
        thousands: ".",
        decimal: ",",
        precision: 2,
        disableNegative: false,
        disabled: false,
        min: null,
        max: null,
        allowBlank: false,
        minimumNumberOfCharacters: 0,
        shouldRound: true,
        focusOnRight: true,
      },
    };
  },
});
</script>

<style></style>
