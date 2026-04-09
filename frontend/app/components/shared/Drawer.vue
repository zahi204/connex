<template>
  <Teleport to="body">
    <Transition name="cx-drawer-transition">
      <div v-if="modelValue" class="cx-drawer-backdrop" @click.self="close">
        <div class="cx-drawer" role="dialog" :aria-label="title">
          <div class="cx-drawer-header">
            <span class="cx-drawer-title">{{ title }}</span>
            <button class="cx-drawer-close" @click="close">✕</button>
          </div>
          <div class="cx-drawer-body">
            <slot />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
defineProps<{ modelValue: boolean; title?: string }>()
const emit = defineEmits(['update:modelValue'])
function close() { emit('update:modelValue', false) }
</script>

<style scoped>
.cx-drawer-transition-enter-active,
.cx-drawer-transition-leave-active { transition: opacity 0.2s ease; }
.cx-drawer-transition-enter-from,
.cx-drawer-transition-leave-to { opacity: 0; }
</style>
