<template>
  <div class="cx-loading-state">
    <div
      v-for="i in rows"
      :key="i"
      class="cx-skeleton cx-skeleton-line"
      :style="{ width: widths[(i - 1) % widths.length] }"
    />
  </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{ rows?: number }>(), { rows: 3 })
const widths = ['100%', '85%', '70%', '90%', '60%']
</script>

<style scoped>
.cx-loading-state { display: flex; flex-direction: column; gap: 0.6rem; padding: 0.5rem 0; }
</style>
