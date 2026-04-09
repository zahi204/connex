<template>
  <div class="portal-page">
    <h1>Training Results</h1>
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error-message">{{ error }}</div>
    <div v-else>
      <div v-if="summary" class="training-summary">
        <div class="stat-card">
          <div class="stat-label">Total Evaluated</div>
          <div class="stat-value">{{ summary.total ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Passed</div>
          <div class="stat-value passed">{{ summary.passed ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Failed</div>
          <div class="stat-value failed">{{ summary.failed ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Average Score</div>
          <div class="stat-value">{{ summary.average_score ?? 'N/A' }}</div>
        </div>
      </div>

      <div v-if="!results.length" class="empty">No training results available.</div>
      <div v-else class="results-list">
        <h2>Worker Results</h2>
        <div v-for="r in results" :key="r.id" class="result-card">
          <div class="result-info">
            <div class="result-name">{{ r.worker_name || r.full_name }}</div>
            <div class="result-meta">
              <span v-if="r.classification">{{ r.classification }}</span>
              <span v-if="r.suitability" class="suitability-badge" :class="r.suitability">{{ r.suitability }}</span>
            </div>
          </div>
          <div class="result-score">{{ r.score ?? 'N/A' }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: 'agency', middleware: ['auth'] })

const { apiFetch } = useApi()
const loading = ref(true)
const error = ref('')
const results = ref<any[]>([])
const summary = ref<any>(null)

onMounted(async () => {
  try {
    const res = await apiFetch('/agency/training') as any
    results.value = res.data?.results || res.data || []
    summary.value = res.data?.summary || null
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
.training-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card { background: var(--cx-bg-card); border: 1px solid var(--cx-border); border-radius: 12px; padding: 1rem; text-align: center; }
.stat-label { color: var(--cx-text-muted); font-size: 0.8rem; margin-bottom: 0.5rem; }
.stat-value { color: var(--cx-text-primary); font-size: 1.5rem; font-weight: 700; }
.stat-value.passed { color: var(--cx-led-green); }
.stat-value.failed { color: var(--cx-led-red); }
.results-list h2 { color: var(--cx-text-primary); font-size: 1.1rem; margin-bottom: 0.75rem; }
.result-card { background: var(--cx-bg-card); border-radius: 8px; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
.result-name { color: var(--cx-text-primary); font-weight: 500; margin-bottom: 0.2rem; }
.result-meta { display: flex; gap: 0.75rem; color: var(--cx-text-muted); font-size: 0.8rem; }
.suitability-badge { padding: 0.1rem 0.5rem; border-radius: 12px; font-size: 0.7rem; }
.suitability-badge.suitable { background: rgba(34,197,94,0.2); color: var(--cx-led-green); }
.suitability-badge.conditional { background: rgba(234,179,8,0.2); color: #facc15; }
.suitability-badge.unsuitable { background: rgba(239,68,68,0.2); color: var(--cx-led-red); }
.result-score { color: var(--cx-text-primary); font-weight: 700; font-size: 1.2rem; }
</style>
