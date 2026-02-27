<template>
  <q-input outlined dense v-model="model" mask="##/##/####">
    <!-- <template v-slot:append>
      <q-icon name="event" class="cursor-pointer"></q-icon>
    </template> -->
    <template v-slot:append>
      <q-icon name="eva-calendar-outline" class="text- cursor-pointer"></q-icon>
    </template>
    <!-- <template v-slot:prepend>
      <q-icon name="eva-calendar-outline" class="cursor-pointer"></q-icon>
    </template> -->
    <q-popup-proxy ref="inputDateRef">
      <q-date
        :locale="locale"
        minimal
        v-model="model"
        mask="DD/MM/YYYY"
        @update:model-value="closeOnSelectDate"
        :model-value="model"
      >
        <!-- <div class="flex justify-end">
          <q-btn flat dense label="Limpar" @click="model = null" />
        </div> -->
      </q-date>
    </q-popup-proxy>
  </q-input>
</template>

<script>
import moment from "moment";
import { ref, computed, defineComponent, onMounted } from "vue";

export default defineComponent({
  props: {
    modelValue: {
      required: false,
      type: [String, Number],
    },
  },
  emits: ["update:modelValue"],
  setup(props, { emit }) {
    const model = computed({
      get: () => {
        if (
          props.modelValue &&
          props.modelValue.length === 10 &&
          moment(props.modelValue, "YYYY-MM-DD").isValid()
        ) {
          return moment(props.modelValue, "YYYY-MM-DD").format("DD/MM/YYYY");
        }
        return props.modelValue;
      },
      set: (value) => {
        if (
          value &&
          value.length === 10 &&
          moment(value, "DD/MM/YYYY").isValid()
        ) {
          const data = moment(value, "DD/MM/YYYY").format("YYYY-MM-DD");
          emit("update:modelValue", data);
          model.value = data;
        } else if (
          value &&
          value.length === 10 &&
          moment(value, "YYYY-MM-DD").isValid()
        ) {
          emit("update:modelValue", value);
        } else {
          emit("update:modelValue", value);
          model.value = null;
        }
        // emit("update:modelValue", value);
      },
    });

    const inputDateRef = ref(null);

    onMounted(() => {
      // model.value = moment().format("DD/MM/YYYY");
    });

    const closeOnSelectDate = () => {
      if (inputDateRef.value) {
        inputDateRef.value.hide();
      }
    };

    return {
      model,
      inputDateRef,
      closeOnSelectDate,
      locale: {
        /* starting with Sunday */
        days: "Domingo_Segunda_Terça_Quarta_Quinta_Sexta_Sábado".split("_"),
        daysShort: "Dom_Seg_Ter_Quar_Qui_Sex_Sáb".split("_"),
        months:
          "Janeiro_Fevereiro_Março_Abril_Maio_Junho_Julho_Agosto_Setembro_Outubro_Novembro_Dezembro".split(
            "_"
          ),
        monthsShort: "Jan_Fev_Mar_Abr_Mai_Jun_Jul_Ago_Set_Out_Nov_Dez".split(
          "_"
        ),
        firstDayOfWeek: 0, // 0-6, 0 - Sunday, 1 Monday, ...
        format24h: true,
        pluralDay: "dias",
      },
    };
  },
});
</script>
