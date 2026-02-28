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
  icone: { type: String, required: true },
  titulo:{ type: String, required: true },
  cor:   { type: String, default: 'primary' },
})

defineEmits(['click'])
</script>

<style scoped lang="scss">
.shortcut-btn {
  width: 100%;
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

  &:hover {
    border-color: #ccc;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
  }

  &:active { transform: translateY(0); }

  .shortcut-titulo {
    color: #666;
    font-weight: 500;
    text-align: center;
    line-height: 1.3;
  }

  &.shortcut-primary  { border-color: #667eea20; color: #667eea; &:hover { background: #667eea08; } }
  &.shortcut-info     { border-color: #2196f320; color: #2196f3; &:hover { background: #2196f308; } }
  &.shortcut-warning  { border-color: #ff980020; color: #ff9800; &:hover { background: #ff980008; } }
  &.shortcut-secondary{ border-color: #9c27b020; color: #9c27b0; &:hover { background: #9c27b008; } }
}
</style>
