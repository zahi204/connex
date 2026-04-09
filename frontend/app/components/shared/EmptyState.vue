<template>
  <div class="cx-empty-state">
    <div class="cx-empty-state-icon">{{ icon }}</div>
    <div class="cx-empty-state-title">{{ title }}</div>
    <div v-if="message" class="cx-empty-state-body">{{ message }}</div>
    <button v-if="actionLabel" class="cx-btn cx-btn-primary" style="margin-top:0.5rem;" @click="$emit('action')">
      {{ actionLabel }}
    </button>
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
  icon?: string
  title?: string
  message?: string
  actionLabel?: string
}>(), {
  icon: '📭',
  title: 'Nothing here yet',
})
defineEmits(['action'])
</script>
