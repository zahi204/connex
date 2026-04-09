<template>
  <div class="cx-page">
    <h1 class="cx-page-title">{{ $t('directory.title') }}</h1>
    <SharedWorkerDirectorySearch />
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })
</script>
