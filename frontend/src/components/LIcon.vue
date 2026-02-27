<template>
  <component :is="iconComponent" :size="size" :stroke-width="strokeWidth" :color="color" v-bind="$attrs" />
</template>

<script setup>
import * as LucideIcons from 'lucide-vue-next'
import { computed } from 'vue'

const props = defineProps({
  name:        { type: String, required: true },
  size:        { type: [Number, String], default: 18 },
  strokeWidth: { type: [Number, String], default: 1.75 },
  color:       { type: String, default: 'currentColor' },
})

const toPascal = (str) => str.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('')
const iconComponent = computed(() => LucideIcons[toPascal(props.name)] ?? LucideIcons.HelpCircle)
</script>
