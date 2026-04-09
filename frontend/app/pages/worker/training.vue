<template>
  <div class="portal-page">
    <h1>Training Results</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else-if="training">
      <div class="training-overview">
        <div class="stat-card">
          <div class="stat-label">Overall Score</div>
          <div class="stat-value">{{ training.score ?? 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Classification</div>
          <div class="stat-value">{{ training.classification || 'N/A' }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Suitability</div>
          <span class="suitability-badge" :class="training.suitability">{{ training.suitability || 'N/A' }}</span>
        </div>
      </div>
      <div v-if="training.results && training.results.length" class="results-list">
        <h2>Detailed Results</h2>
        <div v-for="r in training.results" :key="r.id" class="result-card">
          <div class="result-name">{{ r.name || r.type }}</div>
          <div class="result-score">{{ r.score }}</div>
        </div>
      </div>
    </div>
    <div v-else class="empty">No training results available.</div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'worker', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const training = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/worker/training') as any
    training.value = res.data
  } catch (e: any) {
    error.value = e?.data?.message || 'Failed to load training results'
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.portal-page { padding: 1.5rem; }
.portal-page h1 { color: var(--cx-text-primary); font-size: 1.5rem; margin-bottom: 1.5rem; }
.loading { color: var(--cx-text-muted); }
.error-message { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--cx-led-red); padding: 0.75rem; border-radius: 10px; }
.empty { color: var(--cx-text-muted); text-align: center; padding: 2rem; }
.training-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 12px; padding: 1.25rem; }
.stat-label { color: var(--cx-text-muted); font-size: 0.8rem; margin-bottom: 0.5rem; }
.stat-value { color: var(--cx-text-primary); font-size: 1.5rem; font-weight: 700; }
.suitability-badge { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-top: 0.25rem; }
.suitability-badge.suitable { background: rgba(34,197,94,0.2); color: var(--cx-led-green); }
.suitability-badge.conditional { background: rgba(234,179,8,0.2); color: #facc15; }
.suitability-badge.unsuitable { background: rgba(239,68,68,0.2); color: var(--cx-led-red); }
.results-list h2 { color: var(--cx-text-primary); font-size: 1.1rem; margin-bottom: 1rem; }
.result-card { background: var(--cx-bg-card); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.result-name { color: var(--cx-text-secondary); }
.result-score { color: var(--cx-text-primary); font-weight: 700; font-size: 1.1rem; }
</style>
