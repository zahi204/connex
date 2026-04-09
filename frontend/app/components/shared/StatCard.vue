<template>
  <div class="cx-bento-item">
    <div v-if="led" style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.5rem;">
      <span class="cx-led" :class="led" />
      <span class="cx-mono cx-badge" :class="led.replace('cx-led', 'cx-badge')">{{ value }}</span>
    </div>
    <div v-else class="cx-bento-value">{{ value ?? '—' }}</div>
    <div class="cx-bento-label">{{ label }}</div>
    <div v-if="trend" class="cx-bento-trend" style="font-size:var(--cx-font-xs);color:var(--cx-text-muted);margin-top:0.25rem;">{{ trend }}</div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  label: string
  value?: string | number | null
  trend?: string
  led?: string
}>()
</script>
